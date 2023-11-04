<template>

    <v-date-picker
        v-model="dateTime"
        :mode=props.mode
        :masks="displayMask"
        :model-config="convertMask"
        :select-attribute="attributes"
        :class= "props.mode == 'dateTime' ? 'ranged-date-picker' : ''"
        is24hr
    >
        <template v-slot="{ inputValue, inputEvents }">
            <div
                class="
          relative
          h-[40px]
          w-full
          border-[1px] border-gray-corner
          rounded-full
          overflow-hidden
        "
                :class="[{ error: error, 'input-field-error' : show_error }]"
            >
                <input
                    class="
                    word-space
            flex-grow
            pl-5
            pr-2
            py-1
            outline-none
            bg-white
            text-tints-5 text-16
            font-ropa
            border-0
            w-full
            h-full
            rounded-r-full
            placeholder:text-gray-3
          "
                    :placeholder="$t(placeholder)"
                    :value="inputValue"
                    v-on="inputEvents"
                />
                <div v-if="icon"
                    class="
            date-picker-icon
            absolute
            h-full
            flex
            justify-center
            items-center
            top-0
            right-0
            w-[65px]
            pointer-events-none
          "
                >
                    <DatePickerSecondIcon />
                </div>
                <div v-else
                    class="
            date-picker-icon
            absolute
            h-full
            flex
            justify-center
            items-center
            top-0
            right-0
            w-[65px]
            pointer-events-none
          "
                >
                <DatePickerIcon />
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
const emit = defineEmits(["update:modelValue", 'clear']);
const props = defineProps({

    placeholder: {
        type: String,
        default: "Datum und Uhrzeit eingeben/auswählen",
    },
    show_error: {
        type: Boolean,
        default: false,
    },
    error: String,
    label: String,
    mode:{
        type: String,
        default:"dateTime"
    },
    icon:{
        type: Boolean,
        default: false
    }

});
const attributes = {
    highlight: {
        color: "blue",
        fillMode: "light",
        class: "bar",
    },
};

const dateTime = ref(attrs.modelValue)

const displayMask = computed(() => {
    if (props.mode == 'date') {
        return { input: ['DD.MM.YYYY'] }
    }
    return { inputDateTime24hr: ['DD.MM.YYYY HH:mm'] }
})
const convertMask = computed(() => {
    if (attrs.isDateOnly) {
        return { type: 'string', mask: ['YYYY-MM-DD'] }
    }
    return { type: 'string', mask: ['YYYY-MM-DD HH:mm'] }
})

watch(dateTime, () => {
    if (dateTime.value) {
        emit("update:modelValue", dateTime.value);
    } else {
        emit('clear')
    }
});
</script>

<style lang="scss" scoped>
.word-space {
    word-spacing: 5px;
}

.error {
    border-color: #c81717;
}

.input-field-error {
    border-color: #ff0000;
}
</style>
