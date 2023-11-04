<template>
    <div class="page-title mb-6">
        <span>Marketinggebühren</span>
    </div>
    <div class="challo__card challo__card--transparent">
        <div class="challo__card--header mt-5">
            <div class="flex justify-between items-center gap-6">
                <div :class="hasPermission('marketing_fee.create') ? 'w-3/5' : 'w-full'">
                    <search-in-page
                        :placeholder="$t('Nach Bezeichnung der Marketinggebühr suchen')"
                        routeName="marketing-fees.index"
                        class=""
                     />
                </div>
                <div
                    class="w-2/5"
                    v-if="hasPermission('marketing_fee.create')">
                    <Link
                        :href="route('marketing-fees.create')"
                        class="challo__btn btn-success  btn-block"
                    >
                        {{ $t("Neue Marketinggebühr hinzufügen") }}
                    </Link>
                </div>
            </div>
            <div class="wrapper flex gap-[18px] items-center">

                <div class="challo__filter flex-1" :class="[option.showFilter ? 'bg-white' : '']">
                    <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                    <div class="filters" v-show="option.showFilter">
                        <div class="filter-menu w-2/5">
                            <div class="filter-menu__label">
                                <DateRangeFilter columnKey="created_at" routeName="marketing-fees.index" />
                            </div>
                        </div>
                        <div class="filter-menu w-2/5">
                            <div class="filter-menu__label">
                                <PriceRangeFilter columnKey="marketing_fee" :options="filterData.price.data" valueKey="value" name="name" title="Marketinggebühr" variable="%" :maxValue="maxValue" :minValue = "minValue" routeName="marketing-fees.index" />
                            </div>
                        </div>
                        <div class="filter-menu">
                            <div class="filter-menu__label">
                                <span v-if="option.current_open_filter_menu == 'status'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('status')">
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
                                    routeName="marketing-fees.index"
                                    v-click-away="() => closeCurrentOptionFilterMenu('status')"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="table-filter" :class="{ active: option.showFilter }" ></div>
                </div>

                <EntriesPerPage target="marketing-fees.index"/>

            </div>
        </div>
        <template v-if="marketing_fees.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 13%;">
                        {{ $t("Bezeichnung ") }}
                        <Sort routeName="marketing-fees.index" keyName="designation" />
                    </div>
                    <div class="th" style="width: 15%">
                        {{ $t("Marketinggebühr") }}
                        <Sort routeName="marketing-fees.index" keyName="marketing_fee" />
                    </div>
                    <div class="th" style="width: 17%">
                        {{ $t("Anteil Senior Partner / Berater ") }}
                        <Sort routeName="marketing-fees.index" keyName="direct_consumers_senior_partner_share" width="22"/>
                    </div>
                    <div class="th" style="width: 13%">
                        {{ $t("Anteil Jackpot") }}
                        <Sort routeName="marketing-fees.index" keyName="direct_consumers_share_jackpot" />
                    </div>
                    <div class="th" style="width: 17%">
                        {{ $t("Anteil Gebühr CHalloMates / Vertriebspartner ") }}
                        <Sort routeName="marketing-fees.index" keyName="direct_consumers_share_fee_challomates" width="22" />
                    </div>
                    <div class="th" style="width: 15%">
                        {{ $t("Anteil CHalloMates Marketing AG ") }}
                        <Sort routeName="marketing-fees.index" keyName="direct_consumers_share_challomates_marketing_ag" width="22" />
                    </div>
                    <div class="th" style="width: 10%">
                        {{ $t("Status") }}
                        <Sort routeName="marketing-fees.index" keyName="status" />
                    </div>
                    <div class="th" style="width: 5%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="fee in marketing_fees.data" :key="fee.id" :fee="fee" />
                </template>
            </Table>
            <pagination :paginationData="marketing_fees" />
        </template>
        <EmptyPage v-if="marketing_fees.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive} from "@vue/reactivity";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import FilterOptionStatus from "./Components/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import { trans } from "laravel-vue-i18n";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from '../../Components/Tables/Table.vue'
import DateRangeFilter from '../../Components/Filters/DateRangeFilter.vue'
import PriceRangeFilter from "./Components/PriceRangeFilter";
import EntriesPerPage from "../../Components/EntriesPerPage.vue";
import axios from 'axios'


const props = defineProps({
    marketing_fees: {
        type: Object,
        required: true,
    },
    priceRanges: {
        type: Object,
        required: false,
    },
    minValue:{
        type: Number,
        required: false,
    },
    maxValue:{
        type: Number,
        required: false,
    }
});

const filterData = reactive({

    price: {
        loaded: false,
        data: props.priceRanges
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
            }
        ]
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

    axios.get(route('marketing-fees.filter-data', { ...searchParams })).then(res => {
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
<style lang="scss" scoped>
    .tr .content .th {
    display: flex;
    align-items: flex-start;
    svg {
        margin-top: 5px;
    }
}
</style>
