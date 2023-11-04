<template>
    <div class="filter_options relative" v-click-away="() => { if (active) emit('toggle') }">
        <div class="filter-menu__label--title" @click="emit('toggle')">
            {{  label  }}
            <UpArrow v-if="active" />
            <DownArrow v-if="!active" />
        </div>
        <ul class="filter-option__items max-h-[300px]  overflow-y-auto absolute bg-white min-w-[150px] shadow-sm rounded-b-md z-[1]"
            v-show="active">
            <li v-for="option in options" :key="option[valueKey]"
                class="px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="[attrs.optionClass, form == option[valueKey] ? 'font-semibold' : '']"
                @click="setValue(option[valueKey])">
                {{  option[labelKey]  }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, useAttrs, watch } from "vue";
import { ref, computed } from "@vue/reactivity"
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

const form = ref()

const label = computed(() => {
    const selectedLabel = props.options.find((item) => item[props.valueKey] == form.value)
    return selectedLabel ? selectedLabel[props.labelKey] : props.label
})

//toggle form value if it the same option is selected other wise set it
const setValue = (value) => {
    if (value == form.value) {
        label.value = props.label
        form.value = ''
        return
    }

    form.value = value

}

watch(form, () => {
    Inertia.visit(
        route(props.routeName, {
            ...buildQueryParams(),
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (!form.value) {
                    emit('instantUpdate', props.column)
                }
            }
        }
    )
});

const buildQueryParams = () => {
    let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
    );

    if (form.value) {
        searchParams[props.column] = form.value;
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
        form.value = searchParams[props.column];
        emit('instantUpdate', props.column, true)
    }
})
</script>
