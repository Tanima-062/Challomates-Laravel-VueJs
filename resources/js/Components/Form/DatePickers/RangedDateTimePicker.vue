<template>
    <label v-if="label" class="form-label block mb-[8px]" :class="attrs.labelClass">{{ label }}</label>
    <v-date-picker v-model="range" :mode="attrs.isDateOnly ? 'date' : 'dateTime'" :masks="displayMask"
                   :model-config="convertMask" :columns="2" :select-attribute="attributes" :drag-attribute="attributes"
                   :class="{ 'ranged-date-picker': !attrs.isDateOnly }" ref="calander" is-range
                   :is24hr="attrs.isDateOnly ? false : true">

        <template v-slot="{ inputValue, inputEvents }">

            <div class="flex flex-col bg-white sm:flex-row justify-start items-center border-[1px] placeholder:text-gray-3 border-gray-corner rounded-full"
                 :class="{ '!border-error': (error || show_error) }">
                <div class="relative h-[40px] w-6/12">
                    <p v-if="inputValue.start"
                       class=" absolute text-tints-5 text-16 font-ropa font-normal py-1 w-[65px] h-full flex justify-center items-center">
                        {{ placeholders.start }}
                    </p>
                    <input
                        class=" word-space flex-grow pr-2 py-1 outline-none bg-white text-tints-5 text-16 font-ropa border-0 w-full h-full rounded-l-full placeholder:text-gray-3"
                        :class="{ 'pl-16': inputValue.start, 'pl-5': !inputValue.start }"
                        :placeholder="placeholders.startEmpty" :value="inputValue.start" v-on="inputEvents.start" />
                </div>

                <div class="relative h-[40px] w-6/12">
                    <p v-if="inputValue.end"
                       class=" absolute text-tints-5 text-16 font-ropa font-normal py-1 w-[35px] h-full flex justify-center items-center">
                        {{ placeholders.end }}
                    </p>
                    <input
                        class=" word-space flex-grow pr-2 py-1 outline-none bg-white text-tints-5 text-16 font-ropa border-0 w-full h-full rounded-r-full pl-10"
                        :placeholder="placeholders.endEmpty" :value="inputValue.end" v-on="inputEvents.end" />
                    <div
                        class=" date-picker-icon absolute h-full flex justify-center items-center top-0 right-0 w-[65px] pointer-events-none">
                        <slot name="icon">
                            <DatePickerIcon v-if="!attrs.isDateOnly"/>
                            <DatePickerSecondIcon v-if="attrs.isDateOnly"/>
                        </slot>
                    </div>
                </div>
            </div>

        </template>

    </v-date-picker>
</template>

<script setup>
import { ref, useAttrs, watch, computed } from "@vue/runtime-core";
import DatePickerIcon from "../../Icons/DatePicker.vue";
import DatePickerSecondIcon from "../../Icons/DatePickerSecond.vue";

const attrs = useAttrs();
const emit = defineEmits(["update:startDate", "update:endDate", "change", 'clear']);
const props = defineProps({
    label: String,
    placeholders: {
        type: Object,
        default: {
            start: "Von",
            end: "Bis",
            startEmpty: "Von - Bis",
            endEmpty: "",
        },
    },
    show_error: {
        type: Boolean,
        default: false,
    },
    error: String,
});
const attributes = {
    highlight: {
        color: "blue",
        fillMode: "light",
        class: "bar",
    },
};

const range = ref({
    start: attrs.startDate || attrs['start-date'],
    end: attrs.endDate || attrs['end-date'],
});

const displayMask = computed(() => {
    if (attrs.isDateOnly) {
        return { input: ['DD.MM.YYYY', 'YYYY-MM-DD', 'YYYY/MM/DD'] }
    }
    return { inputDateTime24hr: ['DD.MM.YYYY HH:mm', 'YYYY-MM-DD HH:mm', 'YYYY/MM/DD HH:mm', 'DD.MM.YYYY H:m', 'YYYY-MM-DD H:m', 'YYYY/MM/DD H:m'] }
})

const convertMask = computed(() => {
    if (attrs.isDateOnly) {
        return { type: 'string', mask: ['YYYY-MM-DD', 'YYYY/MM/DD', 'YYYY.MM.DD'] }
    }
    return { type: 'string', mask: ['YYYY-MM-DD HH:mm', 'YYYY/MM/DD HH:mm', 'YYYY.MM.DD HH:mm', 'YYYY-MM-DD H:m', 'YYYY/MM/DD H:m', 'YYYY.MM.DD H:m'] }
})

watch(range, () => {
    if (range.value) {
        emit("update:startDate", range.value.start);
        emit("update:endDate", range.value.end);
        emit('change', { startDate: range.value.start, endDate: range.value.endDate })
    } else {
        emit("clear")
    }
});
</script>

<style lang="scss" scoped>
.word-space {
    word-spacing: 5px;
}
</style>
