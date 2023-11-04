<template>
    <h1 class="text-primary-3 font-poppins font-semibold text-18xl mb-4">
        Store-Besuche
    </h1>
    <div class="challo__card challo__card--transparent">
        <div class="challo__card--header mt-6">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <SearchInPage placeholder="Nach Store-Namen, Besuchervor- oder nachnamen suchen"
                        routeName="store-visits.index" />
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div class="challo__filter flex-1" :class="[option.showFilter ? 'bg-white' : '']">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" class="!mr-0"/>

                    <div class="filters flex justify-around" v-show="option.showFilter">

                        <div class="filter-menu">
                            <SingleCheckboxFilter label="Besucher" column="mobile_app_users" labelKey="name"
                                valueKey="value" :options="filterData.mobile_app_users?.data"
                                routeName="store-visits.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_users')"
                                @instant-update="(key, silently = false) => getFilterOptions(key, silently)"
                                :active="option.current_open_filter_menu == 'mobile_app_users'"
                                @toggle="toggleCurrentOpenFilterMenu('mobile_app_users')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter label="Store" column="sales_partners" labelKey="name" valueKey="value"
                                :options="filterData.sales_partners?.data" routeName="store-visits.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('sales_partners')"
                                @instant-update="(key) => getFilterOptions(key)"
                                :active="option.current_open_filter_menu == 'sales_partners'"
                                @toggle="toggleCurrentOpenFilterMenu('sales_partners')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu">
                            <DateRangeFilter routeName='store-visits.index' label="Datum" />
                        </div>
                    </div>
                </div>
                <EntriesPerPage target="store-visits.index" />
            </div>
        </div>
        <template v-if="store_visits.data.length > 0">
            <Table>
                <template #thead>
                    <div class="td flex items-center gap-[10px] w-[20%]">
                        <span>Besucher</span>
                        <Sort key-name="mobile_app_user_id" route-name="store-visits.index" />
                    </div>
                    <div class="td flex items-center gap-[10px] w-[20%]">
                        <span>Store</span>
                        <Sort key-name="sales_partner_id" route-name="store-visits.index" />
                    </div>
                    <div class="td flex items-center gap-[10px] w-[20%]">
                        <span>Check-in Zeitpunkt</span>
                        <Sort key-name="check_in_time" route-name="store-visits.index" />
                    </div>
                    <div class="td flex items-center gap-[10px] w-[20%]">
                        <span>Check-out Zeitpunkt</span>
                        <Sort key-name="check_out_time" route-name="store-visits.index" />
                    </div>
                    <div class="td flex items-center gap-[10px] w-[15%]">
                        <span>Umsatz</span>
                        <Sort key-name="turnover" route-name="store-visits.index" />
                    </div>
                    <div class="td w-[5%]">
                    </div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="store_visit in store_visits.data" :key="store_visit.id"
                        :store_visit="store_visit" />
                </template>
            </Table>
            <pagination :paginationData="store_visits" />
        </template>
        <EmptyPage v-if="store_visits.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from '@vue/reactivity';
import Table from '../../Components/Tables/Table.vue';
import TableBodyRow from './Components/TableBodyRow.vue';
import Sort from '../../Components/Sort.vue';
import SearchInPage from '../../Components/SearchInPage.vue';
import DateRangeFilter from '../../Components/Filters/DateRangeFilter.vue';
import FilterButton from '../../Components/Filters/FilterButton.vue';
import Pagination from '../../Components/Pagination.vue';
import EntriesPerPage from '../../Components/EntriesPerPage.vue';
import EmptyPage from '../../Components/EmptyPage.vue';
import MultiCheckboxFilter from '../../Components/Filters/MultiCheckboxFilter.vue';
import DateFilter from './Components/DateFilter.vue';
import SingleCheckboxFilter from '../../Components/Filters/SingleCheckboxFilter.vue';
import axios from 'axios';

const props = defineProps({
    store_visits: {
        type: Object,
        required: true,
    }
})


const filterData = reactive({
    mobile_app_users: {
        data: [],
    },
    sales_partners: {
        data: [],
    }
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu, silently = false) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    searchParams.column = menu;

    axios.get(route("store-visits.filter.data", { ...searchParams }))
        .then((res) => {
            filterData[menu]["data"] = res.data;
        });

    if (silently == true)
        return;
    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu == menu) {
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
    if (option.current_open_filter_menu == e) {
        option.current_open_filter_menu = null;
    }
};
</script>
