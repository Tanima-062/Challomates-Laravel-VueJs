<template>
  <div class="challo__card py-10 px-6 -mt-2">
    <Back :target="route('marketing-fees.index')" :showModal="false" :backPrevUrl="true" />
    <h5 class="page-title mb-8">{{ $t("Marketinggebühr Details") }}</h5>

    <div class="challo__card__body">
      <div class="detail__row gap-0">
        <div class="w-1/3 ">
            <h5 class="detail__label mb-2">{{$t('Erstellungsdatum')}}</h5>
            <span class="detail__value">{{formateDate(marketing_fee.created_at)}}</span>
        </div>
        <div class="w-1/3">
            <h5 class="detail__label mb-2">{{$t('Marketinggebühren-ID')}}</h5>
            <span class="detail__value capitalize">{{marketing_fee.prefix_id}}</span>
        </div>

      </div>
      <div class="detail__row gap-0 mb-[56px]">
          <div class="w-1/3">
              <h4 class="detail__label">{{$t('Bezeichnung')}}</h4>
              <span class="detail__value">{{marketing_fee.designation}}</span>
          </div>
          <div class="w-1/3">
              <h4 class="detail__label">{{$t('Marketinggebühr')}}</h4>
              <span class="detail__value">{{marketing_fee.marketing_fee}}%</span>
          </div>
      </div>
      <div class="field-header mb-10">
         <h3 class="font-semibold text-18xl font-poppins text-primary-3">{{$t('Direkte Konsumenten')}}</h3>
      </div>
      <div class="detail__row gap-0">
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Senior Partner')}}</h4>
            <span class="detail__value">{{marketing_fee.direct_consumers_senior_partner_share}}%</span>
        </div>
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Jackpot')}}</h4>
            <span class="detail__value">{{marketing_fee.direct_consumers_share_jackpot}}%</span>
        </div>
      </div>

      <div class="detail__row gap-0">
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Gebühr CHalloMates')}}</h4>
            <span class="detail__value">{{marketing_fee.direct_consumers_share_fee_challomates}}%</span>
        </div>
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil CHalloMates Marketing AG')}}</h4>
            <span class="detail__value">{{marketing_fee.direct_consumers_share_challomates_marketing_ag}}%</span>
        </div>
      </div>

      <div class="field-header mb-10">
         <h3 class="font-semibold text-18xl font-poppins text-primary-3">{{$t('Vertriebskonsumenten')}}</h3>
      </div>
      <div class="detail__row gap-0">
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Berater')}}</h4>
            <span class="detail__value">{{marketing_fee.distribution_consumers_share_of_consultants}}%</span>
        </div>
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Jackpot')}}</h4>
            <span class="detail__value">{{marketing_fee.distribution_consumers_share_jackpot}}%</span>
        </div>
      </div>

      <div class="detail__row gap-0">
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil Vertriebspartner')}}</h4>
            <span class="detail__value">{{marketing_fee.distribution_consumers_proportion_of_sales_partners}}%</span>
        </div>
        <div class="w-1/3">
            <h4 class="detail__label">{{$t('Anteil CHalloMates Marketing AG')}}</h4>
            <span class="detail__value">{{marketing_fee.distribution_consumers_share_challomates_marketing_ag}}%</span>
        </div>
      </div>
    </div>
    <div class="detail__row">
        <div class="w-1/3 pr-5 mt-6">
            <Link
                class="challo__btn btn-primary btn-block flex items-center justify-center"
                :href="route('marketing-fees.edit', { marketing_fee : marketing_fee.id })"
                v-if="hasPermission('marketing_fee.edit') && contract_count===0"
            >
                <Pen class="mr-2.5" />  Bearbeiten
            </Link>
        </div>
    </div>
  </div>
</template>

<script setup>
import {Link} from "@inertiajs/inertia-vue3";
import Pen from '../../Components/Icons/Pen.vue'
import Back from "../../Components/Form/Back.vue";


const props = defineProps({
  marketing_fee: {
    type: Object,
    required: true,
  },
  contract_count: {
    type: [Number, String],
    required: true,
  },
});
</script>
