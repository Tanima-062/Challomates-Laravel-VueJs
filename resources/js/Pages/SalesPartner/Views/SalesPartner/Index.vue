<template>
    <div class="mb-8 -mt-8">
        <h3 class="font-poppins text-18xl text-primary-3 font-semibold">
            Vertriebspartner und Verträge
        </h3>
    </div>
    <Tabs />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-[26px]">
                <div
                    class="relative group placeholder-overflow-search"
                    :class="[hasPermission('sales_partner.create') ? 'w-3/6' : 'w-full']"
                >
                    <search-in-page
                        :class="{
                          'hover:placeholder:text-transparent': hasPermission('sales_partner.create'),
                        }"
                        :placeholder="
                          hasPermission('sales_partner.create')
                            ? 'Nach Vertriebspartner-ID, Firmennamen, Vor- oder Nach...'
                            : 'Nach Vertriebspartner-ID, Firmennamen, Vor- oder Nachnamen der Kontaktperson suchen'
                        "
                        routeName="sales-partner.index"
                        @update="
                          (e) =>
                            hasPermission('sales_partner.create') ? show_full_placeholder(e) : null
                        "
                    />
                    <p
                        v-if="hasPermission('sales_partner.create')"
                        class="placeholder invisible group-hover:visible absolute min-w-[550px] flex top-[30%] left-[10%] pointer-events-none text-16 font-ropa text-secondary-3 font-normal"
                        id="placeholder-overflow"
                    >
                        Nach Vertriebspartner-ID, Firmennamen, Vor- oder Nachnamen der Kontaktperson
                        suchen
                    </p>
                </div>
                <div
                    class="w-4/5 flex gap-[26px]"
                    v-if="hasPermission('sales_partner.create')"
                >
                    <button
                        class="challo__btn btn-block"
                        :class="[
                          selected_sales_partners.length
                            ? 'bg-primary-1'
                            : 'bg-gray-corner cursor-not-allowed',
                        ]"
                        @click="
                          selected_sales_partners.length ? (showAssignConsultantModal = true) : null
                        "
                    >
                        Company Consultant zuteilen
                    </button>
                    <Link
                        :href="route('sales-partner.create')"
                        class="challo__btn btn-success btn-block"
                    >
                    Neuer Vertriebspartner hinzufügen
                    </Link>
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div
                    class="challo__filter flex-1"
                    :class="{ 'bg-white': option.showFilter }"
                >
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                    <div
                        class="filters flex justify-around"
                        v-show="option.showFilter"
                    >
                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <DateRangeFilter routeName="sales-partner.index" />
                            </div>
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Company Consultant"
                                column="company_consultant"
                                labelKey="name"
                                valueKey="id"
                                :options="filterData.company_consultant.data"
                                routeName="sales-partner.index"
                                :active="option.current_open_filter_menu == 'company_consultant'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('company_consultant')"
                                optionClass="min-w-[250px]"
                            />
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Branche"
                                column="branches"
                                labelKey="name"
                                valueKey="id"
                                :options="filterData.branches.data"
                                routeName="sales-partner.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('branches')"
                                @instant-update="(key) => getFilterOptions(key)"
                                :active="option.current_open_filter_menu == 'branches'"
                                @toggle="toggleCurrentOpenFilterMenu('branches')"
                                optionClass="min-w-[250px]"
                            />
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Branchenkategorie"
                                column="branch_catagories"
                                labelKey="name"
                                valueKey="id"
                                :options="filterData.branch_catagories.data"
                                routeName="sales-partner.index"
                                :active="option.current_open_filter_menu == 'branch_catagories'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('branch_catagories')"
                                optionClass="min-w-[250px]"
                            />
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Ort"
                                column="cities"
                                labelKey="name"
                                valueKey="id"
                                :options="filterData.cities.data"
                                routeName="sales-partner.index"
                                :active="option.current_open_filter_menu == 'cities'"
                                v-click-away="() => closeCurrentOptionFilterMenu('cities')"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('cities')"
                                optionClass="min-w-[250px]"
                            />
                        </div>

                        <div class="filter-menu">
                            <div class="filter-menu__label">

                                <MultiSelectFilterUserStatusVue
                                    @toggleMenu="toggleCurrentOpenFilterMenu('status')"
                                    :opend="option.current_open_filter_menu == 'status'"
                                    valueKey="value"
                                    columnKey="status"
                                    label-key="name"
                                    :options="filterData.status.data"
                                    routeName="sales-partner.index"
                                    @closeMenu="closeCurrentOptionFilterMenu('status')"
                                    @instant-update="getFilterOptions('status')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <EntriesPerPage target="sales-partner.index" />
            </div>
        </div>
        <template v-if="sales_partners.data.length > 0">
            <Table>
                <template #thead>
                    <div
                        class="td w-[5%] flex items-center"
                        v-if="hasPermission('sales_partner.edit')"
                    >
                        <div
                            tabindex="0"
                            class="rounded-[4px] min-w-[49px] h-8 flex items-center justify-start p-2 ml-[-9px] gap-2 relative"
                            :class="{ 'border-gray-corner border-[1.5px]': select_all_dropdown_opened }"
                            v-click-away="() => (select_all_dropdown_opened = false)"
                            @click="select_all_dropdown_opened = !select_all_dropdown_opened"
                        >
                            <CheckBox
                                @click.stop="() => sales_partners.data.length == selected_sales_partners.length ? deselectAll() : selectAll()"
                                :value="1"
                                :selected="sales_partners.data.length == selected_sales_partners.length"
                            />
                            <div class="toggle-icons">
                                <DownArrow v-if="!select_all_dropdown_opened" />
                                <UpArrow v-if="select_all_dropdown_opened" />
                            </div>

                            <div
                                class="content w-[150px] divide-y divide-tints-1 dev absolute flex flex-col bg-white top-[110%] left-0 rounded-[8px]"
                                v-if="select_all_dropdown_opened"
                                @click.stop="select_all_dropdown_opened = false"
                            >
                                <p
                                    @click="selectAll"
                                    class="hover:font-semibold px-4 pb-[10px] pt-3 text-tints-5 font-normal text-13 cursor-pointer break-normal"
                                    :class="{'cursor-not-allowed': selected_sales_partners.length == sales_partners.data.length}"
                                >
                                    Alle auswählen
                                </p>
                                <p
                                    @click="deselectAll"
                                    class="hover:font-semibold px-4 pb-[10px] pt-3 text-tints-5 font-normal text-13 cursor-pointer break-normal"
                                    :class="{'cursor-not-allowed': selected_sales_partners.length == 0}"
                                >
                                    Keine auswählen
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="td flex justify-start items-center gap-2 w-[15%]">
                        <span>Erstellungsdatum</span>
                        <Sort
                            keyName="created_at"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div class="td flex justify-start items-center gap-2 w-[15%]">
                        <span>Firmenname</span>
                        <Sort
                            keyName="company_name"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div class="td flex justify-start items-center gap-2 w-[18%]">
                        <span>Vertragslaufzeit</span>
                        <Sort
                            keyName="contract_term_period"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div class="td flex justify-start items-center gap-2 w-[15%]">
                        <span>Branchenkategorie</span>
                        <Sort
                            keyName="branch_category"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div
                        class="td flex justify-start items-center gap-2"
                        :class="[hasPermission('sales_partner.edit') ? 'w-[17%]' : 'w-[20%]']"
                    >
                        Company Consultant
                        <Sort
                            keyName="company_consultant"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div class="td flex justify-start items-center gap-2 w-[10%]">
                        <span>Status</span>
                        <Sort
                            keyName="status"
                            route-name="sales-partner.index"
                        />
                    </div>
                    <div class="td w-[5%]"></div>
                </template>
                <template #tbody>
                    <SalesPartnerTableRow
                        v-for="sales_partner in sales_partners.data"
                        :sales_partner="sales_partner"
                        :key="sales_partner.id"
                        @toggleSelected="(value) => toggleSelectedPartner(value)"
                        :selected="
                          selected_sales_partners.find((item) => item == sales_partner.id)
                            ? true
                            : false
                        "
                    />
                </template>
            </Table>
            <pagination :paginationData="sales_partners" />
        </template>
        <EmptyPage v-if="sales_partners.data.length < 1" />
    </div>

    <AssignConsultantToSalesPartner
        v-if="showAssignConsultantModal"
        class="fixed top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%]"
        :company_consultants="company_consultants"
        @assign="assign"
        @cancel="cancel"
    />
</template>

<script setup>
import { reactive, ref } from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import SearchInPage from "../../../../Components/SearchInPage.vue";
import FilterButton from "../../../../Components/Filters/FilterButton.vue";
import Tabs from "../../Components/Tabs.vue";
import Table from "../../../../Components/Tables/Table.vue";
import EmptyPage from "../../../../Components/EmptyPage.vue";
import Pagination from "../../../../Components/Pagination.vue";
import SalesPartnerTableRow from "../../Components/SalesPartnerTableRow.vue";
import Sort from "../../../../Components/Sort.vue";
import DateRangeFilter from "../../../../Components/Filters/DateRangeFilter.vue";
import axios from "axios";
import MultiCheckboxFilter from "../../../../Components/Filters/MultiCheckboxFilter.vue";
import DownArrow from "../../../../Components/Icons/DownArrow.vue";
import UpArrow from "../../../../Components/Icons/UpArrow.vue";
import CheckBox from "../../../../Components/Form/CheckBox.vue";
import { inject, watch } from "@vue/runtime-core";
import AssignConsultantToSalesPartner from "../../../../Components/Modal/AssignConsultantToSalesPartner.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../../../../Components/Modal/Content/Confirmation.vue";
import EntriesPerPage from "../../../../Components/EntriesPerPage.vue";
import MultiSelectFilterUserStatusVue from "../../../../Components/Filters/MultiSelectFilterUserStatus.vue";
import { nextTick } from "vue";
const props = defineProps({
    sales_partners: {
        type: Object,
        required: true,
    },

    company_consultants: {
        type: Object,
        required: true,
    },

    select_all: {
        type: Boolean,
        default: false,
    },
});

const selected_sales_partners = ref([]);
const select_all_dropdown_opened = ref(false);
const select_all = ref(false);
const show_full_placeholder = (e) => {
    const placeholder = document.getElementById("placeholder-overflow");
    if (e.length > 0) {
        placeholder?.classList.remove("group-hover:visible");
        return;
    }

    !placeholder?.classList.contains("group-hover:visible")
        ? placeholder?.classList.add("group-hover:visible")
        : null;
};

const toggleSelectedPartner = (value) => {
    const index = selected_sales_partners.value.indexOf(value);
    index !== -1
        ? selected_sales_partners.value.splice(index, 1)
        : selected_sales_partners.value.push(value);
};

Inertia.on("navigate", (event) => {
    if (event.detail.page.component == "SalesPartner/Views/SalesPartner/Index") {
        select_all.value = false;
        selected_sales_partners.value = [];
    }
});

const selectAll = () => {
    selected_sales_partners.value = props.sales_partners.data.map((item) => item.id);
}
const deselectAll = () => {
    selected_sales_partners.value = [];
}

const showAssignConsultantModal = ref(false);
const modal = inject("modal");

const assign = (value) => {
    showAssignConsultantModal.value = false;
    modal.show(Confirmation, {
        props: {
            message: "Selektierte Vertriebspartner zuteilen",
            text:
                "Sind Sie sicher, dass Sie die von Ihnen selektierten Vertriebspartner dem ausgewählten Company Consultant zuteilen wollen? ",
        },
        events: {
            yesClick: () => {
                Inertia.put(route("sales-partner.assign.consultant"), {
                    consultant: value,
                    sales_partners: selected_sales_partners.value,
                }, {
                    onSuccess: () => {
                        selected_sales_partners.value = []
                        select_all.value = false
                    }
                });
            },
            noClick: () => modal.hide(),
        },
    });
};

const cancel = () => {
    modal.show(Confirmation, {
        props: {
            message: "Company Consultant Zuteilung abbrechen?",
            text:
                "Wenn Sie diesen Vorgang abbrechen, werden alle Daten verworfen. Sind Sie sicher, dass Sie diese Company Consultant Zuteilung wirklich abbrechen wollen?",
        },
        events: {
            yesClick: () => {
                showAssignConsultantModal.value = false;
                modal.hide();
            },
            noClick: () => modal.hide(),
        },
    });
};

const filterData = reactive({
    status: {
        loaded: false,
        data: [],
    },

    company_consultant: {
        data: [],
    },

    branches: {
        data: [],
    },

    branch_catagories: {
        data: [],
    },

    cities: {
        data: [],
    },
});

const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));

    searchParams.column = menu;

    axios.get(route("sales-partner.filter.data", { ...searchParams })).then((res) => {
        filterData[menu]["data"] = res.data;
        option.current_open_filter_menu = menu;
    });

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu == menu) {
        option.current_open_filter_menu = null;
    } else {
        if (filterData.hasOwnProperty(menu) && !filterData[menu]["loaded"]) {
            getFilterOptions(menu);
        } else {
            option.current_open_filter_menu = menu;
        }
    }
};

const closeCurrentOptionFilterMenu = (e) => {
    if (option.current_open_filter_menu == e) {
        option.current_open_filter_menu = null;
    }
};
</script>
