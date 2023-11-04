<template>
    <v-date-picker v-model="time" mode="time" :masks="{ input: ['HH:mm', 'H:m'] }"
        :model-config="{ type: 'string', mask: ['HH:mm', 'H:m'] }" is24hr>
        <template v-slot="{ inputEvents }">
            <input type="text" v-on="inputEvents" v-model="time">
        </template>
    </v-date-picker>
</template>


<script setup>
import { ref } from "@vue/reactivity";
import { computed, watch } from "@vue/runtime-core"
import { useAttrs } from "vue";

const attrs = useAttrs();
const emit = defineEmits(["update:modelValue"]);
defineProps({
    placeholder: {
        type: String,
        default: "00:00",
    },
});


const time = ref(attrs.modelValue);

watch(time, () => {
    if (time.value) {
        emit("update:modelValue", time.value);
    }
});

</script>
