<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('participation.index', { ...buildQueryParams() } )" :backPrevUrl="false" :showModal="false" />
        <h5 class="page-title">Teilnahmedetails</h5>

        <div class="challo__card__body">
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Zeitpunkt</h4>
                    <span class="detail__value">{{ formateDate(participation.created_at, 'DD.MM.YYYY HH:mm' ) }} Uhr</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Teilnehmer</h4>
                    <span class="detail__value underline">
                        <a :href="route('mobile-app-users.show', { mobile_app_user: participation.mobile_app_user_id })" target="_blank" rel="noopener noreferrer">
                            <span style="word-break: break-word;">
                                {{ participation.participant }}
                            </span>
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Benutzer-ID</h4>
                    <span class="detail__value">{{ participation.mobile_app_users_prefix_id }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel</h4>
                    <span class="detail__value underline">
                        <a :href="route('sweepstakes.show', { sweepstake : participation.sweepstake_id, ...buildQueryParams() })" target="_blank" rel="noopener noreferrer">
                            <span style="word-break: break-word;">
                                {{ participation.sweepstake_name }}
                            </span>
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel-ID</h4>
                    <span class="detail__value">
                        <a :href="route('sweepstakes.show', { sweepstake : participation.sweepstake_id, ...buildQueryParams() })" target="_blank" rel="noopener noreferrer">
                            <span>
                                {{ participation.sweepstake_prefix_id }}
                            </span>
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Art</h4>
                    <span class="detail__value">{{ sweepstake_types[participation.sweepstake_type] }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Laufzeit </h4>
                    <span class="detail__value">{{ formateDate( participation.sweepstakes_runtime_from, 'DD.MM.YYYY HH:mm' ) }} - {{ formateDate( participation.sweepstakes_runtime_to, 'DD.MM.YYYY HH:mm' ) }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Verlosungszeitpunkt</h4>
                    <span class="detail__value">{{ formateDate(participation.sweepstakes_raffle_time, 'DD.MM.YYYY &nbsp;&nbsp;&nbsp; HH:mm') }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel Status</h4>
                    <span class="detail__value">{{ sweepstake_status[participation.status_with_published_status] }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive} from "@vue/reactivity";
import { Link } from "@inertiajs/inertia-vue3";
import Back from "../../Components/Form/Back.vue";
const props = defineProps({
    participation: {
        type: Object,
        required: true,
    },
    sweepstake_types: {
        type: Object,
        default: {
            'sweepstake' : 'Gewinnspiel',
            'jackpot' : 'Jackpot',
        }
    },
    sweepstake_status: {
        type: Object,
        default: {
            'active_published' : 'Aktiv (Publiziert)',
            'finished_published' : 'Beendet (Publiziert)',
            'drawn_published' : 'Ausgelost (Drawn)',
            'drawn_not_published' : 'Ausgelost (Drawn)',
        }
    }
});

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

</script>

<style lang="scss" scoped>

</style>
