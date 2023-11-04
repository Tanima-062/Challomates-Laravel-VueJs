<template>
    <li class="mb-6 h-8"
        :class="{ active: isActiveComponent }">
        <Link
          :href="route(routeName)"
          class="flex items-center text-shades-2 text-22 gap-2"
        >
            <slot />
        </Link>
      </li>
</template>

<script setup>
import {computed} from "vue"
import { Link, usePage } from "@inertiajs/inertia-vue3";

const page = usePage()

const props = defineProps({
    routeName: {
        type: String,
        required: true
    },
    activeText: {
        type: String,
        required: true
    },
    relatedComp: {
        type: Array,
        default: []
    }
})

const isActiveComponent = computed(() => {
    const regex = RegExp(`^(${props.activeText})`)
    return regex.test(page.component.value) || props.relatedComp.includes(page.component.value.split('/')[0]);
})
</script>

<style lang="scss" scoped>
    .active a {
        color: #00708e;
        font-family: "Poppins",serif;
        font-style: normal;
        font-weight: 400;
        font-size: 22px;
        line-height: 33px;
        text-decoration: underline;
    }
</style>
