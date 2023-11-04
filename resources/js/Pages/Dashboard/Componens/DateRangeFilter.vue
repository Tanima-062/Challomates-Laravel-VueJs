<template>
    <div class="date-selection relative">
        <span
            class="filter-menu__label--title"
            @click="toggleOpen"
            v-html="filterDateLabel()"
        >

        </span>
        <div
            class="options absolute right-0 w-[450px] bg-white divide-y divide-tints-1 text-gray-3"
            v-if="open && !showDateRangePicker"
            v-click-away="close"
        >
            <div
                class="p-[10px] cursor-pointer text-tints-5"
                :class="{ 'font-bold': selection == 'last_week' }"
                @click="setDateRange('last_week')"
            >Letzte Woche</div>
            <div
                class="p-[10px] cursor-pointer text-tints-5"
                :class="{ 'font-bold': selection == 'last_month' }"
                @click="setDateRange('last_month')"
            >Letzter Monat</div>
            <div
                class="p-[10px] cursor-pointer text-tints-5"
                :class="{ 'font-bold':  (dateForm.date.start && dateForm.date.end && selection == null) }"
                @click="toggleDateRangePicker"
            >Benutzerdefinierter Zeitraum</div>
        </div>

        <div
            class="datepicker relative"
            v-if="showDateRangePicker"
            v-click-away="close"
        >
            <v-date-picker
                v-model="dateForm.date"
                is-range
                title-position="left"
                @dayclick="dayclick"
                :attributes="datepickerOptions.attrs"
                color="green"
                :key="compoenentKey"
                :masks="{ input: 'DD.MM.YYYY', title: 'MMM YYYY' }"
            >
                <template v-slot="{ inputValue, inputEvents }">
                    <div class="filter-date-range-picker shadow-md rounded-b-md">
                        <input
                            id="date"
                            class="challo__input"
                            :value="inputValue.start"
                            v-on="inputEvents.start"
                            :placeholder="$t('TT.MM.JJJJ')"
                        />
                        <span class="">
                            <svg viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                                />
                            </svg>
                        </span>
                        <input
                            id="date"
                            class="challo__input"
                            :value="inputValue.end"
                            v-on="inputEvents.end"
                            :placeholder="$t('TT.MM.JJJJ')"
                        />

                        <button
                            class="clear-btn challo__btn"
                            @click.prevent="clearDate"
                        >Löschen</button>
                    </div>
                </template>
            </v-date-picker>
        </div>
    </div>

</template>

<script>
import { reactive, ref } from "@vue/reactivity";
import { computed, watch } from "@vue/runtime-core";
import { debounce as _debounce } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import DownArrow from "../../../Components/Icons/DownArrow.vue";
import dayjs from 'dayjs'
import weekday from "dayjs/plugin/weekday"

dayjs.locale({
    weekStart: 1
}, null, true)
dayjs.extend(weekday)

export default {
    props: {
        keyPrefix: {
            type: String,
            default: null,
        },
        routeName: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: 'Erstellungsdatum'
        },
        columnKey: {
            type: String,
            default: null
        }
    },
    components: {
        DownArrow
    },
    setup(props, ctx) {
        const compoenentKey = ref(0)
        const showDateRangePicker = ref(false)
        const dateForm = reactive({
            date: {
                start: null,
                end: null,
            },
            is_default: true,
        });
        const selection = ref(null)

        const start_date_key = computed(() => props.keyPrefix ? `${props.keyPrefix}_start_date` : 'start_date')
        const end_date_key = computed(() => props.keyPrefix ? `${props.keyPrefix}_end_date` : 'end_date')

        const datepickerOptions = reactive({
            attrs: [
                {
                    key: "today",
                    highlight: "red",
                    dates: new Date(),
                },
            ],
        });

        const setDateRange = (range) => {
            selection.value = selection.value == range ? null : range
        }

        const toggleDateRangePicker = () => {
            showDateRangePicker.value = !showDateRangePicker.value
        }

        const setDateFromUrl = () => {
            let searchParams = Object.fromEntries(
                new URLSearchParams(location.search)
            );
            selection.value = searchParams.date_selection
            let start = null;
            let end = null;

            if (searchParams.hasOwnProperty(start_date_key.value)) {
                start = searchParams[start_date_key.value];
            }
            if (searchParams.hasOwnProperty(end_date_key.value)) {
                end = searchParams[end_date_key.value];
            }

            if (searchParams.hasOwnProperty('range_selection')) {
                selection.value = searchParams.range_selection;
            }

            if (start && end) {
                dateForm.date.start = start;
                dateForm.date.end = end;
                dateForm.is_default = false;
            }

            if (start && !end) {
                dateForm.date.start = start;
                dateForm.date.end = start;
                dateForm.is_default = false;
            }

            if (end && !start) {
                dateForm.date.end = end;
                dateForm.date.start = end;
                dateForm.is_default = false;
            }
        };

        setDateFromUrl();

        watch(
            () => [dateForm.date],
            _debounce(function () {
                if (dateForm.date == null || dateForm.date.start == null || dateForm.date.end == null) return

                dateForm.is_default = false;
                selection.value = null
                Inertia.visit(
                    route(props.routeName, {
                        ...buildQueryParams(),
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }, 500),
            { deep: true }
        );

        watch(selection,
            _debounce(function () {
                Inertia.visit(
                    route(props.routeName, {
                        ...buildQueryParams(),
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }, 500),
            { deep: true }
        );

        const formatDate = (date) => {
            return new Date(date).toISOString().slice(0, 10);
        };

        const dayclick = (e) => {

        };

        const buildQueryParams = () => {
            let searchParams = Object.fromEntries(
                new URLSearchParams(location.search)
            );
            searchParams.date_selection = selection.value

            if (!dateForm.is_default) {
                searchParams[start_date_key.value] = formatDate(dateForm.date.start);
                searchParams[end_date_key.value] = formatDate(dateForm.date.end);
            } else {
                if (searchParams.hasOwnProperty(start_date_key.value)) {
                    delete searchParams[start_date_key.value];
                }
                if (searchParams.hasOwnProperty(end_date_key.value)) {
                    delete searchParams[end_date_key.value];
                }
            }
            if (searchParams.hasOwnProperty("page")) {
                delete searchParams["page"];
            }

            return searchParams;
        };



        const clearDate = () => {
            dateForm.date.start = null,
                dateForm.date.end = null,
                compoenentKey.value++;
            dateForm.is_default = true;
            Inertia.visit(
                route(props.routeName, {
                    ...buildQueryParams(),
                }),
                {
                    preserveScroll: true,
                    preserveState: true,
                }
            );

        }

        const open = ref(false)

        const toggleOpen = () => {
            open.value = !open.value
            if (open.value == false) {
                showDateRangePicker.value = false
            }
        }

        const close = () => {
            open.value = false
            showDateRangePicker.value = false
        }

        const changeDateFilter = (dates) => {
            option.filter_dates = dates;
        }

        const filterDateLabel = () => {
            if (dateForm.date && dateForm.date.start && dateForm.date.end) {
                return `<span class="filter_dates">
                ${dayjs(dateForm.date.start).format('DD.MM.YYYY')} - ${dayjs(dateForm.date.end).format('DD.MM.YYYY')}
                </span>
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.4697 6.53033C10.7626 6.82322 11.2374 6.82322 11.5303 6.53033C11.8232 6.23744 11.8232 5.76256 11.5303 5.46967L10.4697 6.53033ZM6 1L6.53033 0.469669C6.23744 0.176776 5.76256 0.176776 5.46967 0.469669L6 1ZM0.46967 5.46967C0.176777 5.76256 0.176777 6.23744 0.46967 6.53033C0.762563 6.82322 1.23744 6.82322 1.53033 6.53033L0.46967 5.46967ZM11.5303 5.46967L6.53033 0.469669L5.46967 1.53033L10.4697 6.53033L11.5303 5.46967ZM5.46967 0.469669L0.46967 5.46967L1.53033 6.53033L6.53033 1.53033L5.46967 0.469669Z" fill="#135F84"/>
                    </svg>
            `
            }
            return `${props.label}
                    ${open.value
                    ? '<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4697 6.53033C10.7626 6.82322 11.2374 6.82322 11.5303 6.53033C11.8232 6.23744 11.8232 5.76256 11.5303 5.46967L10.4697 6.53033ZM6 1L6.53033 0.469669C6.23744 0.176776 5.76256 0.176776 5.46967 0.469669L6 1ZM0.46967 5.46967C0.176777 5.76256 0.176777 6.23744 0.46967 6.53033C0.762563 6.82322 1.23744 6.82322 1.53033 6.53033L0.46967 5.46967ZM11.5303 5.46967L6.53033 0.469669L5.46967 1.53033L10.4697 6.53033L11.5303 5.46967ZM5.46967 0.469669L0.46967 5.46967L1.53033 6.53033L6.53033 1.53033L5.46967 0.469669Z" fill="#787878"/></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" > <path d="M1.53033 0.46967C1.23744 0.176777 0.762563 0.176777 0.46967 0.46967C0.176777 0.762563 0.176777 1.23744 0.46967 1.53033L1.53033 0.46967ZM6 6L5.46967 6.53033C5.76256 6.82322 6.23744 6.82322 6.53033 6.53033L6 6ZM11.5303 1.53033C11.8232 1.23744 11.8232 0.762563 11.5303 0.46967C11.2374 0.176777 10.7626 0.176777 10.4697 0.46967L11.5303 1.53033ZM0.46967 1.53033L5.46967 6.53033L6.53033 5.46967L1.53033 0.46967L0.46967 1.53033ZM6.53033 6.53033L11.5303 1.53033L10.4697 0.46967L5.46967 5.46967L6.53033 6.53033Z" fill="#787878" /> </svg>'}`
        }

        return {
            dateForm,
            dayclick,
            datepickerOptions,
            clearDate,
            compoenentKey,
            open,
            toggleOpen,
            close,
            filterDateLabel,
            setDateRange,
            selection,
            toggleDateRangePicker,
            showDateRangePicker,
        };
    },
};
</script>

<style lang="scss" scoped>
.filter-date-range-picker {
    display: flex;
    justify-content: space-between;


}

.filter-date-range-picker {
    display: flex;
    justify-content: space-between;
    width: 450px;
    background-color: white;
    padding: 10px;
    position: absolute;
    top: 10px;
    right: 0px;
    // border: 1px solid #E2E2E2;
    align-items: center;

    span {
        // width: 50px;
        margin-left: 5px;
        margin-right: 10px;
        color: currentColor;
        font-size: 36px;
        display: flex;
        align-items: center;

        svg {
            fill: currentColor;
            width: 18px;
            height: 18px;
            ;
            stroke: #787878;
            ;
        }
    }

    .clear-btn {
        outline: none;
        border: none;
        text-align: center;
        // background-color: rgb(167, 2, 2);

        font-weight: 700;

        padding: 7px 25px;
        // margin-left: 5px;;
        // border-radius: 5px;
        margin-left: 10px;
        font-family: 'Ropa Sans';


        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
        cursor: pointer;
    }
}
</style>
