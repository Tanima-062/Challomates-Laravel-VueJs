<template>
    <div class="challo__card py-10 px-6 -mt-2">
        <Back :target="route('store-visits.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-12">Store-Besuchdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row mb-10 gap-0">

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Besucher
                    </h5>
                    <span class="detail__value underline underline-offset-2">
                        <a target="_blank" :href="route('mobile-app-users.show', {mobile_app_user: store_visit.mobile_app_user_id})">{{ store_visit.mobile_app_user.first_name }} {{ store_visit.mobile_app_user.last_name }}</a>
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Benutzer-ID
                    </h5>
                    <span class="detail__value">
                        {{ store_visit.mobile_app_user.prefix_id }}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Store
                    </h5>
                    <span class="detail__value underline underline-offset-2">
                        <a target="_blank" :href="route('sales-partner.show', {'sales_partner': store_visit.sales_partner_id})">{{ store_visit.sales_partner.company_name }}</a>
                    </span>
                </div>

            </div>

            <div class="detail__row mb-10 gap-0">

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Vertriebspartner-ID
                    </h5>
                    <span class="detail__value">
                        {{ store_visit.sales_partner.prefix_id }}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Check-in Zeitpunkt
                    </h5>
                    <span class="detail__value">
                        {{ dayjs(store_visit.check_in_time).format("MM.DD.YYYY &nbsp; &nbsp; &nbsp; HH:mm") }}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Check-in ID
                    </h5>
                    <span class="detail__value">
                    {{store_visit.check_in_prefix_id}}
                    </span>
                </div>

            </div>

            <div class="detail__row mb-10 gap-0">

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Check-out Zeitpunkt
                    </h5>
                    <span class="detail__value">
                        {{
                                store_visit.check_out_time ? dayjs(store_visit.check_out_time).format("MM.DD.YYYY &nbsp; &nbsp; &nbsp; HH:mm") : ''
                        }}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Check-out ID
                    </h5>
                    <span class="detail__value">
                    {{store_visit.check_out_prefix_id}}
                    </span>
                </div>

                <div class="w-1/3 flex flex-col items-start">
                    <h5 class="detail__label mb-3">
                        Umsatz
                    </h5>
                    <span class="detail__value">
                        CHF {{ store_visit.turnover }}
                    </span>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import dayjs from 'dayjs';
import Back from '../../Components/Form/Back.vue';

const props = defineProps({
    store_visit: {
        type: Object,
        required: true,
    }
})
</script>
