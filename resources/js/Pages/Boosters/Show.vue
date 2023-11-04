<template>
  <div class="challo__card py-10 px-6 -mt-2">
    <Back :target="route('marketing-fees.index')" :showModal="false" :backPrevUrl="true" />
    <h5 class="page-title mb-8">{{ $t("Boosterdetails") }}</h5>

    <div class="challo__card__body">
        <div class="detail__row gap-0">
            <div class="w-1/3 ">
                <h5 class="detail__label mb-2">{{$t('Erstellungsdatum')}}</h5>
                <span class="detail__value">{{formateDate(booster.created_at)}}</span>
            </div>
            <div class="w-1/3">
                <h5 class="detail__label mb-2">{{$t('Booster-ID')}}</h5>
                <span class="detail__value capitalize">{{booster.prefix_id}}</span>
            </div>
        </div>
        <div class="detail__row gap-0 mb-[56px]">
          <div class="w-1/3">
              <h4 class="detail__label">{{$t('Vertriebspartner')}}</h4>
               <a :href="route('sales-partner.show', {sales_partner: booster.sales_partner_id})" target="_blank"><span class="detail__value underline underline-offset-4">{{booster.sales_partner.company_name}}</span></a>
          </div>
          <div class="w-1/3">
              <h4 class="detail__label">{{$t('Vertrag')}}</h4>
              <a :href="route('contract.show', {contract: booster.contract_id})" target="_blank"><span class="detail__value underline underline-offset-4">{{booster.contract.name}}</span></a>
          </div>
        </div>
        <div class="detail__row gap-4">
            <div class="w-1/3">
                <h4 class="detail__label">{{$t('Titel')}}</h4>
                <span class="detail__value">{{booster.title}}</span>
            </div>
            <div class="w-1/3">
                <h4 class="detail__label" v-if="booster.body">{{$t('Text')}}</h4>
                <span class="detail__value">{{booster.body}}</span>
            </div>
            <div class="w-1/3 flex flex-col items-start">
                <h5 class="detail__label mb-4">{{$t('Bild')}}</h5>
                <span
                    class="detail__value flex gap-3 cursor-pointer"
                    v-if="booster.file"
                    @click="showReceiptTemplate"
                >
                    <Image />
                    <span> Bild ansehen </span>
                </span>
                </div>
            </div>

        <div class="detail__row gap-0">
            <div class="w-1/3">
                <h4 class="detail__label">{{$t('Reichweite')}}</h4>
                <span class="detail__value">{{booster.range.toFixed(2)}} Km</span>
            </div>
            <div class="w-1/3">
                <h4 class="detail__label">{{$t('Boosterart')}}</h4>
                <span class="detail__value">{{booster?.type == 'Recurring' ? 'Wiederkehrend' : 'Einmalig'}}</span>
            </div>
            <div class="w-1/3" v-if="booster?.type == 'One Time'">
                <h4 class="detail__label">{{$t('Postingzeitpunkt ')}}</h4>
                <span class="detail__value">{{formateDate(booster?.posting_time, 'DD.MM.YYYY HH:mm')}}</span>
            </div>
            <div class="w-1/3" v-if="booster?.type == 'Recurring'">
                <h4 class="detail__label">{{$t('Anzahl Booster / Monat')}}</h4>
                <span class="detail__value">{{booster?.booster_booster_types[0]?.number_of_boosters_month}}</span>
            </div>
        </div>

        <div class="detail__row gap-0" v-if="booster?.type == 'Recurring' && booster?.booster_booster_types[0]?.number_of_boosters_month==1">
            <div class="w-full">
                <h4 class="detail__label">{{$t('Jeweiliger Postingzeitpunkt')}}</h4>
                <span class="detail__value">Am {{ getWeek(booster?.booster_booster_types[0]?.week) }} {{ getWeekday(booster?.booster_booster_types[0]?.weekday) }} um {{ getTime(booster?.booster_booster_types[0]?.time)}} Uhr jeden Monat</span>
            </div>
        </div>

        <div class="field-header mb-10" v-if="booster?.type == 'Recurring' && booster?.booster_booster_types[0]?.number_of_boosters_month > 1">
            <h3 class="font-semibold text-18xl font-poppins text-primary-3">{{$t('Postingzeitpunkte')}}</h3>
        </div>
        <div v-if="booster?.type == 'Recurring' && booster?.booster_booster_types[0]?.number_of_boosters_month > 1">
            <div v-for = "(item,index) in booster?.booster_booster_types[0]?.number_of_boosters_month" :key="index">
                <div class="detail__row gap-0 space-y-4" v-if="index % 3 === 0">
                    <div class="w-1/3" v-if="booster?.booster_booster_types[index]?.week">
                        <h4 class="detail__label">{{index+1}}. Booster</h4>
                        <span class="detail__value">Am {{ getWeek(booster?.booster_booster_types[index]?.week) }} {{ getWeekday(booster?.booster_booster_types[index]?.weekday) }} um {{ getTime(booster?.booster_booster_types[index]?.time)}} Uhr jeden Monat</span>
                    </div>
                    <div class="w-1/3" v-if="booster?.booster_booster_types[index+1]?.week">
                        <h4 class="detail__label">{{index+2}}. Booster</h4>
                        <span class="detail__value">Am {{ getWeek(booster?.booster_booster_types[index+1]?.week) }} {{ getWeekday(booster?.booster_booster_types[index+1]?.weekday) }} um {{ getTime(booster?.booster_booster_types[index+1]?.time)}} Uhr jeden Monat</span>
                    </div>
                    <div class="w-1/3" v-if="booster?.booster_booster_types[index+2]?.week">
                        <h4 class="detail__label">{{index+3}}. Booster</h4>
                        <span class="detail__value">Am {{ getWeek(booster?.booster_booster_types[index+2]?.week) }} {{ getWeekday(booster?.booster_booster_types[index+2]?.weekday) }} um {{ getTime(booster?.booster_booster_types[index+2]?.time)}} Uhr jeden Monat</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="field-header mt-5 mb-5" v-if="booster?.type == 'Recurring'">
            <h3 class="font-semibold text-18xl font-poppins text-primary-3">{{$t('Laufzeit')}}</h3>
        </div>
        <div class="detail__row gap-0" v-if="booster?.type == 'Recurring'">
            <div class="w-1/3">
                <h4 class="detail__label">{{$t('Beginn')}}</h4>
                <span class="detail__value">{{formateDate(booster?.start)}}</span>
            </div>
            <div class="w-1/3">
                <h4 class="detail__label">{{$t('Endet am')}}</h4>
                <span class="detail__value">{{formateDate(booster?.end)}}</span>
            </div>
        </div>
    </div>
    <div class="detail__row">
        <div class="w-1/3 pr-5 mt-6">
            <a
                class="challo__btn btn-primary btn-block flex items-center justify-center"
                :href="route('boosters.edit', { booster : booster.id })"
                v-if="hasPermission('booster.edit')"
                @click.prevent="editAlert(booster)"
            >
                <Pen class="mr-12" />  Bearbeiten
            </a>
        </div>
    </div>
  </div>
</template>

<script setup>
import Pen from '../../Components/Icons/Pen.vue'
import Back from "../../Components/Form/Back.vue";
import { inject } from "@vue/runtime-core";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../../Components/Modal/Content/Confirmation.vue";
import Image from "../../Components/Icons/Image.vue";
import ViewImage from "../../Components/Modal/Content/ViewImage.vue";

const modal = inject('modal')

const props = defineProps({
  booster: {
    type: Object,
    required: true,
  },
});

const getTime = (time) =>{
    var times = time.split(':');
    return times[0]+":"+times[1];
}

const getWeek = (week) => {
    if(week == '1st'){
        return 'Ersten';
    }else if (week == '2nd'){
        return 'Zweiten';
    }else if (week == '3rd'){
        return 'Dritten';
    }else if (week == '4th'){
        return 'Vierten';
    }else if (week == 'last'){
        return 'Letzten'
    }
}

const getWeekday = (weekday) => {
    if(weekday == 'Monday'){
        return 'Montag';
    }else if (weekday == 'Tuesday'){
        return 'Dienstag';
    }else if (weekday == 'Wednesday'){
        return 'Mittwoch';
    }else if (weekday == 'Thursday'){
        return 'Donnerstag';
    }else if (weekday == 'Friday'){
        return 'Freitag'
    }else if (weekday == 'Saturday'){
        return 'Samstag'
    }else if (weekday == 'Sunday'){
        return 'Sonntag'
    }
}

const editAlert = (booster) => {
    if(booster.status == 'active'){
        modal.show(Confirmation, {
            props: {
                message: `Aktiven Booster ändern?`,
                text: `Dieser Booster ist aktiv. Sind Sie sicher, dass Sie diesen Booster trotzdem ändern wollen und bestätigen damit, dass der Vertriebspartner darüber informiert und ein entsprechender Vertrag vorhanden ist oder die
                        Änderung keinen Einfluss auf den Vertriebspartner und den Vertrag haben?`,
            },
            events: {
                yesClick: () => {
                    Inertia.get(route('boosters.edit', { booster: props.booster.id })); modal.hide()
                },
                noClick: () => modal.hide(),
            },
        });
    }else{
        Inertia.get(route('boosters.edit', { booster: props.booster.id })); modal.hide()
    }
}

const showReceiptTemplate = () => {
  modal.show(ViewImage, {
    props: {
      image: props.booster.file_url,
    },
  });
};
</script>
