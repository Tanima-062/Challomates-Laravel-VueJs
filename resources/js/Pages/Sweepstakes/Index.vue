<template>
<!--    <PermanentNotification />-->
    <Tabs :jackpot-amount="current_jackpot_amount" activeTab="sweepstakes" />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div :class="hasPermission('sweepstakes.create') ? 'w-3/5' : 'w-full'">
                    <search-in-page
                        placeholder="Nach Gewinnspielname suchen"
                        routeName="sweepstakes.index"
                        class=""
                    />
                </div>
                <div
                    class="w-2/5"
                    v-if="hasPermission('sweepstakes.create')">
                    <Link
                        :href="route('sweepstakes.create')"
                        class="challo__btn btn-success btn-block"
                    >
                        Neues Gewinnspiel hinzufügen
                    </Link>
                </div>
            </div>

            <div class="flex justify-between items-center gap-6">

                <div class="w-[86%]">
                    <div class="challo__filter" :class="[option.showFilter ? 'bg-white' : '']">
                        <FilterButton @toggleShowFilter="value => (option.showFilter = value)" />

                        <div class="filters " v-show="option.showFilter">

                            <div class="filter-menu w-fit mr-28">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        routeName="sweepstakes.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-28">
                                <div class="filter-menu__label">
                                    <span v-if="option.current_open_filter_menu == 'type'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('type')">
                                        Art
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('type')">
                                        Art
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        v-if="option.current_open_filter_menu === 'type'"
                                        valueKey="value"
                                        columnKey="type"
                                        :options="filterData.type.data"
                                        routeName="sweepstakes.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('type')"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-28">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        keyPrefix="runtime"
                                        label="Laufzeit"
                                        routeName="sweepstakes.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-28">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        keyPrefix="raffle_time"
                                        label="Verlosungszeitpunkt"
                                        routeName="sweepstakes.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit">
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
                                        v-if="option.current_open_filter_menu === 'status'"
                                        valueKey="value"
                                        columnKey="status"
                                        :options="filterData.status.data"
                                        routeName="sweepstakes.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('status')"
                                    />
                                </div>
                            </div>

                        </div>
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
                            routeName="sweepstakes.index"
                            @closePerPageOption="closeCurrentOptionFilterMenu('per_page')"
                            v-click-away="() => closeCurrentOptionFilterMenu('per_page')"
                        />
                    </div>
                </div>

            </div>
        </div>
        <template v-if="sweepstakes.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 16%">
                        Name
                        <Sort routeName="sweepstakes.index" keyName="name" />
                    </div>
                    <div class="th" style="width: 10%">
                        Art
                        <Sort routeName="sweepstakes.index" keyName="type" />
                    </div>
                    <div class="th" style="width: 22%">
                        Laufzeit
                        <Sort routeName="sweepstakes.index" keyName="runtime_from" />
                    </div>

                    <div class="th" style="width: 27%">
                        Verbleibende Laufzeit
                        <Sort routeName="sweepstakes.index" keyName="runtime_to" />
                    </div>

                    <div class="th" style="width: 15%">
                        Verlosungszeitpunkt
                        <Sort routeName="sweepstakes.index" keyName="raffle_time" />
                    </div>
                    <div class="th" style="width: 6%">
                        Status
                        <Sort routeName="sweepstakes.index" keyName="status" />
                    </div>
                    <div class="th" style="width: 4%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="(sweepstake, index) in sweepstakes.data" :key="index" :sweepstake="sweepstake" />
                </template>
            </Table>
            <pagination :paginationData="sweepstakes" />
        </template>
        <EmptyPage v-if="sweepstakes.data.length < 1" />
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
import FilterOptionStatus from './Components/FilterOption'
import PermanentNotification from "./Components/PermanentNotification";
import FilterOption from "../../Components/Filters/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from '../../Components/Tables/Table.vue'
import axios from 'axios'
import SweepStakeIcon from '../../Components/Icons/Sweepstake.vue'
import Tabs from './Components/Tabs.vue'

const props = defineProps({
    sweepstakes: {
        type: Object,
        required: true,
    },
    current_jackpot_amount: {
        type: [Number, String],
        required: true,
    },
    pinnedSweepstakes: {
        type: Object,
        required: false,
    },
});

const filterData = reactive({
    status: {
        loaded: false,
        data: [
            {
                name: 'Neu (Publiziert)',
                value: 'new_published'
            },
            {
                name: 'Neu (NICHT Publiziert)',
                value: 'new_not_published'
            },
            {
                name: 'Aktiv (Publiziert)',
                value: 'active_published'
            },
            {
                name: 'Aktiv (NICHT Publiziert)',
                value: 'active_not_published'
            },
            {
                name: 'Beendet (Publiziert)',
                value: 'finished_published'
            },
            {
                name: 'Beendet (NICHT Publiziert)',
                value: 'finished_not_published'
            },
            {
                name: 'Abgebrochen (Publiziert)',
                value: 'canceled_published'
            },
            {
                name: 'Abgebrochen (NICHT Publiziert)',
                value: 'canceled_not_published'
            },
            {
                name: 'Ausgelost (Drawn)',
                value: 'drawn_published'
            }
        ],
    },
    type: {
        loaded: false,
        data: [],
    },
    runtime: {
        loaded: false,
        data: [],
    },
    raffle_time: {
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

    axios.get(route('sweepstakes.filter-data', { ...searchParams })).then(res => {
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

</script>

<style lang="scss" scoped>
.filter-menu__label--title {
    &.text-tints-5 {
        color: #135F84;
    }
}
</style>
