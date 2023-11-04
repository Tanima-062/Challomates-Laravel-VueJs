<template>
    <div class="relative">
        <div class="title-wrapper flex gap-[7px] items-center text-tints-5 cursor-pointer" @click="active = !active"
            v-click-away="() => active = false">
            <slot name="title" :status="active">
                <p class="label font-poppins text-16 text-tints-5">Einträge pro Seite</p>
                <UpArrow v-if="active" />
                <DownArrow v-if="!active" />
            </slot>
        </div>
        <ul class="absolute flex flex-col divide-y-[1px] divide-tints-1 rounded-[16px] top-[100%] right-0 bg-white w-16 z-[1]"
            v-if="active">
            <slot name="pages" :changePerPage="changePerPage">
                <li @click.stop="changePerPage(10)"
                    class="font-poppins text-13 text-tints-5 cursor-pointer h-[37px] w-full flex justify-center items-center">
                    10</li>
                <li @click.stop="changePerPage(25)"
                    class="font-poppins text-13 text-tints-5 cursor-pointer h-[37px] w-full flex justify-center items-center">
                    25</li>
                <li @click.stop="changePerPage(50)"
                    class="font-poppins text-13 text-tints-5 cursor-pointer h-[37px] w-full flex justify-center items-center">
                    50</li>
                <li @click.stop="changePerPage(100)"
                    class="font-poppins text-13 text-tints-5 cursor-pointer h-[37px] w-full flex justify-center items-center">
                    100</li>
            </slot>
        </ul>
    </div>
</template>

<script setup>
import UpArrow from './Icons/UpArrow.vue';
import DownArrow from './Icons/DownArrow.vue';
import { Inertia } from '@inertiajs/inertia';
import route from '../../../vendor/tightenco/ziggy/src/js';
import { useAttrs } from 'vue';
import { ref } from '@vue/reactivity';

const attrs = useAttrs()
const active = ref(attrs.active ?? false)

const props = defineProps({
    target: {
        type: String,
        required: true,
    },
    params: {
        type: Object,
        default: {},
    }
})

const changePerPage = (per_page) => {
    active.value = false

    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    searchParams['per_page'] = per_page
    let params = { ...props.params, ...searchParams }
    delete params.page

    Inertia.visit(route(props.target, params), {
        method: 'GET',
        preserveScroll: true,
    })
}

</script>
