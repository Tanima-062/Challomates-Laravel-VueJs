<template>
    <div class="modal__confirmation flex flex-col" :class="attrs['class']" v-bind:style="attrs['style']">
        <h5 class="title">{{ message}}</h5>
        <p class="description" v-html="text"></p>
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
    message: {
        type: String,
        required: false,
        default: "Are You Sure"
    },

    text: {
        type: String,
        required: false,
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

    .modal__confirmation {
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
        text-align: center;
        color: #1AA1E4;
        margin-bottom: 32px;
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
</style>

