<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('sweepstakes.index', { ...buildQueryParams() } )" :backPrevUrl="false" :showModal="false" />
        <h5 class="page-title">Gewinnspiel Details</h5>

        <div class="challo__card__body">
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Erstellungsdatum</h4>
                    <span class="detail__value">{{ formateDate(sweepstake.created_at) }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel-ID</h4>
                    <span class="detail__value">{{ sweepstake.prefix_id }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Status</h4>
                    <span class="detail__value">{{ sweepstake.status }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Name</h4>
                    <span class="detail__value" style="word-break: break-word;">{{ sweepstake.name }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Art </h4>
                    <span class="detail__value">{{ sweepstake.type === 'sweepstake' ? 'Gewinnspiel' : 'Jackpot' }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Laufzeit</h4>
                    <span class="detail__value">{{ formateDate(sweepstake.runtime_from, 'DD.MM.YYYY HH:mm') }} - {{ formateDate(sweepstake.runtime_to, 'DD.MM.YYYY HH:mm') }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Verbleibende Laufzeit</h4>
                    <div class="detail__value">
                        <div class="td break-all flex text-center">

                            <div class="remaining remaning_date">
                                <span class="title !text-center !mb-0">{{ countdown.months }}</span>
                                <span class="subtitle">Monat/e</span>
                            </div>:

                            <div class="remaining remaning_date">
                                <span class="title !text-center !mb-0">{{ countdown.weeks }}</span>
                                <span class="subtitle">Woche/n</span>
                            </div>:

                            <div class="remaining remaning_date">
                                <span class="title !text-center !mb-0">{{ countdown.days }}</span>
                                <span class="subtitle">Tag/e</span>
                            </div>:

                            <div class="remaining remaning_hour">
                                <span class="title !text-center !mb-0">{{ countdown.hours }}</span>
                                <span class="subtitle">Stunde/n</span>
                            </div>:

                            <div class="remaining remaning_minute">
                                <span class="title !text-center !mb-0">{{ countdown.minutes }}</span>
                                <span class="subtitle">Minute/n</span>
                            </div>:

                            <div class="remaining remaning_sedond">
                                <span class="title !text-center !mb-0">{{ countdown.seconds }}</span>
                                <span class="subtitle">Sekunde/n</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Verlosungszeitpunkt</h4>
                    <span class="detail__value">{{ formateDate(sweepstake.raffle_time, 'DD.MM.YYYY &nbsp;&nbsp;&nbsp; HH:mm') }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Preis/e</h4>
                    <span class="detail__value">{{ sweepstake.price }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Total Verlosungszahlstellen</h4>
                    <span class="detail__value">{{ sweepstake.total_sweepstake_number_position }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnzahlstellen</h4>
                    <span class="detail__value">{{ sweepstake.winning_number_position_from }} - {{ sweepstake.winning_number_position_to }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Anzahl Coins für Teilnahme</h4>
                    <span class="detail__value">{{ sweepstake.number_of_coins_for_participation }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3 pr-5">
                    <Link
                        class="challo__btn btn-primary btn-block flex items-center justify-center"
                        :href="route('sweepstakes.edit', { sweepstake : sweepstake.id, ...buildQueryParams() })"
                        v-if="hasPermission('sweepstakes.edit') && (sweepstake.status === 'new' || sweepstake.published_status === 'not_published' )"
                    >
                        <Pen class="mr-2.5" />  Bearbeiten
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";
import Back from "../../Components/Form/Back.vue";
import Pen from '../../Components/Icons/Pen.vue'
import dayjs from "dayjs";
import {reactive} from "@vue/reactivity";
import {onMounted} from "vue";

const props = defineProps({
    sweepstake: {
        type: Object,
        required: true,
    },
});

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

const timeDifference = (start_time, end_time, unit) => {
    const date1 = start_time;
    const date2 = dayjs(end_time);
    let diff = 0;
    //let diff = date2.diff(date1, 'day', true);
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

onMounted( () => {
    let interval = setInterval(() => {
        countdown.seconds = timeDifference(new Date(), props.sweepstake.runtime_to, 'second');
        countdown.minutes = timeDifference(new Date(), props.sweepstake.runtime_to, 'minute');
        countdown.hours = timeDifference(new Date(), props.sweepstake.runtime_to, 'hour');
        countdown.days = timeDifference(new Date(), props.sweepstake.runtime_to, 'day');
        countdown.weeks = timeDifference(new Date(), props.sweepstake.runtime_to, 'week');
        countdown.months = timeDifference(new Date(), props.sweepstake.runtime_to, 'month');
    }, 1000 );
} );

</script>

<style lang="scss" scoped>
.remaining {
    display: flex;
    flex-direction: column;

    .title {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135F84;
        display: block;
    }

    .subtitle {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}
</style>
