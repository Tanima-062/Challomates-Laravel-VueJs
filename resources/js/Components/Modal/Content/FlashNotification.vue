<template>
    <div class="modal__notification flex flex-col" :class="className" :style="styles">

        <div class="heading flex justify-between items-center flex-row mb-[18px]">
            <span class="title">{{ title }}</span>
            <span class="close cursor-pointer" @click="$emit('hide')">
                <Cross color="#135F84" />
            </span>
        </div>
        <p class="message">{{ content }}</p>
    </div>
</template>


<script setup>
import { computed, toRaw } from "@vue/runtime-core";
import Cross from "../../Icons/Cross.vue";
import { trans } from "laravel-vue-i18n";
const props = defineProps({
    title: {
        type: String,
        required: false,
        default: "Benachrichtigung",
    },
    className: {
        type: String,
        required: false,
        default: "top w-600",
    },

    styles: {
        type: Object,
        required: false,
    },

    message: {
        required: false,
        default: "Hello There!",
    },
});
const content = computed(() => {
    return props.message;
    //   const message = toRaw(props.message);
    //   if (typeof message == "string") {
    //     return trans(message);
    //   }
    //   return trans(message.text, message.attributes);
});
</script>

<script>
export default {
    inheritAttrs: false,
};
</script>

<style lang="scss" scoped>
.challo__modal .overlay {
    position: absolute;
}

.modal__notification {
    width: 600px;
    padding: 24px;
    background: #84e5ff;
    border-radius: 24px;
    height: fit-content;
    border-radius: 24px;
    position: absolute;
    top: 40px;
}

.title {
    font-family: "Poppins";
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 27px;
    color: #135f84;
}

.message {
    font-family: "Ropa Sans";
    font-style: normal;
    font-weight: 400;
    font-size: 22px;
    line-height: 30px;

    color: #135f84;
}
</style>
