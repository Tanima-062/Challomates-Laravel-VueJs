<template>
    <div
        class="select w-full relative"
        v-click-away="() => (showOptions = false)"
    >
        <label v-if="label" class="form-label block mb-[8px]" :class="attrs.labelClass">{{ label }}</label>
        <div
            class="selected bg-white flex h-full w-full items-center justify-between px-5 py-3 rounded-[48px] border-[1px]"
            :class="[attrs.error ? 'border-[1px] border-error' : 'border-gray-corner']"
            @click="showOptions = !showOptions"
        >
            <p class="label text-16 font-ropa" :class="[attrs.labelClass, attrs.modelValue ? 'text-tints-5' : 'text-gray-3']">
                {{selected || placeholder}}
            </p>
            <span class="toggle-icons">
        <DownArrow v-if="!showOptions"/>
        <UpArrow v-if="showOptions"/>
      </span>
        </div>
        <ul
            class="options bg-white w-full absolute mt-1 divide-y divide-gray-corner z-[1] max-h-[250px] overflow-y-auto"
            v-show="showOptions"
        >
            <li
                v-for="option in options"
                class="
          option
          text-tints-5 text-16
          font-ropa
          px-5
          py-3
          cursor-pointer
          hover:bg-gray-corner
        "
                :class="isSelected(option) ? 'bg-gray-corner': ''"
                :key="option[value_name]"
                @click.stop="(e) => setSelected(e, option)"
            >
                {{option[label_name]}}
            </li>
        </ul>
    </div>
</template>

<script setup>
import {v4 as uuid} from "uuid";
import {computed, ref, useAttrs} from "vue";
import DownArrow from "../Icons/DownArrow.vue";
import UpArrow from "../Icons/UpArrow.vue";

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue",'change']);
const props = defineProps({
    name: {
        type: String,
        required: false,
    },

    label: String,

    id: {
        type: String,
        default: `select-input-${uuid()}`,
    },

    placeholder: {
        type: String,
        default: "Select Options",
    },

    options: {
        type: [Array, Object],
        required: true,
    },

    value_name: {
        type: String,
        default: "value",
    },

    label_name: {
        type: String,
        default: "label",
    },
});

const showOptions = ref(false);

const selected = computed(() => {
    const selected_option = props.options.find((option) => attrs.modelValue == option[props.value_name])
    return selected_option ? selected_option[props.label_name] : attrs.modelValue
})

const setSelected = (e, option) => {
    emit("update:modelValue", option[props.value_name]);
    emit('change');
    e.target.classList.add('bg-gray-corner')
    showOptions.value = false;
};

const isSelected = (option) => {
    return attrs.modelValue == option[props.value_name]
}
</script>

<style lang="scss" scoped>
// .options {
//   .option:first-of-type {
//     border-top-right-radius: 24px;
//     border-top-left-radius: 24px;
//   }

//   .option:last-of-type {
//     border-bottom-right-radius: 24px;
//     border-bottom-left-radius: 24px;
//   }
// }
</style>
