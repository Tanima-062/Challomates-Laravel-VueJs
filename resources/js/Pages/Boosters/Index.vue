<template>
    <div class="page-title mb-6">
        <span>Boosters</span>
    </div>
    <div class="challo__card challo__card--transparent">
        <div class="challo__card--header mt-5">
            <div class="flex justify-between items-center gap-6">
                <div :class="hasPermission('booster.create') ? 'w-3/5' : 'w-full'">
                    <search-in-page
                        :placeholder="$t('Nach Vertriebspartnerfirmennamen, Booster-ID oder -titel suchen')"
                        routeName="boosters.index"
                        class=""
                     />
                </div>
                <div
                    class="w-2/5"
                    v-if="hasPermission('booster.create')">
                    <Link
                        :href="route('boosters.create')"
                        class="challo__btn btn-success  btn-block"
                    >
                        {{ $t("Neuer Booster hinzufügen") }}
                    </Link>
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">

                <div class="challo__filter flex-1" :class="[option.showFilter ? 'bg-white' : '']">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                    <div class="filters w-4/5" v-show="option.showFilter">
                        <div class="filter-menu w-1/5">
                            <div class="filter-menu__label">
                                <DateRangeFilter columnKey="created_at" routeName="boosters.index" />
                            </div>
                        </div>
                        <div class="filter-menu w-1/5">
                            <div class="filter-menu__label">
                                <span v-if="option.current_open_filter_menu == 'sales_partner'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sales_partner')">
                                    Vertriebspartner
                                    <up-arrow />
                                </span>
                                <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sales_partner')">
                                    Vertriebspartner
                                    <down-arrow />
                                </span>
                                <FilterOptionStatus
                                    v-if="option.current_open_filter_menu == 'sales_partner'"
                                    valueKey="value"
                                    columnKey="sales_partner"
                                    :options="filterData.sales_partner.data"
                                    routeName="boosters.index"
                                    v-click-away="() => closeCurrentOptionFilterMenu('sales_partner')"
                                />
                            </div>
                        </div>
                        <div class="filter-menu w-1/5">
                            <div class="filter-menu__label">

                                <FilterOption
                                    @toggleMenu="toggleCurrentOpenFilterMenu('booster_type')"
                                    title="Boosterart"
                                    value="value"
                                    name="name"
                                    key_prefix="booster_type"
                                    :opend = "option.current_open_filter_menu == 'booster_type'"
                                    :options="filterData.booster_type.data"
                                    routeName="boosters.index"
                                    @closeMenu="closeCurrentOptionFilterMenu('booster_type')"
                                />

                            </div>
                        </div>
                        <div class="filter-menu w-1/5">
                            <div class="filter-menu__label">
                                <DateRangeFilter columnKey="start" routeName="boosters.index" label="Laufzeit" keyPrefix="runtime"/>
                            </div>
                        </div>

                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <span  v-if="option.current_open_filter_menu == 'status'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('status')">
                                    Status
                                    <up-arrow />
                                </span>
                                <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('status')">
                                    Status
                                    <down-arrow />
                                </span>

                                <FilterOptionStatus
                                    v-if="option.current_open_filter_menu == 'status'"
                                    valueKey="value"
                                    columnKey="status"
                                    :options="filterData.status.data"
                                    routeName="boosters.index"
                                    v-click-away="() => closeCurrentOptionFilterMenu('status')"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="table-filter" :class="{ active: option.showFilter }" ></div>
                </div>

                <EntriesPerPage target="boosters.index"/>
            </div>
        </div>
        <template v-if="boosters.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 15%;">
                        {{ $t("Erstellungsdatum") }}
                        <Sort routeName="boosters.index" keyName="created_at" />
                    </div>
                    <div class="th" style="width: 25%">
                        {{ $t("Vertriebspartner") }}
                        <Sort routeName="boosters.index" keyName="sales_partner_id" />
                    </div>
                    <div class="th" style="width: 17%">
                        {{ $t("Titel") }}
                        <Sort routeName="boosters.index" keyName="title" />
                    </div>
                    <div class="th" style="width: 13%">
                        {{ $t("Boosterart") }}
                        <Sort routeName="boosters.index" keyName="type" />
                    </div>
                    <div class="th" style="width: 20%">
                        {{ $t("Laufzeit") }}
                        <Sort routeName="boosters.index" keyName="start" />
                    </div>
                    <div class="th" style="width: 10%">
                        {{ $t("Status") }}
                        <Sort routeName="boosters.index" keyName="status" />
                    </div>
                    <div class="th" style="width: 5%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="booster in boosters.data" :key="booster.id" :booster="booster" />
                </template>
            </Table>
            <pagination :paginationData="boosters" />
        </template>
        <EmptyPage v-if="boosters.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive} from "@vue/reactivity";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import FilterOption from "./Components/FilterForBooster.vue";
import FilterOptionStatus from "./Components/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import { trans } from "laravel-vue-i18n";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from '../../Components/Tables/Table.vue';
import DateRangeFilter from '../../Components/Filters/DateRangeFilter.vue'
import RunTimeFilter from '../../Components/Filters/RunTimeFilter.vue'
import EntriesPerPage from "../../Components/EntriesPerPage.vue";
import axios from 'axios'

const props = defineProps({
    boosters: {
        type: Object,
        required: true,
    },
    sales_partners:{
        type: Array,
        required: false
    }
});

const filterData = reactive({
    booster_type: {
        loaded: false,
        data: [
            {
                name: "Einmalig",
                value: "One Time",
            },
            {
                name: "Wiederkehrend",
                value: "Recurring",
            }
        ],
    },
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
            },
            {
                name: 'Neu',
                value: 'new'
            },
            {
                name: 'Abgelaufen',
                value: 'expired'
            },
            {
                name: 'Posted',
                value: 'posted'
            }
        ]
    },
    sales_partner: {
        loaded: false,
        data: props.sales_partners
    }
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu) => {
    let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
    );

    searchParams.column = menu;

    axios.get(route('boosters.filter-data', { ...searchParams })).then(res => {
        filterData[menu]['data'] = res.data;
    });

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu == menu) {
        option.current_open_filter_menu = null
    } else {
        if ( filterData.hasOwnProperty(menu) && !filterData[menu]['loaded'] ) {
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
