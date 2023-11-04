<template>
    <input class="flex-grow outline-none bg-white text-tints-5 text-18x font-ropa border-0 w-full h-full"
        :class="[error || v$.time.$invalid ? 'border-[1px] border-error' : '', attrs.inputClass]"
        :placeholder="placeholder" v-model="time" @input="v$.$touch()" />
</template>

<script setup>
import { ref } from "@vue/reactivity";
import { computed, watch } from "@vue/runtime-core"
import { useAttrs } from "vue";
import dayjs from "dayjs";
import useVuelidate from "@vuelidate/core";
import { helpers } from '@vuelidate/validators'

const customeParseFormat = require('dayjs/plugin/customParseFormat')
dayjs.extend(customeParseFormat)

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue"]);
const props = defineProps({
    placeholder: {
        type: String,
        default: "00:00",
    },
});

const time = ref(attrs.modelValue);

const time_format = helpers.regex(/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/);
const rule = {
    time: {
        invalid_time: helpers.withMessage('invaild time format', time_format)
    }
}

const v$ = useVuelidate(rule, { time: time })


const error = computed(() => {
    if (attrs.requiredTime == true && !time.value) {
        return true
    }
    return false
})

watch(time, () => {
    emit("update:modelValue", time.value);
});

</script>
