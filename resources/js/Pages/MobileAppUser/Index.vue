<template>
    <h1 class="text-primary-3 font-poppins font-semibold text-18xl mb-4">
        Mobile App-Benutzer
    </h1>
    <div class="challo__card challo__card--transparent">
        <div class="challo__card--header mt-6">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page placeholder="Nach Benutzer- Vor-, Nachnamen oder E-Mail Adresse suchen"
                        routeName="mobile-app-users.index" class="" />
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div class="challo__filter flex-1" :class="[option.showFilter ? 'bg-white' : '']">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                    <div class="filters" v-show="option.showFilter">
                        <div class="filter-menu w-420">
                            <div class="filter-menu__label">
                                <DateRangeFilter routeName="mobile-app-users.index" />
                            </div>
                        </div>

                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <FilterUserStatus @toggleMenu="toggleCurrentOpenFilterMenu('status')"
                                    :opend="option.current_open_filter_menu == 'status'" valueKey="value"
                                    label-key="name"
                                    columnKey="status" :options="filterData.status.data"
                                    routeName="mobile-app-users.index"
                                    @closeMenu="closeCurrentOptionFilterMenu('status')"
                                    @instant-update="getFilterOptions('status')"  />
                            </div>
                        </div>
                    </div>
                    <div class="table-filter" :class="{ active: option.showFilter }"></div>
                </div>
                <EntriesPerPage target="mobile-app-users.index"/>
            </div>
        </div>
        <template v-if="mobile_app_users.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 15%">
                        Erstellungsdatum
                        <Sort routeName="mobile-app-users.index" keyName="created_at" />
                    </div>
                    <div class="th" style="width: 15%">
                        Benutzername
                        <Sort routeName="mobile-app-users.index" keyName="username" />
                    </div>
                    <div class="th" style="width: 15%">
                        Art
                        <Sort routeName="mobile-app-users.index" keyName="type" />
                    </div>
                    <div class="th" style="width: 20%">
                        Name
                        <Sort routeName="mobile-app-users.index" keyName="name" />
                    </div>
                    <div class="th" style="width: 20%">
                        E-Mail
                        <Sort routeName="mobile-app-users.index" keyName="email" />
                    </div>
                    <div class="th" style="width: 10%">
                        Status
                        <Sort routeName="mobile-app-users.index" keyName="status" />
                    </div>
                    <div class="th" style="width: 5%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="user in mobile_app_users.data" :key="user.id" :user="user" />
                </template>
            </Table>
            <pagination :paginationData="mobile_app_users" />
        </template>
        <EmptyPage v-if="mobile_app_users.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import FilterOption from "../../Components/Filters/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import { trans } from "laravel-vue-i18n";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import FilterUserStatus from '../../Components/Filters/FilterUserStatus.vue'
import Table from "../../Components/Tables/Table.vue";
import DateRangeFilter from "../../Components/Filters/DateRangeFilter.vue";
import EntriesPerPage from "../../Components/EntriesPerPage.vue";
import axios from "axios";

const props = defineProps({
    mobile_app_users: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    status: {
        data: [
            {
                name: "Active",
                value: "active",
            },
            {
                name: "Inactive",
                value: "inactive",
            },
            {
                name: "Registration pending",
                value: "pending",
            },
            {
                name: "New",
                value: "new",
            },
        ],
    },
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    searchParams.column = menu;

    axios
        .get(route("mobile-app-users.filter.data", { ...searchParams }))
        .then((res) => {
            filterData[menu]["data"] = res.data;
            option.current_open_filter_menu = menu;
        });
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


<style>

</style>
