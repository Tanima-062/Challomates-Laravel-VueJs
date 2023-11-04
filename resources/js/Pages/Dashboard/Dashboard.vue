<template>
    <h1 class="text-primary-3 font-poppins font-semibold text-18xl mb-4">
        Dashboard
    </h1>
    <div class="challo__card challo__card--transparent pb-7">
        <div class="challo__card--header mt-2">
            <!-- <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <SearchInPage
                        placeholder="Nach Store-Namen, Besuchervor- oder nachnamen suchen"
                        routeName="dashboard"
                    />
                </div>
            </div> -->

            <div class="wrapper flex gap-[18px] items-center">
                <div
                    class="challo__filter flex-1"
                    :class="[option.showFilter ? 'bg-white' : '']"
                >
                    <FilterButton
                        @toggleShowFilter="(value) => (option.showFilter = value)"
                        class="!mr-0"
                    />

                    <div
                        class="filters flex justify-around"
                        v-show="option.showFilter"
                    >

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Vertriebspartner"
                                column="sales_partners"
                                labelKey="name"
                                valueKey="value"
                                :options="filterData.sales_partners?.data"
                                routeName="dashboard"
                                v-click-away="() => closeCurrentOptionFilterMenu('sales_partners')"
                                @instant-update="(key) => getFilterOptions(key)"
                                :active="option.current_open_filter_menu == 'sales_partners'"
                                @toggle="toggleCurrentOpenFilterMenu('sales_partners')"
                                optionClass="min-w-[250px]"
                                :all-selectable="true"
                            />
                        </div>

                        <div class="filter-menu">
                            <SingleCheckboxFilter
                                label="Mobile App-Benutzer"
                                column="mobile_app_users"
                                labelKey="name"
                                valueKey="value"
                                :options="filterData.mobile_app_users?.data"
                                routeName="dashboard"
                                v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_users')"
                                @instant-update="
                                    (key, silently = false) => getFilterOptions(key, silently)
                                "
                                :active="option.current_open_filter_menu == 'mobile_app_users'"
                                @toggle="toggleCurrentOpenFilterMenu('mobile_app_users')"
                                optionClass="min-w-[250px]"
                                :all-selectable="true"
                                :active-selectable="true"
                                :inactive-selectable="true"
                            />
                        </div>

                        <div class="filter-menu">
                            <MultiCheckboxFilter
                                label="Company Consultant"
                                column="company_consultant"
                                labelKey="name"
                                valueKey="value"
                                :options="filterData.company_consultant.data"
                                routeName="dashboard"
                                :active="option.current_open_filter_menu == 'company_consultant'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('company_consultant')"
                                optionClass="min-w-[250px]"
                            />
                        </div>
                        <div class="filter-menu">
                            <DateRangeFilter
                                routeName="dashboard"
                                label="Datum"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="charts grid grid-cols-2 grid-rows-2 gap-6">
            <PieChart :data="UserChart.data" chart-id="user-chart" :label="UserChart.labels" :total="UserChart.total" title="Benutzer"/>
            <PieChart data-label-prefix="CHF" :data="TurnOverChart.data" chart-id="turnover-chart" :label="TurnOverChart.labels" :total="TurnOverChart.total" title="Umsatz"/>
            <PieChart data-label-prefix="CHF" :data="DirectConsumerChart.data" chart-id="user-chart" :label="DirectConsumerChart.labels" :total="DirectConsumerChart.total" title="Direkte Konsumenten"/>
            <PieChart data-label-prefix="CHF" :data="SalesPartnerConsumerChart.data" chart-id="user-chart" :label="SalesPartnerConsumerChart.labels" :total="SalesPartnerConsumerChart.total" title="Vertriebskonsumenten"/>
        </div>
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";
import DateRangeFilter from "./Componens/DateRangeFilter.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import MultiCheckboxFilter from "./Componens/MultiCheckboxFilter.vue";
import axios from "axios";
import PieChart from "./Componens/PieChart.vue";
import SingleCheckboxFilter from "./Componens/SingleCheckboxFilter.vue";

const props = defineProps({
    UserChart: {
        type: Object,
        required: true,
    },
    TurnOverChart: {
        type: Object,
        required: true,
    },
    DirectConsumerChart: {
        type: Object,
        required: true,
    },
    SalesPartnerConsumerChart: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    mobile_app_users: {
        data: [],
    },
    sales_partners: {
        data: [],
    },
    company_consultant: {
        data: [],
    },
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu, silently = false) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    searchParams.column = menu;

    axios.get(route("dashboard.filter.data", { ...searchParams })).then((res) => {
        filterData[menu]["data"] = res.data;
    });

    if (silently == true) return;
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
