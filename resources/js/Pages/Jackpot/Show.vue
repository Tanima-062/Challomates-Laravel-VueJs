<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('jackpot.index')" :showModal="false" />
        <h5 class="page-title">Jackpotbeitrag Details</h5>

        <div class="challo__card__body">
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel</h4>
                    <span class="detail__value underline">Jackpot 1</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Check-out Zeitpunkt</h4>
                    <span class="detail__value">{{ formateDate(jackpot_contribution.check_out_time, 'DD.MM.YYYY&nbsp;&nbsp;&nbsp;HH:mm') }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Check-out-ID</h4>
                    <span class="detail__value">C{{ jackpot_contribution.store_visit_prefix_id }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Mobile App-Benutzer</h4>
                    <span class="detail__value underline">
                        <a :href="route('mobile-app-users.show', { mobile_app_user: jackpot_contribution.mobile_app_user_id })" target="_blank" rel="noopener noreferrer">
                            <span style="word-break: break-word;">
                                {{ jackpot_contribution.mobile_app_user_full_name }}
                            </span>
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Benutzer-ID</h4>
                    <span class="detail__value">{{ jackpot_contribution.mobile_app_user_prefix_id }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Store</h4>
                    <span class="detail__value underline">
                        <a :href="route('sales-partner.show', { sales_partner: jackpot_contribution.store_id })" target="_blank" rel="noopener noreferrer">
                            <span style="word-break: break-word;">
                                {{ jackpot_contribution.store_name }}
                            </span>
                        </a>
                    </span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Vertriebspartner-ID</h4>
                    <span class="detail__value">{{ jackpot_contribution.distributor_id }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Vertrags-ID</h4>
                    <span class="detail__value">{{ jackpot_contribution.contract_prefix_id }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Marketinggebühren-ID</h4>
                    <span class="detail__value">{{ jackpot_contribution.marketing_fees_prefix_id }}</span>
                </div>
            </div>
            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Umsatz</h4>
                    <span class="detail__value">CHF {{ ( parseFloat(jackpot_contribution.turn_over) || 0 ).toFixed(2) }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Anteil Jackpot</h4>
                    <span class="detail__value">{{ ( jackpot_contribution.mobile_app_user_type === 'direct_consumer' ) ? ( parseFloat( jackpot_contribution.direct_consumers_share_jackpot ) || 0 ).toFixed(2) : ( parseFloat( jackpot_contribution.distribution_consumers_share_jackpot ) || 0 ).toFixed(2) }}%</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Jackpot Beitrag</h4>
                    <span class="detail__value">CHF {{ ( parseFloat( jackpot_contribution.jackpot_share ) || 0 ).toFixed(2) }}</span>
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
    jackpot_contribution: {
        type: Object,
        required: true,
    },
});

</script>

<style lang="scss" scoped>

</style>
