<template>
    <div
        class="filter_options relative"
        v-click-away="() => { if (active) emit('toggle') }"
    >
        <div
            class="filter-menu__label--title"
            @click="emit('toggle')"
        >
            {{ label }}
            <UpArrow v-if="active" />
            <DownArrow v-if="!active" />
        </div>
        <ul
            class="filter-option__items max-h-[300px]  overflow-y-auto absolute bg-white min-w-[150px] shadow-sm rounded-b-md z-[1]"
            v-show="active"
        >
            <!-- Select All -->
            <li
                class="px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="attrs.optionClass"
                v-if="allSelectable"
            >
                <label>
                    <input
                        class="mr-1"
                        type="checkbox"
                        :checked="allSelected"
                        @click="toggleSelectAll"
                    />
                    Alle auswählen
                </label>
            </li>

            <!-- Select Active Only -->
            <li
                class="px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="attrs.optionClass"
                v-if="activeSelectable && options.filter(item => item[statusKey] == 'active').length"
            >
                <label>
                    <input
                        class="mr-1"
                        type="checkbox"
                        :checked="allActiveSelected"
                        @click="toggleActiveSelection"
                    />
                    Nur Aktive
                </label>
            </li>

            <!-- Select Inactive Only -->
            <li
                class="px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="attrs.optionClass"
                v-if="inactiveSelectable && options.filter(item => item[statusKey] == 'inactive').length"
            >
                <label>
                    <input
                        class="mr-1"
                        type="checkbox"
                        :checked="allInactiveSelected"
                        @click="toggleInactiveSelection"
                    />
                    Nur Inaktive
                </label>
            </li>
            <li
                v-for="option in options"
                :key="option[valueKey]"
                class="px-4 py-2 border-b border-tints-1 last:border-0 cursor-pointer"
                :class="[attrs.optionClass, option.status == 'active' ? '!text-green-600' : '!text-red-600']"
            >
                <label>
                    <input
                        class="mr-1"
                        type="checkbox"
                        :value="option[valueKey]"
                        v-model="form"
                    />
                    {{ option[labelKey] }}
                </label>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, useAttrs, watch } from "vue";
import { computed, ref } from "@vue/reactivity"
import { debounce as _debounce } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import UpArrow from "../../../Components/Icons/UpArrow.vue";
import DownArrow from "../../../Components/Icons/DownArrow.vue";


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
    statusKey: {
        type: String,
        default: 'status'
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
    },
    allSelectable: {
        type: Boolean,
        require: false,
        default: false,
    },
    activeSelectable: {
        type: Boolean,
        require: false,
        default: false,
    },
    inactiveSelectable: {
        type: Boolean,
        require: false,
        default: false,
    },
})

const emit = defineEmits(['instantUpdate', 'toggle'])
const attrs = useAttrs()

const form = ref([])

const allSelected = computed(() => {
    return props.options.length < 1 ? false :  form.value.length == props.options.length
})

const allActiveSelected = computed(() => {
    const options = props.options.filter(option => option[props.statusKey] == 'active')
    const selectedActiveOptions = form.value.filter(id => options.find(option => option[props.valueKey] == id))

    return options.length < 1 ? false : selectedActiveOptions.length == options.length
})

const allInactiveSelected = computed(() => {
    const options = props.options.filter(option => option[props.statusKey] == 'inactive')
    const selectedInactiveOptions = form.value.filter(id => options.find(option => option[props.valueKey] == id))
    return options.length < 1 ? false : selectedInactiveOptions.length == options.length
})

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

const toggleSelectAll = () => {
    if (form.value.length < props.options.length) {
        form.value = props.options.map(item => item[props.valueKey])
    } else {
        form.value = []
    }
}

const toggleActiveSelection = () => {
    if (allActiveSelected.value) {
        form.value = form.value.filter(id => {
            const option = props.options.find(option => option[props.valueKey] == id)
            if (option.status !== 'active')
                return true

            return false
        })
    } else {
        const options = props.options.filter(option => option.status == 'active')
        form.value = options.map(item => item[props.valueKey])
    }
}

const toggleInactiveSelection = () => {
    if (allInactiveSelected.value) {
        form.value = form.value.filter(id => {
            const option = props.options.find(option => option[props.valueKey] == id)
            if (option.status !== 'inactive')
                return true

            return false
        })
    } else {
        const options = props.options.filter(option => option.status == 'inactive')
        form.value = options.map(item => item[props.valueKey])
    }
}

onMounted(() => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    if (searchParams.hasOwnProperty(props.column)) {
        form.value = searchParams[props.column].toString().split(",");
    }
})
</script>
