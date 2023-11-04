<template>
    <v-date-picker v-model="date" mode="date" :masks="displayMask" :model-config="convertMask"
        :columns="$attrs.columns ?? 2" :select-attribute="attributes" :drag-attribute="attributes" ref="calander"
        is-range>

        <template v-slot="{ inputValue, inputEvents }">

            <div class="flex flex-col bg-white sm:flex-row justify-start items-center border-[1px] placeholder:text-gray-3 border-gray-corner rounded-full relative"
                :class="{ '!border-error': (error || show_error) }">
                <div class="relative h-[40px] w-full rounded-full overflow-hidden">
                    <input
                        class=" flex-grow pr-2 py-1 outline-none bg-white text-tints-5 text-16 font-ropa border-0 w-full h-full rounded-l-full placeholder:text-gray-3 pl-5"
                        :placeholder="attrs.placeholder ?? 'Von - Bis'"
                        @change="(e) => updateDate(e, inputEvents)"
                        :value="(inputValue.start && inputValue.end) ? `${inputValue.start}-${inputValue.end}` : ''"
                        v-on="{ focusin: inputEvents.start.focusin, focusout: inputEvents.start.focusout, click: inputEvents.start.click }" />
                </div>
                <div
                    class=" date-picker-icon absolute h-full flex justify-center items-center top-0 right-0 w-[65px] pointer-events-none">
                    <slot name="icon">
                        <DatePickerSecondIcon />
                    </slot>
                </div>
            </div>
        </template>

    </v-date-picker>
</template>

<script setup>
import { ref, useAttrs, watch, computed } from "@vue/runtime-core";
import { values } from "lodash";
import DatePickerIcon from "../../Icons/DatePicker.vue";
import DatePickerSecondIcon from "../../Icons/DatePickerSecond.vue";

const attrs = useAttrs();

const emit = defineEmits(["update:startDate", "update:endDate", "change"]);

const props = defineProps({
    label: String,

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

const date = ref({
    start: attrs.startDate || attrs['start-date'],
    end: attrs.endDate || attrs['end-date'],
})


const updateDate = (e, inputEvents) => {
    let values = e.target.value.split('-')
    inputEvents.start.change({ target: { value: values[0] } })
    inputEvents.end.change({ target: { value: values[1] ?? values[0] } })
}



const displayMask = computed(() => {
    return { input: ['DD.MM.YYYY', 'YYYY-MM-DD', 'YYYY/MM/DD'] }
})

const convertMask = computed(() => {
    return { type: 'string', mask: ['YYYY-MM-DD', 'YYYY/MM/DD', 'YYYY.MM.DD'] }
})

watch(date, () => {
    if (date.value) {
        emit("update:startDate", date.value.start);
        emit("update:endDate", date.value.end);
        emit('change', { startDate: date.value.start, endDate: date.value.end })
    } else {
        emit('change', '')
    }
});
</script>
