<template>
    <div class="challo__card py-10 px-6 -mt-2">
        <Back :target="route('coins-credit.index')" :showModal="false" :backPrevUrl="true" />
        <h5 class="page-title mb-8">Gutschriftdetails</h5>

        <div class="challo__card__body">
            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Mobile App-Benutzer</h4>
                    <span class="detail__value">
                        <a target="_blank" class="underline underline-offset-2"
                            :href="route('mobile-app-users.show', { mobile_app_user: credit.mobile_app_user_id })">
                            {{ credit.mobile_app_user.first_name }} {{ credit.mobile_app_user.last_name }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Benutzer-ID</h4>
                    <span class="detail__value">
                        {{ credit.mobile_app_user.prefix_id }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Store</h4>
                    <span class="detail__value">
                        <a target="_blank" class="underline underline-offset-2"
                            :href="route('sales-partner.show', { sales_partner: credit.sales_partner_id })">
                            {{ credit.sales_partner.company_name }}
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Vertriebspartner-ID</h4>
                    <span class="detail__value">
                        {{ credit.sales_partner.prefix_id }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Vertrags-ID</h4>
                    <span class="detail__value">
                        <a target="_blank" class="underline underline-offset-2"
                            v-if="credit.contract"
                            :href="route('contract.show', { contract: credit.contract.id })">
                            {{ credit.contract.prefix_id }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Paket-ID</h4>
                    <span class="detail__value">
                        <a target="_blank" class="underline underline-offset-2"
                            v-if="credit.contract"
                            :href="route('package.show', { package: credit.contract.package.id })">
                            {{ credit.contract.package.package_prefix_id }}
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Coin Faktor</h4>
                    <span class="detail__value">
                    {{credit.contract.package.coin_factor.toFixed(2)}}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Aufenthalt</h4>
                    <span class="detail__value">
                        {{ dayjs(credit.check_in_time).format('DD.MM.YYYY HH:mm') }}
                        -
                        {{ dayjs(credit.check_out_time).format('DD.MM.YYYY HH:mm') }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Check-in ID</h4>
                    <span class="detail__value">
                        {{ credit.check_in_prefix_id }}
                    </span>
                </div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Check-out ID</h4>
                    <span class="detail__value">
                        {{ credit.check_out_prefix_id }}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Umsatz</h4>
                    <span class="detail__value">
                        CHF {{  parseFloat(credit.turnover) }}
                    </span>
                </div>
                <div class="w-1/3"></div>
            </div>

            <div class="detail__row gap-0">
                <div class="w-1/3">
                    <h4 class="detail__label">Umsatz-Coins</h4>
                    <span class="detail__value">{{credit.posting_coins}}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Posting-Coins</h4>
                    <span class="detail__value">
                        {{credit.coin}}
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Total Coin</h4>
                    <span class="detail__value">
                        {{ credit.total_coins }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Back from '../../../../Components/Form/Back.vue';
import dayjs from 'dayjs';

const props = defineProps({
    credit: {
        type: Object,
        required: true,
    },
});
</script>
