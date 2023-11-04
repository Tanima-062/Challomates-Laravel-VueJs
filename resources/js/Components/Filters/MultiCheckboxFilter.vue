<template>
    <div class="filter_options relative" v-click-away="() => { if (active) emit('toggle') }">
        <div class="filter-menu__label--title" @click="emit('toggle')">
            {{ label }}
            <UpArrow v-if="active" />
            <DownArrow v-if="!active" />
        </div>
        <ul class="filter-option__items max-h-[300px]  overflow-y-auto absolute bg-white min-w-[150px] shadow-sm rounded-b-md z-[1]"
            v-show="active">
            <li v-for="option in options" :key="option[valueKey]"
                class="px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="attrs.optionClass">
                <label>
                    <input class="mr-1" type="checkbox" :value="option[valueKey]" v-model="form" />
                    {{ option[labelKey] }}
                </label>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, useAttrs, watch } from "vue";
import { ref } from "@vue/reactivity"
import { debounce as _debounce } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import UpArrow from "../Icons/UpArrow.vue";
import DownArrow from "../Icons/DownArrow.vue";


const props = defineProps({
    label: {
        type: String,
        required: true,
    },

    options: {
        type: [Array, Object],
        require: true,
    },
    valueKey: {
        type: String,
        default: 'value'
    },
    labelKey: {
        type: String,
        default: 'label'
    },
    routeName: {
        type: String,
        required: true,
    },
    column: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        required: true,
    }
})

const emit = defineEmits(['instantUpdate', 'toggle'])
const attrs = useAttrs()

const form = ref([])

watch(form, _debounce(function (cur, prev) {
    Inertia.visit(
        route(props.routeName, {
            ...buildQueryParams(),
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (cur.length < prev.length) {
                    emit('instantUpdate', props.column)
                }
            }
        }
    );
}, 500)
);

const buildQueryParams = () => {
    let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
    );

    if (form.value.length) {
        searchParams[props.column] = form.value.join(",");
    } else {
        delete searchParams[props.column];
    }

    if (searchParams.hasOwnProperty("page")) {
        delete searchParams["page"];
    }

    return searchParams;
};

onMounted(() => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    if (searchParams.hasOwnProperty(props.column)) {
        form.value = searchParams[props.column].toString().split(",");
    }
})
</script>
