<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold">
        <div class="content">
            <div
                class="td"
                style="width: 16%"
            >
                <Link
                    style="word-break: break-word;"
                    :href="route('sweepstakes.show', { sweepstake : sweepstake.id, ...buildQueryParams() })"
                >
                {{ sweepstake.name }}
                </Link>
            </div>
            <div
                class="td"
                style="width: 10%"
            >
                <span>
                    {{ sweepstake.type === 'sweepstake' ? 'Gewinnspiel' : 'Jackpot' }}
                </span>
            </div>
            <div
                class="td"
                style="width: 22%"
            >
                {{ formateDate( sweepstake.runtime_from, 'DD.MM.YYYY HH:mm') }} - {{ formateDate( sweepstake.runtime_to,
                'DD.MM.YYYY HH:mm')}}
            </div>
            <div
                class="td flex text-center"
                style="width: 27%"
            >
                <div class="remaining remaning_date w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.months }}</span>
                    <span class="subtitle">Monat/e</span>
                </div>:
                <div class="remaining remaning_date w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.weeks }}</span>
                    <span class="subtitle">Woche/n</span>
                </div>:
                <div class="remaining remaning_date w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.days }}</span>
                    <span class="subtitle">Tag/e</span>
                </div>:
                <div class="remaining remaning_hour w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.hours }}</span>
                    <span class="subtitle">Stunde/n</span>
                </div>:
                <div class="remaining remaning_minute w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.minutes }}</span>
                    <span class="subtitle">Minute/n</span>
                </div>:
                <div class="remaining remaning_sedond w-[50px]">
                    <span class="title !text-center !mb-0">{{ countdown.seconds }}</span>
                    <span class="subtitle">Sekunde/n</span>
                </div>

            </div>
            <div
                class="td"
                style="width: 15%"
            >
                {{ formateDate( sweepstake.raffle_time, 'DD.MM.YYYY HH:mm')}}
            </div>
            <div
                class="td"
                style="width: 6%"
            >
                <SweepstakeStatus
                    :status="sweepstake.status"
                    :published="sweepstake.published_status"
                />
            </div>
            <div
                class="td text-center"
                style="width: 4%"
            >
                <button
                    class="relative"
                    type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    @click="showDropdown = true"
                >
                    <menu-bar />
                    <div
                        class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm "
                        v-click-away="() => (showDropdown = false)"
                        v-show="showDropdown"
                    >
                        <Link
                            class="dropdown-item"
                            :href="route('sweepstakes.show', { sweepstake : sweepstake.id, ...buildQueryParams() })"
                            v-if="hasPermission('sweepstakes.view')"
                        >
                        Details ansehen
                        </Link>

                        <Link
                            class="dropdown-item"
                            :backUrl="{ sweepstake : sweepstake.id, ...buildQueryParams() }"
                            :href="route('sweepstakes.edit', { sweepstake : sweepstake.id, ...buildQueryParams() })"
                            v-if="hasPermission('sweepstakes.edit') && (sweepstake.status === 'new' || sweepstake.published_status === 'not_published' )"
                        >
                        Bearbeiten
                        </Link>

                        <CancelOrPublishSweepstakesStatus
                            @click.stop="showDropdown = false"
                            :staticBackdrop='true'
                            :value='sweepstake.id'
                            :attributes='{ sweepstake_name: sweepstake.name }'
                            label='Abbrechen'
                            class='dropdown-item'
                            routeName='sweepstakes.cancel'
                            canceled_message='Sind Sie sicher, dass Sie das Gewinnspiel ":sweepstake_name" wirklich abbrechen wollen?'
                            title="Gewinnspiel abbrechen?"
                            v-if="hasPermission('sweepstakes.edit') && (sweepstake.status === 'new' || (sweepstake.status === 'active' && sweepstake.published_status === 'not_published') )"
                        />

                        <CancelOrPublishSweepstakesStatus
                            @click.stop="showDropdown = false"
                            :staticBackdrop='true'
                            :value='sweepstake.id'
                            :attributes='{ sweepstake_name: sweepstake.name }'
                            :showError='true'
                            label='Publizieren'
                            class='dropdown-item'
                            routeName='sweepstakes.publish'
                            canceled_message='Sind Sie sicher, dass Sie das Gewinnspiel ":sweepstake_name" wirklich publizieren wollen und dies dadurch für alle Mobile App-Benutzer ersichtlich ist?'
                            title="Gewinnspiel publizieren?"
                            v-if="hasPermission('sweepstakes.edit') && (sweepstake.status === 'new' && sweepstake.published_status === 'not_published' )"
                        />

                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { trans } from "laravel-vue-i18n";
import { computed, reactive, ref, } from "@vue/reactivity";
import SweepstakeStatus from './SweepstakeStatus.vue'
import CancelOrPublishSweepstakesStatus from "./CancelOrPublishSweepstakesStatus";
import Confirmation from './../../../Components/Modal/Content/Confirmation.vue'
import { Inertia } from "@inertiajs/inertia";
import { inject, onMounted } from 'vue'
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc"
import timezone from "dayjs/plugin/timezone"
import customParseFormat from "dayjs/plugin/customParseFormat"
dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(customParseFormat)

const modal = inject("modal");
const props = defineProps({
    sweepstake: {
        type: Object,
        required: true,
    }
});

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

const showDropdown = ref(false);

const timeDifference = (start_time, end_time, unit) => {
    const date1 = dayjs(start_time);
    const date2 = dayjs(end_time);
    let diff = 0;

    let totalSeconds = date2.diff(date1, 'second');
    let totalDays, totalMinutes, leftSeconds, months, weeks, days, hours, minutes, seconds;
    totalDays = totalMinutes = months = weeks = days = hours = minutes = seconds = 0;

    if (totalSeconds < 1) {
        return '00';
    }

    totalDays = Math.floor(((totalSeconds / 60) / 60) / 24);
    months = Math.floor(totalDays / 30);
    weeks = Math.floor((totalDays % 30) / 7);
    days = ((totalDays % 30) % 7);
    leftSeconds = (totalSeconds - (((((months * 30) + (weeks * 7) + days) * 24) * 60) * 60));
    totalMinutes = Math.floor(leftSeconds / 60);
    hours = Math.floor(totalMinutes / 60);
    minutes = totalMinutes % 60;

    if (unit === 'month') {
        diff = months;
    }

    if (unit === 'week') {
        diff = weeks;
    }

    if (unit === 'day') {
        diff = days;
    }

    if (unit === 'hour') {
        diff = hours;
    }

    if (unit === 'minute') {
        diff = minutes;
    }

    if (unit === 'second') {
        diff = Math.floor(leftSeconds - (totalMinutes * 60));
    }

    if (diff < 10) {
        diff = `0${diff}`
    }

    return diff;
};

let countdown = reactive(
    {
        seconds: '00',
        minutes: '00',
        hours: '00',
        days: '00',
        weeks: '00',
        months: '00',
    }
);

onMounted(() => {
    let interval = setInterval(() => {
        countdown.seconds = timeDifference(new Date(), props.sweepstake.runtime_to, 'second');
        countdown.minutes = timeDifference(new Date(), props.sweepstake.runtime_to, 'minute');
        countdown.hours = timeDifference(new Date(), props.sweepstake.runtime_to, 'hour');
        countdown.days = timeDifference(new Date(), props.sweepstake.runtime_to, 'day');
        countdown.weeks = timeDifference(new Date(), props.sweepstake.runtime_to, 'week');
        countdown.months = timeDifference(new Date(), props.sweepstake.runtime_to, 'month');
    }, 1000);
});

</script>

<style lang="scss" scoped>
.content {
    .td {
        font-family: 'Poppins', serif;
        font-style: normal;
        // font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #135F84;
        font-weight: inherit;
    }
}

.dropdown {
    right: 0;
    top: 20px;
    width: max-content;

    .dropdown-item {
        border-bottom: 1px solid #8ED5F6;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 30px;
        font-family: 'Poppins', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        color: #135F84;
        text-align: left;

        &:hover {
            font-weight: 600;
        }

        &:last-child {
            border-bottom: none;
        }
    }
}

.remaining {
    display: flex;
    flex-direction: column;

    .title {
        font-family: 'Poppins', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135F84;
        display: block;
    }

    .subtitle {
        font-family: 'Poppins', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}
</style>
