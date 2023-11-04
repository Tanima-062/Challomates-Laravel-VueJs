<template>
    <Tabs :jackpot-amount="current_jackpot_amount" activeTab="raffles" />
    <div class="challo__card rounded-t-none pt-5">
        <div class="challo__card--header">
            <div class="flex justify-between items-center gap-6">
                <div class="w-full">
                    <search-in-page
                        placeholder="Nach Verlosungs-ID oder Gewinnspielname suchen"
                        routeName="raffles.index"
                        class=""
                    />
                </div>
            </div>

            <div class="flex justify-between items-center gap-6">
                <div class="w-[86%]">
                    <div class="challo__filter" :class="[option.showFilter ? 'bg-white' : '']">
                        <FilterButton @toggleShowFilter="(value) => (option.showFilter = value)" />

                        <div class="filters" v-show="option.showFilter">
                            <div class="filter-menu w-fit mr-40">
                                <div class="filter-menu__label">

                                    <span v-if="option.current_open_filter_menu == 'sweepstake_id'" class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sweepstake_id')">
                                        Gewinnspiel
                                        <up-arrow />
                                    </span>
                                    <span v-else class="filter-menu__label--title" @click="toggleCurrentOpenFilterMenu('sweepstake_id')">
                                        Gewinnspiel
                                        <down-arrow />
                                    </span>

                                    <FilterOption
                                        class-list="w-[220px]"
                                        v-if="option.current_open_filter_menu === 'sweepstake_id'"
                                        valueKey="value"
                                        columnKey="sweepstake_id"
                                        :options="filterData.sweepstake_id.data"
                                        routeName="raffles.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('sweepstake_id')"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-40">
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
                                        routeName="raffles.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('type')"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit mr-40">
                                <div class="filter-menu__label">
                                    <DateRangeFilter
                                        keyPrefix="raffle_time"
                                        label="Verlosungszeitpunkt"
                                        routeName="raffles.index"
                                    />
                                </div>
                            </div>

                            <div class="filter-menu w-fit">
                                <div class="filter-menu__label">
                                    <SingleCheckboxFilter
                                        label="Gewinner"
                                        column="winner"
                                        labelKey="name"
                                        valueKey="value"
                                        :options="filterData.winner.data"
                                        routeName="raffles.index"
                                        v-click-away="() => closeCurrentOptionFilterMenu('winner')"
                                        @instant-update="(key, silently = false) => getFilterOptions(key)"
                                        :active="option.current_open_filter_menu == 'winner'"
                                        @toggle="toggleCurrentOpenFilterMenu('winner')" optionClass="min-w-[250px]"
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
                            routeName="raffles.index"
                            @closePerPageOption="closeCurrentOptionFilterMenu('per_page')"
                            v-click-away="() => closeCurrentOptionFilterMenu('per_page')"
                        />
                    </div>
                </div>
            </div>
        </div>
        <template v-if="raffles.data.length > 0">
            <Table>
                <template #thead>
                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 12%' : 'width: 13%'"
                    >
                        Verlosungs-ID
                        <Sort routeName="raffles.index" keyName="raffle_id" />
                    </div>
                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 14%' : 'width: 14%'"
                    >
                        Gewinnspiel
                        <Sort routeName="raffles.index" keyName="sweepstake_name" />
                    </div>
                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 10%' : 'width: 12%'"
                    >
                        Art
                        <Sort routeName="raffles.index" keyName="sweepstake_type" />
                    </div>

                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 16%' : 'width: 18%'"
                    >
                        Verlosungszeitpunkt
                        <Sort routeName="raffles.index" keyName="raffle_time" />
                    </div>
                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 15%' : 'width: 17%'"
                    >
                        Gewinnzahlstellen
                        <Sort routeName="raffles.index" keyName="winning_numbers_from" />
                    </div>
                    <div v-if="hasPermission('raffles.edit')" class="th" style="width: 8%">
                        Verlosung starten
                        <Sort
                            :width="hasPermission('raffles.edit') ? 15 : 8"
                            routeName="raffles.index"
                            keyName="raffle_launch"
                        />
                    </div>

                    <div
                        class="th"
                        :style="hasPermission('raffles.edit') ? 'width: 15%' : 'width: 16%'"
                    >
                        Gewinner
                        <Sort routeName="raffles.index" keyName="winner" />
                    </div>
                    <div class="th" style="width: 8%">
                        Video
                        <Sort routeName="raffles.index" keyName="video" />
                    </div>
                    <div class="th" style="width: 2%"></div>
                </template>
                <template #tbody>
                    <TableBodyRow
                        v-for="(raffle, index) in raffles.data"
                        :key="index"
                        :raffle="raffle"
                    />
                </template>
            </Table>
            <pagination :paginationData="raffles" />
        </template>
        <EmptyPage v-if="raffles.data.length < 1" />
    </div>
</template>

<script setup>
import { reactive } from "@vue/reactivity";
import SearchInPage from "../../Components/SearchInPage.vue";
import { Link } from "@inertiajs/inertia-vue3";
import DateRangeFilter from "../../Components/Filters/DateRangeFilter";
import DownArrow from "../../Components/Icons/DownArrow.vue";
import UpArrow from "../../Components/Icons/UpArrow.vue";
import FilterDatePicker from "../../Components/Filters/FilterDatePicker.vue";
import PerPageOption from "../../Components/Filters/PerPageOption";
import FilterOptionStatus from "./Components/FilterOptionStatus";
import SingleCheckboxFilter from "../../Components/Filters/SingleCheckboxFilter";
import FilterOption from "../../Components/Filters/FilterOption.vue";
import FilterButton from "../../Components/Filters/FilterButton.vue";
import Sort from "../../Components/Sort.vue";
import TableBodyRow from "./Components/TableBodyRow.vue";
import EmptyPage from "../../Components/EmptyPage.vue";
import Pagination from "../../Components/Pagination.vue";
import FlashNotification from "../../Components/Modal/Content/FlashNotification.vue";
import Table from "../../Components/Tables/Table.vue";
import axios from "axios";
import SweepStakeIcon from "../../Components/Icons/Sweepstake.vue";
import Tabs from "./Components/Tabs.vue";

const props = defineProps({
    raffles: {
        type: Object,
        required: true,
    },
    current_jackpot_amount: {
        type: [Number, String],
        required: true,
    },
});

const filterData = reactive({
    type: {
        loaded: false,
        data: [],
    },
    sweepstake_id: {
        loaded: false,
        data: [],
    },
    winner: {
        loaded: false,
        data: [],
    },
});
const option = reactive({
    showFilter: false,
    current_open_filter_menu: null,
});

const getFilterOptions = (menu) => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));

    searchParams.column = menu;

    axios.get(route("raffles.filter-data", { ...searchParams })).then((res) => {
        filterData[menu]["data"] = res.data;
    });

    option.current_open_filter_menu = menu;
};

const toggleCurrentOpenFilterMenu = (menu) => {
    if (option.current_open_filter_menu === menu) {
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
    if (option.current_open_filter_menu === e) {
        option.current_open_filter_menu = null;
    }
};
</script>

<style lang="scss" scoped>
.filter-menu__label--title {
    &.text-tints-5 {
        color: #135f84;
    }
}
</style>
