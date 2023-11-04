<template>
    <div class="page-title mb-6 float-left text-primary-3">
        <span>Jackpotbeiträge</span>
    </div>

    <Back class="float-right" :backPrevUrl="true" :showModal="false" />

    <div class="challo__card challo__card--transparent clear-both">
        <div class="challo__card--header mt-5">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page
                        placeholder="Nach Store-Namen, Mobile App-Benutzer-, Vor- oder Nachnamen suchen"
                        routeName="jackpot.index"
                        class=""
                     />
                </div>
            </div>

            <div class="flex justify-between items-center gap-[18px]">
                <div class="w-[86%]">
                    <div class="challo__filter" :class="[option.showFilter ? 'bg-white' : '']">
                        <FilterButton @toggleShowFilter="value => (option.showFilter = value)" />

                        <div class="filters " v-show="option.showFilter">

                            <div class="filter-menu w-fit mr-32">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        keyPrefix="check_out"
                                        label="Check-out Zeitpunkt"
                                        routeName="jackpot.index" />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-32">
                                <div class="filter-menu__label">

<!--                                    <span v-if="option.current_open_filter_menu == 'mobile_app_user'" class="filter-menu__label&#45;&#45;title" @click="toggleCurrentOpenFilterMenu('mobile_app_user')">
                                        Mobile App-Benutzer
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label&#45;&#45;title" @click="toggleCurrentOpenFilterMenu('mobile_app_user')">
                                       Mobile App-Benutzer
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        class-list="w-[200px]"
                                        v-if="option.current_open_filter_menu === 'mobile_app_user'"
                                        valueKey="value"
                                        columnKey="mobile_app_user"
                                        :options="filterData.mobile_app_user.data"
                                        routeName="jackpot.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_user')"
                                    />-->

                                    <SingleCheckboxFilter
                                        label="Mobile App-Benutzer"
                                        column="mobile_app_user"
                                        labelKey="name"
                                        valueKey="value"
                                        :options="filterData.mobile_app_user.data"
                                        routeName="jackpot.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('mobile_app_user')"
                                        @instant-update="(key, silently = false) => getFilterOptions(key)"
                                        :active="option.current_open_filter_menu == 'mobile_app_user'"
                                        @toggle="toggleCurrentOpenFilterMenu('mobile_app_user')" optionClass="min-w-[250px]"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-32">
                                <div class="filter-menu__label">


                                    <span v-if="option.current_open_filter_menu == 'store'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('store')">
                                        Store
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('store')">
                                       Store
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        class-list="w-[250px]"
                                        v-if="option.current_open_filter_menu === 'store'"
                                        valueKey="value"
                                        columnKey="store"
                                        :options="filterData.store.data"
                                        routeName="jackpot.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('store')"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-32">
                                <div class="filter-menu__label">

                                    <span v-if="option.current_open_filter_menu == 'mobile_user_type'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('mobile_user_type')">
                                        Benutzer-Art
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('mobile_user_type')">
                                        Benutzer-Art
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        class-list="w-[250px]"
                                        v-if="option.current_open_filter_menu === 'mobile_user_type'"
                                        valueKey="value"
                                        columnKey="mobile_user_type"
                                        :options="filterData.mobile_user_type.data"
                                        routeName="jackpot.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('mobile_user_type')"
                                    />
                                </div>
                            </div>

                        </div>
                        <div class="table-filter" :class="{ active: option.showFilter }"></div>
                    </div>
                </div>
                <div class="">
                    <div class="filter-menu__label text-right">

                        <span v-if="option.current_open_filter_menu == 'per_page'" class="text-tints-5 filter-menu__label--title !font-poppins" @click="toggleCurrentOpenFilterMenu('per_page')">
                            Einträge pro Seite
                            <up-arrow />
                        </span>

                        <span v-else class="text-tints-5 filter-menu__label--title !font-poppins" @click="toggleCurrentOpenFilterMenu('per_page')">
                            Einträge pro Seite
                            <down-arrow />
                        </span>

                        <PerPageOption
                            v-if="option.current_open_filter_menu === 'per_page'"
                            valueKey="value"
                            columnKey="per_page"
                            routeName="jackpot.index"
                            @closePerPageOption="closeCurrentOptionFilterMenu('per_page')"
                            v-click-away="() => closeCurrentOptionFilterMenu('per_page')"
                        />
                    </div>
                </div>
            </div>
        </div>
        <template v-if="jackpot_contributions.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 20%;">
                        Check-out Zeitpunkt
                        <Sort routeName="jackpot.index" keyName="checkout_time" />
                    </div>
                    <div class="th" style="width: 20%;">
                        Mobile App-Benutzer
                        <Sort routeName="jackpot.index" keyName="mobile_app_users" />
                    </div>
                    <div class="th" style="width: 15%;">
                        Benutzer-Art
                        <Sort routeName="jackpot.index" keyName="user_type" />
                    </div>

                    <div class="th" style="width: 15%;">
                        Store
                        <Sort routeName="jackpot.index" keyName="store" />
                    </div>

                    <div class="th" style="width: 13%;">
                        Umsatz
                        <Sort routeName="jackpot.index" keyName="turn_over" />
                    </div>
                    <div class="th" style="width: 15%;">
                        Jackpot Beitrag
                        <Sort routeName="jackpot.index" keyName="jackpot" />
                    </div>
                    <div class="th" style="width: 2%;"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="(jackpot, index) in jackpot_contributions.data" :key="index" :jackpot="jackpot" />
                </template>
            </Table>
             <pagination :paginationData="jackpot_contributions" />
        </template>
        <EmptyPage v-if="jackpot_contributions.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive} from "@vue/reactivity";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DateRangeFilter from "../../Components/Filters/DateRangeFilter";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import PerPageOption from "../../Components/Filters/PerPageOption";
import SingleCheckboxFilter from "../../Components/Filters/SingleCheckboxFilter";
import FilterOption from "../../Components/Filters/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import { trans } from "laravel-vue-i18n";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from '../../Components/Tables/Table.vue'
import axios from 'axios'
import Back from "../../Components/Form/Back";

const props = defineProps({
    jackpot_contributions: {
        type: Object,
        required: true,
    },
});

const filterData = reactive({
    mobile_app_user: {
        loaded: false,
        data: [],
    },

    store: {
        loaded: false,
        data: [],
    },

    mobile_user_type: {
        loaded: false,
        data: [],
    },
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

    axios.get(route('jackpot.filter-data', { ...searchParams })).then(res => {
        filterData[menu]['data'] = res.data;
    });

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu === menu) {
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
