<template>
    <div @click="emit('toggle', value)" :class="[static_class, attrs.classLists, attrs.selected ? 'bg-primary-1' : '']">

    </div>
</template>

<script setup>
import {useAttrs } from "@vue/runtime-core";

const static_class = "checkbox border-[1.5px] border-tints-4 rounded-[4px] w-[16px] h-[16px]";
const attrs = useAttrs()
const emit = defineEmits(["toggle"]);

const props = defineProps({
    value: {
        type: [String, Number, Boolean],
        required: true,
    },
});
</script>
