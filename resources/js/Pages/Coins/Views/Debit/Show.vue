<template>
    <div class="challo__card py-10 px-6 -mt-2">
        <Back :target="route('coins-debit.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-8">Belastungsdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Zeitpunkt</h4>
                    <span class="detail__value">
                        {{  dayjs(debit.participated_at).format('DD.MM.YYYY HH:mm')  }} Uhr
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Mobile App-Benutzer</h4>
                    <span class="detail__value">
                        <a class="underline underline-offset-2" target="_blank"
                            :href="route('mobile-app-users.show', { mobile_app_user: debit.mobile_app_user_id })">
                            {{  `${debit.mobile_app_user.first_name} ${debit.mobile_app_user.last_name}`  }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Benutzer-ID</h4>
                    <span class="detail__value">{{  debit.mobile_app_user.prefix_id  }}</span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Beschreibung</h4>
                    <span class="detail__value">{{debit.sweepstake.description}}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Teilnahme ID</h4>
                    <span class="detail__value">
                        <a class="underline underline-offset-2" target="_blank"
                            :href="route('participation.show', { participation: debit.id })">
                            {{  debit.prefix_id  }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel/e</h4>
                    <span class="detail__value">
                        <a class="underline underline-offset-2"
                            :href="route('sweepstakes.show', { sweepstake: debit.sweepstake_id })">
                            {{  debit.sweepstake.name  }}
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Anzahl Teilnahmen</h4>
                    <span class="detail__value">
                        {{  debit.total_participations  }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Coins pro Teilnahme</h4>
                    <span class="detail__value">
                        {{  debit.sweepstake.number_of_coins_for_participation  }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Total Coins</h4>
                    <span class="detail__value">
                        {{  debit.total_coins.toFixed(2)  }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Back from '../../../../Components/Form/Back.vue';
import dayjs from 'dayjs';

const props = defineProps({
    debit: {
        type: Object,
        required: true,
    },
});
</script>
