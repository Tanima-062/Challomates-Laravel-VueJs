<template>
    <h3 class="font-poppins text-18xl text-primary-3 font-semibold mb-4">Coins</h3>

    <Tabs />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page placeholder="Nach Gewinnspiel-Namen, Mobile App-Benutzer Vor- oder Nachnamen suchen"
                        routeName="coins-debit.index" />
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div class="challo__filter flex-1" :class="{ 'bg-white': option.showFilter }">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                    <div class="filters flex justify-around" v-show="option.showFilter">

                        <div class="filter-menu mr-auto">
                            <DateRangeFilter routeName='coins-debit.index' label="Zeitpunkt" />
                        </div>

                        <div class="filter-menu mr-auto">
                            <SingleCheckboxFilter label="Mobile App-Benutzer" column="mobile_app_users" labelKey="name"
                                valueKey="value" :options="filterData.mobile_app_users?.data"
                                routeName="coins-debit.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_users')"
                                @instant-update="(key, silently = false) => getFilterOptions(key, silently)"
                                :active="option.current_open_filter_menu == 'mobile_app_users'"
                                @toggle="toggleCurrentOpenFilterMenu('mobile_app_users')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu mr-auto">
                            <MultiCheckboxFilter label="Gewinnspiel/e" column="sweepstakes" labelKey="name"
                                valueKey="value" :options="filterData.sweepstakes?.data" routeName="coins-debit.index"
                                v-click-away="() => closeCurrentOptionFilterMenu('sweepstakes')"
                                @instant-update="(key) => getFilterOptions(key)"
                                :active="option.current_open_filter_menu == 'sweepstakes'"
                                @toggle="toggleCurrentOpenFilterMenu('sweepstakes')" optionClass="min-w-[250px]" />
                        </div>


                    </div>
                </div>
                <EntriesPerPage :target="'coins-debit.index'" />
            </div>
        </div>
        <template v-if="true">
            <Table>
                <template #thead>
                    <div class="td flex items-center gap-2 w-[20%]">
                        <span>Zeitpunkt</span>
                        <Sort routeName="coins-debit.index" keyName="created_at" />
                    </div>
                    <div class="td flex items-center gap-2 w-[20%]">
                        <span>Mobile App-Benutzer</span>
                        <Sort routeName="coins-debit.index" keyName="mobile_app_user" />
                    </div>
                    <div class="td flex items-center gap-2 w-[25%]">
                        <span>Beschreibung</span>
                        <Sort routeName="coins-debit.index" keyName="description" />
                    </div>
                    <div class="td flex items-center gap-2 w-[15%]">
                        <span>Gewinnspiel/e</span>
                        <Sort routeName="coins-debit.index" keyName="sweepstake" />
                    </div>
                    <div class="td flex items-center gap-2 w-[15%]">
                        <span>Total Coins</span>
                        <Sort routeName="coins-debit.index" keyName="total_coins" />
                    </div>
                    <div class="td flex items-center gap-2 w-[5%]">

                    </div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="debit in debits.data" :debit="debit" />
                </template>
            </Table>
            <pagination :paginationData="debits" v-if="debits.data.length > 0"/>
        </template>
        <EmptyPage v-if="debits.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";

//filter
import FilterButton from "../../../../Components/Filters/FilterButton.vue";
import MultiCheckboxFilter from "../../../../Components/Filters/MultiCheckboxFilter.vue";
import SingleCheckboxFilter from "../../../../Components/Filters/SingleCheckboxFilter.vue";
import DateRangeFilter from "../../../../Components/Filters/DateRangeFilter.vue";
import EntriesPerPage from "../../../../Components/EntriesPerPage.vue";
import SearchInPage from "../../../../Components/SearchInPage.vue";

//sorting
import Sort from "../../../../Components/Sort.vue";

//data visualizing
import TableBodyRow from "./Components/TableBodyRow";
import EmptyPage from "../../../../Components/EmptyPage.vue";
import Pagination from "../../../../Components/Pagination.vue";
import Tabs from "../../Components/Tabs.vue";
import Table from "../../../../Components/Tables/Table.vue";

//libraries
import axios from "axios";

const props = defineProps({
    debits: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    mobile_app_users: {
        data: [],
    },
    sweepstakes: {
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

    axios.get(route("coins-debit.filter.data", { ...searchParams })).then((res) => {
        filterData[menu]["data"] = res.data;
    });

    if (silently == true)
        return;

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
