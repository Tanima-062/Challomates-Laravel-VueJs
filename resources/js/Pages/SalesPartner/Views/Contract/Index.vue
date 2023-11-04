<template>
    <div class="mb-8 -mt-8">
        <h3 class="font-poppins text-18xl text-primary-3 font-semibold">Vertriebspartner und Verträge</h3>
    </div>
    <Tabs />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div :class="[hasPermission('contract.create') ? 'w-3/5' : 'w-full']">
                    <search-in-page
                        placeholder="Nach Vertrags-ID, Vertragsnummer/-bezeichnung oder Vertriebspartnerfirmennamen suchen"
                        routeName="contract.index" class="" />
                </div>
                <div class="w-2/5" v-if="hasPermission('contract.create')">
                    <Link :href="route('contract.create')" class="challo__btn btn-success  btn-block">
                    Neuer Vertrag hinzufügen
                    </Link>
                </div>
            </div>

            <div class="wrapper flex gap-[18px] items-center">
                <div class="challo__filter flex-1" :class="{ 'bg-white': option.showFilter }">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" class="!mr-0"/>

                    <div class="filters flex justify-around" v-show="option.showFilter">
                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <DateRangeFilter routeName="contract.index" />
                            </div>
                        </div>
                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <DateRangeFilter routeName="contract.index" label="Vertragslaufzeit"
                                    keyPrefix="contract_term" />
                            </div>
                        </div>
                        <div class="filter-menu">
                            <MultiCheckboxFilter label="Vertriebspartner" column="sales_partner" labelKey="name"
                                valueKey="id" :options="filterData.sales_partner.data" routeName="contract.index"
                                :active="option.current_open_filter_menu == 'sales_partner'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('sales_partner')" optionClass="min-w-[250px]" />
                        </div>
                        <div class="filter-menu">
                            <MultiCheckboxFilter label="Paket" column="package" labelKey="name" valueKey="id"
                                :options="filterData.package.data" routeName="contract.index"
                                :active="option.current_open_filter_menu == 'package'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('package')" optionClass="min-w-[250px]" />
                        </div>
                        <div class="filter-menu">
                            <MultiCheckboxFilter label="Marketinggebühr" column="marketing_fee" labelKey="name"
                                valueKey="id" :options="filterData.marketing_fee.data" routeName="contract.index"
                                :active="option.current_open_filter_menu == 'marketing_fee'"
                                @instant-update="(key) => getFilterOptions(key)"
                                @toggle="toggleCurrentOpenFilterMenu('marketing_fee')" optionClass="min-w-[250px]" />
                        </div>

                        <div class="filter-menu">
                            <FilterUserStatus @toggleMenu="toggleCurrentOpenFilterMenu('status')"
                                :opend="option.current_open_filter_menu == 'status'" valueKey="value" columnKey="status"
                                label-key="name" :options="filterData.status.data" routeName="contract.index"
                                @closeMenu="closeCurrentOptionFilterMenu('status')"
                                @instant-update="getFilterOptions('status')" />
                        </div>
                    </div>
                </div>
                <EntriesPerPage :target="'contract.index'" />
            </div>
        </div>
        <template v-if="contracts.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th w-[20%]">
                        <span>Erstellungsdatum</span>
                        <Sort keyName="created_at" routeName="contract.index" />
                    </div>
                    <div class="th w-[30%]">
                        <span>Vertragsnummer/-bezeichnung</span>
                        <Sort keyName="name" routeName="contract.index" />
                    </div>
                    <div class="th w-[17%]">
                        <span>Vertriebspartner</span>
                        <Sort keyName="sales_partner" routeName="contract.index" />
                    </div>
                    <div class="th w-[17%]">
                        <span>Vertragslaufzeit</span>
                        <Sort keyName="contract_term_period" routeName="contract.index" />
                    </div>
                    <div class="th w-[11%]">
                        <span>Status</span>
                        <Sort keyName="status" routeName="contract.index" />
                    </div>
                    <div class="th w-[5%]"></div>
                </template>
                <template #tbody>
                    <ContractTableRow v-for="contract in contracts.data" :contract="contract" />
                </template>
            </Table>
            <pagination :paginationData="contracts" />
        </template>
        <EmptyPage v-if="contracts.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import SearchInPage from "../../../../Components/SearchInPage.vue";
import FilterButton from "../../../../Components/Filters/FilterButton.vue";
import Tabs from "../../Components/Tabs.vue";
import Table from "../../../../Components/Tables/Table.vue";
import EmptyPage from "../../../../Components/EmptyPage.vue";
import Pagination from "../../../../Components/Pagination.vue";
import Sort from "../../../../Components/Sort.vue";
import ContractTableRow from "../../Components/ContractTableRow.vue";
import DateRangeFilter from "../../../../Components/Filters/DateRangeFilter.vue";
import MultiCheckboxFilter from "../../../../Components/Filters/MultiCheckboxFilter.vue";
import FilterUserStatus from "../../../../Components/Filters/FilterUserStatus.vue";
import EntriesPerPage from "../../../../Components/EntriesPerPage.vue";
import axios from "axios";

const props = defineProps({
    contracts: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    status: {
        loaded: false,
        data: [],
    },
    sales_partner: {
        loaded: false,
        data: [],
    },
    package: {
        loaded: false,
        data: [],
    },
    marketing_fee: {
        loaded: false,
        data: [],
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

    axios
        .get(route("contract.filter.data", { ...searchParams }))
        .then((res) => {
            filterData[menu]["data"] = res.data;
            option.current_open_filter_menu = menu;
        });


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
