<template>
    <div class="page-title mb-6">
        <span>Pakete</span>
    </div>
    <div class="challo__card challo__card--transparent">
        <div class="challo__card--header mt-5">
            <div class="flex justify-between items-center gap-6">
                <div :class="hasPermission('package.create') ? 'w-3/5' : 'w-full'">
                    <search-in-page
                        placeholder="Nach Paketname suchen"
                        routeName="package.index"
                        class=""
                    />
                </div>
                <div
                    class="w-2/5"
                    v-if="hasPermission('package.create')"
                >
                    <Link
                        :href="route('package.create')"
                        class="challo__btn btn-success  btn-block"
                    >
                    Neues Paket hinzufügen
                    </Link>
                </div>
            </div>

            <div class="flex justify-between items-center gap-[18px]">
                <div class="w-[85%]">
                    <div
                        class="challo__filter"
                        :class="[option.showFilter ? 'bg-white' : '']"
                    >
                        <FilterButton @toggleShowFilter="value => (option.showFilter = value)" />

                        <div
                            class="filters "
                            v-show="option.showFilter"
                        >
                            <div class="filter-menu w-1/3">
                                <div class="filter-menu__label">
                                    <DateRangeFilter routeName="package.index" />
                                </div>
                            </div>

                            <div class="filter-menu w-1/3">
                                <div class="filter-menu__label">
                                    <PriceRangeFilter
                                        columnKey="package_fee"
                                        :options="priceRanges"
                                        valueKey="value"
                                        name="name"
                                        :maxValue="maxValue"
                                        :minValue="minValue"
                                        routeName="package.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-1/3">
                                <div class="filter-menu__label">
                                    <span
                                        v-if="option.current_open_filter_menu == 'status'"
                                        class="filter-menu__label--title"
                                        @click="toggleCurrentOpenFilterMenu('status')"
                                    >
                                        Status
                                        <up-arrow />
                                    </span>
                                    <span
                                        v-else
                                        class="filter-menu__label--title"
                                        @click="toggleCurrentOpenFilterMenu('status')"
                                    >
                                        Status
                                        <down-arrow />
                                    </span>

                                    <FilterOptionStatus
                                        v-if="option.current_open_filter_menu === 'status'"
                                        valueKey="value"
                                        columnKey="status"
                                        :options="filterData.status.data"
                                        routeName="package.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('status')"
                                    />
                                </div>
                            </div>
                        </div>
                        <div
                            class="table-filter"
                            :class="{ active: option.showFilter }"
                        ></div>
                    </div>
                </div>
                <div class="">
                    <div class="filter-menu__label text-right">
                        <span
                            v-if="option.current_open_filter_menu == 'per_page'"
                            class="text-tints-5 filter-menu__label--title !font-poppins"
                            @click="toggleCurrentOpenFilterMenu('per_page')"
                        >
                            Einträge pro Seite
                            <up-arrow />
                        </span>
                        <span
                            v-else
                            class="text-tints-5 filter-menu__label--title !font-poppins"
                            @click="toggleCurrentOpenFilterMenu('per_page')"
                        >
                            Einträge pro Seite
                            <down-arrow />
                        </span>

                        <PerPageOption
                            v-if="option.current_open_filter_menu === 'per_page'"
                            valueKey="value"
                            columnKey="per_page"
                            routeName="package.index"
                            @closePerPageOption="closeCurrentOptionFilterMenu('per_page')"
                            v-click-away="() => closeCurrentOptionFilterMenu('per_page')"
                        />
                    </div>
                </div>
            </div>

        </div>

        <template v-if="packages.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th w-[5%]">

                        <div
                            tabindex="0"
                            class="rounded-[4px] p-2 flex items-center justify-center ml-[-9px] relative"
                            :class="{ 'border-gray-corner border-[1.5px]': show_all_dropdown_opened }"
                            v-click-away="() => show_all_dropdown_opened = false"
                            @click="show_all_dropdown_opened = !show_all_dropdown_opened"
                        >

                            <CheckBox
                                @click.stop="toggleShowAll()"
                                :value="1"
                                :selected="(selected_packages.length == packages.data.length)"
                            />
                            <div class="toggle-icons">
                                <DownArrow v-if="!show_all_dropdown_opened" />
                                <UpArrow v-if="show_all_dropdown_opened" />
                            </div>

                            <div
                                class="content w-[150px] divide-y divide-tints-1 dev absolute flex flex-col bg-white top-[110%] left-0"
                                v-if="show_all_dropdown_opened"
                                @click.stop="show_all_dropdown_opened = false"
                            >
                                <p
                                    class="hover:font-semibold pl-4 pr-[18px] pb-3 pt-3 text-tints-5 font-normal text-13 cursor-pointer"
                                    @click="visit_miltiple_details_page()"
                                    :class="[!selected_packages.length ? 'cursor-not-allowed' : '']"
                                >
                                    Details ansehen
                                </p>
                                <p
                                    @click="toggleShowAll"
                                    class="hover:font-semibold pl-4 pr-[18px] pb-[10px] pt-3 text-tints-5 font-normal text-13 cursor-pointer"
                                >
                                    <template v-if="(selected_packages.length == packages.data.length)">
                                        Keine auswählen
                                    </template>
                                    <template v-else>
                                        Alle auswählen
                                    </template>
                                </p>
                                <!-- <p
                                    @click="toggleShowAll"
                                    v-if="show_all"
                                    class="hover:font-semibold pl-4 pr-[18px] pb-[10px] pt-3 text-tints-5 font-normal text-13 cursor-pointer"
                                >
                                </p> -->

                            </div>

                        </div>
                    </div>
                    <div
                        class="th"
                        style="width: 15%;"
                    >
                        Erstellungsdatum
                        <Sort
                            routeName="package.index"
                            keyName="created_at"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 15%"
                    >
                        Name
                        <Sort
                            routeName="package.index"
                            keyName="package_name"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 10%"
                    >
                        Preis
                        <Sort
                            routeName="package.index"
                            keyName="package_fee"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 20%"
                    >
                        Booster
                        <Sort
                            routeName="package.index"
                            keyName="booster"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 20%"
                    >
                        Anzahl Registrationen
                        <Sort
                            routeName="package.index"
                            keyName="number_of_registration"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 10%"
                    >
                        Status
                        <Sort
                            routeName="package.index"
                            keyName="status"
                        />
                    </div>
                    <div
                        class="th"
                        style="width: 5%"
                    ></div>
                </template>
                <template #tbody>
                    <TableBodyRow
                        v-for="(pkg, index) in packages.data"
                        :key="index"
                        :pkg="pkg"
                        :selected="selected_packages.includes(pkg.id)"
                        :set_selected_package="set_selected_package"
                    />
                </template>
            </Table>
            <pagination :paginationData="packages" />
        </template>
        <EmptyPage v-if="packages.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive, ref } from '@vue/reactivity'
import SearchInPage from '../../Components/SearchInPage.vue'
import { Link } from '@inertiajs/inertia-vue3'
import DownArrow from '../../Components/Icons/DownArrow.vue'
import UpArrow from '../../Components/Icons/UpArrow.vue'
import DateRangeFilter from '../../Components/Filters/DateRangeFilter.vue'
import FilterOptionStatus from './Components/FilterOption.vue'
import PerPageOption from '../../Components/Filters/PerPageOption'
import FilterButton from '../../Components/Filters/FilterButton.vue'
import Sort from '../../Components/Sort.vue'
import TableBodyRow from './Components/TableBodyRow.vue'
import EmptyPage from '../../Components/EmptyPage.vue'
import Pagination from '../../Components/Pagination.vue'
import Table from '../../Components/Tables/Table.vue'
import PriceRangeFilter from "./Components/PriceRangeFilter";
import axios from 'axios'
import CheckBox from '../../Components/Form/CheckBox.vue'
import { computed, watch, nextTick } from '@vue/runtime-core'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
    packages: {
        type: Object,
        required: true
    },
    priceRanges: {
        type: Object,
        required: false,
    },
    minValue: {
        type: Number,
        required: false,
    },
    maxValue: {
        type: Number,
        required: false,
    },
})

const filterData = reactive({
    status: {
        loaded: false,
        data: [
            {
                name: 'Aktiv',
                value: 'active'
            },
            {
                name: 'Inaktiv',
                value: 'inactive'
            }
        ]
    },

    package_fee: {
        loaded: false,
        data: [
            {
                name: "0-100",
                value: "0-100"
            },
            {
                name: "101-500",
                value: "100-500"
            },
            {
                name: "501-1000",
                value: "500-1000"
            },
            {
                name: "1001-2000",
                value: "1000-2000"
            },
            {
                name: "other preis",
                value: "other_preis"
            }
        ]
    }
});

const show_all_dropdown_opened = ref(false)

const option = reactive({
    showFilter: false,
    current_open_filter_menu: null
})

const selected_packages = ref([])

const set_selected_package = package_id => {
    const index = selected_packages.value.indexOf(package_id)
    selected_packages.value.includes(package_id)
        ? selected_packages.value.splice(index, 1)
        : selected_packages.value.push(package_id)
}

const toggleShowAll = () => {
    if (selected_packages.value.length == props.packages.data.length) {
        selected_packages.value = []
    } else {
        selected_packages.value = props.packages.data.map(item => item.id);
    }
}

Inertia.on("navigate", (event) => {
    if (event.detail.page.component == "Package/Index") {
        selected_packages.value = [];
    }
});

const packages_id = computed(() => {
    return props.packages.data.map(item => item.id)
})

const visit_miltiple_details_page = () => {
    if (selected_packages.value.length) {
        Inertia.visit(route('package.show.multiple', {
            packages: selected_packages.value,
            ...buildQueryParams()
        }))
    }
}


const getFilterOptions = menu => {
    let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
    );

    searchParams.column = menu;

    axios.get(route('package.filter-data', { ...searchParams })).then(res => {
        filterData[menu]['data'] = res.data;
    });

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu === menu) {
        option.current_open_filter_menu = null
    } else {
        if (filterData.hasOwnProperty(menu) && !filterData[menu]['loaded']) {
            getFilterOptions(menu);
        } else {
            option.current_open_filter_menu = menu;
        }
    }
};

const closeCurrentOptionFilterMenu = (e) => {
    if (option.current_open_filter_menu === e) {
        option.current_open_filter_menu = null;
    }
};


const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};
</script>

<style lang="scss" scoped>
.filter-menu__label--title {
    &.text-tints-5 {
        color: #135F84;
    }
}
</style>
