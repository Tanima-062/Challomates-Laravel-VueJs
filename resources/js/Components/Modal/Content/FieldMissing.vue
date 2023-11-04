<template>
  <div class="modal__missing-field">
      <h5 class="text-center title">
        {{ title }}
      </h5>
      <p class="text-center message" v-html="description"></p>
      <div class="flex items-center justify-center mt-8">
          <div class="buttons">
            <button
            class="btn-block challo__btn btn-success"
            @click="$emit('hide')"
            >
            {{ "OK" }}
            </button>
          </div>
      </div>
  </div>
</template>

<script setup>
import { computed } from "@vue/runtime-core";
import { trans } from "laravel-vue-i18n";
const props = defineProps({
  title: {
    type: String,
    required: false,
    default: "Fehlende Pflichtangaben",
  },
  description: {
    type: String,
    required: false,
    default: "Bitte füllen Sie alle erforderlichen Felder aus.",
  },
});
</script>



<style lang="scss" scoped>
    .challo__modal .overlay{
        position: absolute;
    }
    .modal__missing-field {
        width: 600px;
        padding: 32px;
        padding-bottom: 24px;
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
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 600;
        font-size: 18px;
        line-height: 27px;
        text-align: center;
        color: #1AA1E4;
        margin-bottom: 32px;
    }

    .message {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        text-align: center;
        color: #135F84;
    }

    .buttons {
        width: 285px;
    }
</style>

