<template>
    <div class="modal_booster flex flex-col" :class="attrs['class']" v-bind:style="attrs['style']">
        <h5 class="text-center title">
          {{ title }}
        </h5>
        <p class="text-center message">
            Für den Vertriebspartner “{{ sales_partner.company_name }}” ist/sind folgende/r Booster bereits vorhanden:
        </p>
        <div class="mt-5 overflow-y-auto h-24">
            <table>
                <thead>
                    <tr>
                        <th class="text-left title w-1/4">Titel</th>
                        <th class="text-left title w-1/4">Boosterart</th>
                        <th class="text-left title w-2/4">Laufzeit</th>
                        <th class="w-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-y-2 hover:font-semibold" v-for="booster in boosters" :key="booster.id">
                        <td class="w-1/4 border-spacing-2">{{booster.title}}</td>
                        <td class="w-1/4 border-spacing-2">{{booster.type=='Recurring' ? 'Wiederkehrend' : 'Einmalig'}}</td>
                        <td class="w-2/4 border-spacing-2" v-if="booster.type=='One Time' && booster?.posting_time">{{ formateDate(booster?.posting_time) }}</td>
                        <td class="w-2/4 border-spacing-2" v-else-if="booster.type=='Recurring' && booster?.start">{{ formateDate(booster?.start) }} -  {{formateDate(booster?.end)}}</td>
                        <td class="w-2/4 border-spacing-2" v-else></td>
                        <td class="w-2">
                            <a target="_blank" :href="route('boosters.show', {booster: booster.id})">
                                <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 7L17.5303 7.53032C17.7448 7.31582 17.809 6.99324 17.6929 6.71298C17.5768 6.43273 17.3033 6.25 17 6.25V7ZM1 6.25C0.585786 6.25 0.25 6.58579 0.25 7C0.25 7.41421 0.585786 7.75 1 7.75L1 6.25ZM13.4697 4.53033C13.7626 4.82322 14.2374 4.82322 14.5303 4.53033C14.8232 4.23744 14.8232 3.76256 14.5303 3.46967L13.4697 4.53033ZM11.5303 0.46967C11.2374 0.176777 10.7626 0.176777 10.4697 0.46967C10.1768 0.762563 10.1768 1.23744 10.4697 1.53033L11.5303 0.46967ZM10.4698 12.4697C10.1769 12.7626 10.1769 13.2374 10.4698 13.5303C10.7627 13.8232 11.2376 13.8232 11.5305 13.5303L10.4698 12.4697ZM17 6.25L1 6.25L1 7.75L17 7.75V6.25ZM14.5303 3.46967L11.5303 0.46967L10.4697 1.53033L13.4697 4.53033L14.5303 3.46967ZM11.5305 13.5303L17.5303 7.53032L16.4697 6.46968L10.4698 12.4697L11.5305 13.5303Z" fill="#135F84"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-center mt-4">Möchten Sie diesen neuen Booster trotzdem erstellen?</p>
        <div class="btns flex justify-between">
            <button class="yes challo__btn btn-success btn-block  w-1/2 mr-4" @click="$emit('yesClick')">
                {{ yes_btn_text }}
            </button>
            <button class="no challo__btn btn-outline-primary  btn-block w-1/2 ml-4" style="color: #44B8F1" @click="$emit('noClick')">
                {{ no_btn_text }}
            </button>
        </div>
    </div>
  </template>
<script setup>
import { useAttrs } from "@vue/runtime-core";
const attrs = useAttrs();

defineProps({
  title: {
    type: String,
    required: false,
    default: "Bereits vorhandene/r Booster",
  },
  yes_btn_text: {
    type: String,
    required: false,
    default: "Ja"
  },

  no_btn_text: {
    type: String,
    required: false,
    default: "Nein",
  },
  boosters:{
    type:[Array, Object],
    required: true
  },
  sales_partner:{
    type: Object,
    required: true
  }

});
</script>
<script>
    export default {
        inheritAttrs: false,
    };
</script>
<style lang="scss" scoped>
    .challo__modal .overlay{
        position: absolute;
    }

    .modal_booster{
        width: 600px;
        padding: 32px;
        background-color: #fff;
        box-shadow: 0 4px 4px rgb(0 0 0 / 10%);
        height: fit-content;
        border-radius: 24px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border: 1px solid #FFBA49;
        z-index: 1;
    }

    .title {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 600;
        font-size: 18px;
        line-height: 27px;
        color: #1AA1E4;
        margin-bottom: 20px;
    }

    .description {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        // display: flex;
        // align-items: center;
        // text-align: center;
        color: #135F84;
        text-align: center;
    }

    .btns {
        margin-top: 32px;
    }
    .content {
        .td {
            font-family: 'Poppins';
            font-style: normal;
            // font-weight: 400;
            font-size: 16px;
            line-height: 24px;
            color: #135F84;
            font-weight: inherit;
        }
    }

</style>
