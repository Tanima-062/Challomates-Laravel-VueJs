<template>
  <div class="select w-full relative" v-click-away="() => (showOptions = false)">
    <div
      class="selected bg-white flex items-center overflow-hidden h-10"
      :class="[
        attrs.class,
        attrs.error ? 'border-[1px] border-error' : 'border-[1px] border-gray-corner',
        showOptions ? 'rounded-t-[25px]' : 'rounded-[48px]',
      ]"
      @click="showOptions = !showOptions"
    >
      <p
        class="label text-16 font-ropa px-5 py-3 w-[92%]"
        :class="[attrs.labelClass, options.find(option => option[valueKey] == attrs.modelValue) ? 'text-tints-5' : 'text-gray-3']"
        v-if="!showOptions"
      >
        <slot name="label" :label="label">
          {{ label }}
        </slot>
      </p>
      <input
        type="text"
        v-model="search_input"
        v-if="showOptions"
        @click.stop=""
        ref="search_input_element"
        class="challo__input rounded-[0px] border-0 w-[92%] h-[40px] px-5 py-3"
        :placeholder="placeholder"
      />
      <span class="toggle-icons w-[8%]">
        <DownArrow v-if="!showOptions" />
        <UpArrow v-if="showOptions" />
      </span>
    </div>
    <ul
      class="options bg-white w-full absolute divide-y divide-gray-corner z-[1] max-h-[400px] overflow-y-auto border-r-[1px] border-l-[1px] border-b-[1px] border-gray-corner rounded-b-[25px]"
      v-if="showOptions"
    >
      <li
        v-for="option in options"
        :key="option[valueKey]"
        @click.stop="(e) => setSelected(e, option[valueKey])"
      >
        <slot name="option" :option="option" :isSelected="isSelected(option)">
          <p
            class="option text-tints-5 text-16 font-ropa px-5 py-3 cursor-pointer hover:bg-gray-corner"
            :class="isSelected(option) ? 'bg-gray-corner' : ''"
          >
            {{ option[labelKey] }}
          </p>
        </slot>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, useAttrs, watch, nextTick } from "vue";
import UpArrow from "../Icons/UpArrow.vue";
import DownArrow from "../Icons/DownArrow.vue";

const emit = defineEmits(["update:modelValue", "change", "showOptions"]);
const attrs = useAttrs();

const props = defineProps({
  options: {
    type: [Object, Array],
    required: true,
  },

  searchable: {
    type: [Array, Object],
    required: true,
  },

  placeholder: {
    type: String,
    default: "Select Option",
  },

  labelKey: {
    type: String,
    default: "label",
  },

  valueKey: {
    type: String,
    default: "value",
  },
});

const search_input_element = ref("");
const search_input = ref("");
const showOptions = ref(false);

watch(showOptions, () => {
  emit("showOptions", showOptions.value);
  if (showOptions.value)
    nextTick(() => {
      search_input_element.value.focus();
    });
});

const setSelected = (e, option) => {
  emit("update:modelValue", option);
  emit("change", option);
  showOptions.value = false;
};

const isSelected = (value) => {
  if (attrs.modelValue) {
    return attrs.modelValue == value[props.valueKey];
  }
};

const label = computed(() => {
  if (attrs.modelValue) {
    const selected_option = props.options.find(
      (option) => option[props.valueKey] == attrs.modelValue
    );
    return selected_option ? selected_option[props.labelKey] : props.placeholder;
  }
  return props.placeholder;
});

const options = computed(() => {
  if (search_input.value) {
    const filter_options = props.options.filter((item) => {
      for (const search_key of props.searchable) {
        if (item[search_key]?.toLowerCase().includes(search_input.value.toLowerCase())) {
          return true;
        }
      }
    });
    return filter_options;
  }
  return props.options;
});
</script>

<style lang="scss">
.options {
  &::-webkit-scrollbar {
    width: 5px;
  }

  &::-webkit-scrollbar-thumb {
    background-color: #787878;
    border-radius: 10px;
  }
}
</style>
