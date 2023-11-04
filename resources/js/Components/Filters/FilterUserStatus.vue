<template>
    <div v-click-away="() => emit('closeMenu')">
        <span class="filter-menu__label--title" @click="emit('toggleMenu')">
            {{ activeStatusText }}
            <DownArrow v-if="!opend" />
            <UpArrow v-if="opend" />
        </span>

        <div class="filter-option  absolute bg-white  min-w-max shadow-sm rounded-b-md z-10" v-if="opend">
            <ul class="filter-option__items max-h-[300px] overflow-y-auto">
                <li v-for="option in options" :key="option[valueKey]"
                    class=" px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0">
                    <label @click="filter(option[valueKey])" class="cursor-pointer"
                        :class="[(getStatusText(option[labelKey]?.toLowerCase() || option[valueKey])) == activeStatusText ? '!font-semibold' : '']">
                        <span class="status" :class="`status-${option[valueKey].toLowerCase()}`"></span> {{
                        getStatusText(option[labelKey]?.toLowerCase() || option[valueKey])
                        }}
                    </label>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "@vue/reactivity";
import { watch } from "@vue/runtime-core";
import { debounce as _debounce } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import { onMounted } from "vue";
import DownArrow from "../Icons/DownArrow.vue";
import UpArrow from "../Icons/UpArrow.vue";

const emit = defineEmits(['closeMenu', 'toggleMenu', 'instantUpdate'])
const props = defineProps({
    options: {
        type: [Array, Object],
        default: [],
    },
    valueKey: {
        type: [String, Number],
        default: "id",
    },
    columnKey: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    opend: {
        type: Boolean,
        required: true,
    },
    labelKey: {
        type: [String],
        required: false,
    }
})

const form = reactive({
    value: undefined,
});

const activeStatusText = ref('Status')

watch(
    () => form.value,
    _debounce(function (cur) {

        let activeStatus = cur;
        if (props.labelKey) {
            activeStatus = props.options.find((item) => item[props.valueKey] == cur)
            activeStatus = activeStatus ? activeStatus[props.labelKey].toLowerCase() : cur
        }
        activeStatusText.value = getStatusText(activeStatus)
        Inertia.visit(
            route(props.routeName, {
                ...buildQueryParams(),
            }),
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    if (cur == undefined) {
                        emit('instantUpdate');
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

    searchParams[props.columnKey] = form.value

    if (searchParams.hasOwnProperty("page")) {
        delete searchParams["page"];
    }

    return searchParams;
};

const filter = (status) => {
    form.value = form.value == status ? undefined : status
}

const getStatusText = (status) => {
    switch (status) {
        case 'active':
            return 'Aktiv'

        case 'inactive':
            return 'Inaktiv'

        case 'pending':
            return 'Anstehend'

        case 'registration pending':
            return 'Registrierung pendent'

        case 'canceled':
            return 'Abgebrochen'

        case 'expired':
            return 'Abgelaufen'

        case 'new':
            return "Neu"
        default:
            return "Status"
    }
}


onMounted(() => {
    let param = Object.fromEntries(new URLSearchParams(location.search)).status
    if (props.labelKey) {
        const activeStatus = props.options.find((item) => item[props.valueKey] == param)
        param = activeStatus ? activeStatus[props.labelKey].toLowerCase() : param
    }
    activeStatusText.value = param ? getStatusText(param) : 'Status'

    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    if (searchParams.hasOwnProperty(props.columnKey)) {
        form.value = searchParams[props.columnKey];
    }

})
</script>

<style lang="scss" scoped>
.filter-option {
    top: 30px;
}

.filter-option__items li {}

.filter-option__items li label {
    font-family: 'Ropa Sans';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 17px;
    color: #135F84;
}

.status {
    width: 10px;
    height: 10px;
    left: 16px;
    background: transparent;
    border-radius: 50%;
    margin-right: 8px;
    display: inline-block;
}

.status-active {
    background: #06CEB5;
}

.status-inactive {
    background: #FF0000;
}

.status-pending {
    background: #05655A;
}

.status-new {
    background: #1AA1E4;
}


.status-canceled {
    background: #FFA013;
}

.status-expired {
    background: #102327;
}
</style>
