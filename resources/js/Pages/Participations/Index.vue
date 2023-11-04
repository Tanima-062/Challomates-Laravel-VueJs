<template>
    <Tabs :jackpot-amount="current_jackpot_amount" activeTab="participations" />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page
                        placeholder="Nach Gewinnspielname suchen"
                        routeName="participation.index"
                        class=""
                     />
                </div>
            </div>

            <div class="flex justify-between items-center gap-6">

                <div class="w-[86%]">
                    <div class="challo__filter" :class="[option.showFilter ? 'bg-white' : '']">
                        <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                        <div class="filters " v-show="option.showFilter">

                            <div class="filter-menu w-1/5">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        label="Zeitpunkt"
                                        routeName="participation.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-1/5">
                                <div class="filter-menu__label">

<!--                                    <span v-if="option.current_open_filter_menu == 'participant_name'" class="filter-menu__label&#45;&#45;title" @click="toggleCurrentOpenFilterMenu('participant_name')">
                                        Teilnehmer
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label&#45;&#45;title" @click="toggleCurrentOpenFilterMenu('participant_name')">
                                        Teilnehmer
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        v-if="option.current_open_filter_menu === 'participant_name'"
                                        valueKey="value"
                                        columnKey="participant_id"
                                        :options="filterData.participant_name.data"
                                        routeName="participation.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('participant_name')"
                                    />-->

                                    <SingleCheckboxFilter
                                        label="Teilnehmer"
                                        column="participant_id"
                                        labelKey="name"
                                        valueKey="value"
                                        :options="filterData.participant_name.data"
                                        routeName="participation.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('participant_name')"
                                        @instant-update="(key, silently = false) => getFilterOptions(key)"
                                        :active="option.current_open_filter_menu == 'participant_name'"
                                        @toggle="toggleCurrentOpenFilterMenu('participant_name')" optionClass="min-w-[250px]"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-1/5">
                                <div class="filter-menu__label">

                                    <span v-if="option.current_open_filter_menu == 'sweepstake_name'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sweepstake_name')">
                                        Gewinnspiel
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sweepstake_name')">
                                        Gewinnspiel
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        v-if="option.current_open_filter_menu === 'sweepstake_name'"
                                        valueKey="value"
                                        columnKey="sweepstake_id"
                                        :options="filterData.sweepstake_name.data"
                                        routeName="participation.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('sweepstake_name')"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-1/5">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        keyPrefix="raffle"
                                        label="Verlosungszeitpunkt"
                                        routeName="participation.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-1/5">
                                <div class="filter-menu__label">

                                    <span v-if="option.current_open_filter_menu == 'status'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('status')">
                                        Gewinnspiel Status
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('status')">
                                        Gewinnspiel Status
                                        <down-arrow />
                                    </span>

                                    <FilterOptionStatus
                                        v-if="option.current_open_filter_menu === 'status'"
                                        valueKey="value"
                                        columnKey="status"
                                        :options="filterData.status.data"
                                        routeName="participation.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('status')"
                                    />
                                </div>
                            </div>

                        </div>
                        <div class="table-filter" :class="{ active: option.showFilter }" >
                        </div>
                    </div>
                </div>

                <div>
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
                            routeName="participation.index"
                            @closePerPageOption="closeCurrentOptionFilterMenu('per_page')"
                            v-click-away="() => closeCurrentOptionFilterMenu('per_page')"
                        />
                    </div>
                </div>

            </div>
        </div>
        <template v-if="participations.data.length > 0">
            <Table>
                <template #thead>
                    <div class="th" style="width: 15%">
                        Zeitpunkt
                        <Sort routeName="participation.index" keyName="created_at" />
                    </div>
                    <div class="th" style="width: 15%">
                        Teilnehmer
                        <Sort routeName="participation.index" keyName="participant" />
                    </div>
                    <div class="th" style="width: 15%">
                        Gewinnspiel
                        <Sort routeName="participation.index" keyName="sweepstake_name" />
                    </div>

                    <div class="th" style="width: 17%">
                        Gewinnzahlen
                        <Sort routeName="participation.index" keyName="winning_numbers" />
                    </div>

                    <div class="th" style="width: 18%">
                        Verlosungszeitpunkt
                        <Sort routeName="participation.index" keyName="sweepstakes_raffle_time" />
                    </div>
                    <div class="th" style="width: 15%">
                        Gewinnspiel Status
                        <Sort routeName="participation.index" keyName="status" />
                    </div>
                    <div class="th" style="width: 5%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow v-for="(participant, index) in participations.data" :key="index" :participant="participant" />
                </template>
            </Table>
             <pagination :paginationData="participations" />
        </template>
        <EmptyPage v-if="participations.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive} from "@vue/reactivity";
import DateRangeFilter from "../../Components/Filters/DateRangeFilter";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import FilterOptionStatus from './Components/FilterOption'
import FilterOption from "../../Components/Filters/FilterOption.vue";
import SingleCheckboxFilter from "../../Components/Filters/SingleCheckboxFilter";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import PerPageOption from "../../Components/Filters/PerPageOption";
import { trans } from "laravel-vue-i18n";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from '../../Components/Tables/Table.vue'
import axios from 'axios'
import SweepStakeIcon from '../../Components/Icons/Sweepstake.vue'
import Tabs from './Components/Tabs.vue'

const props = defineProps({
    participations: {
        type: Object,
        required: true,
    },
    current_jackpot_amount: {
        type: [Number, String],
        required: true,
    },
});

const filterData = reactive({
    sweepstake_name: {
        loaded: false,
        data: [],
    },
    participant_name: {
        loaded: false,
        data: [],
    },
    status: {
        loaded: false,
        data: [
            {
                name: 'Aktiv (Publiziert)',
                value: 'active_published'
            },
            {
                name: 'Beendet (Publiziert)',
                value: 'finished_published'
            },
            {
                name: 'Ausgelost (Drawn)',
                value: 'drawn_published'
            },
            /*{
                name: 'Ausgelost (Drawn)',
                value: 'drawn_not_published'
            }*/
        ],
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

    axios
    .get(route("participation.filter-data", { ...searchParams }))
    .then((res) => {
        filterData[menu]["data"] = res.data;
        option.current_open_filter_menu = menu;
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
