<template>
    <div class="relative" v-click-away="() => hideAll()">
        <slot name="label" :showContent="showContent" :showDatePicker="showDatePicker" :toggleContent="toggleContent"
            :hideAll="hideAll">
            <div class="label flex gap-[6px] items-center text-gray-3"
                @click="() => showDatePicker ? hideAll() : toggleContent()">
                <h5 class="font-normal font-ropa text-16">
                    <slot name="labelText">
                        {{ labelText }}
                    </slot>
                </h5>
                <DownArrow v-if="!showContent && !showDatePicker" />
                <UpArrow v-if="showContent || showDatePicker" />
            </div>
        </slot>


        <slot name="content" :showContent="showContent" :toggleContent="toggleContent" :setValue="setValue">
            <div class="content absolute bg-white w-[200px] divide-y-[1px] divided-gray-corner shadow-[0px 8px 8px rgba(19, 95, 132, 0.15)] rounded-[8px]"
                v-if="showContent">
                <div class="item cursor-pointer py-4 pl-4 text-gray-3" @click="setValue('last week')">Letzte Woche</div>
                <div class="item cursor-pointer py-4 pl-4 text-gray-3" @click="setValue('last month')">Letzter Monat
                </div>
                <div class="item cursor-pointer py-4 pl-4 text-gray-3"
                    @click="() => { showDatePicker = true; toggleContent(false) }">
                    Zeitraum wählen
                </div>
            </div>
        </slot>
        <RangedDatePicker class="absolute w-[250px]" v-if="showDatePicker" @change="(value) => setValue(value)" />
    </div>
</template>

<script setup>

//vue core functionalities
import { computed, ref, toRaw } from '@vue/reactivity';
import { watch } from "@vue/runtime-core"
import { useAttrs, onMounted } from 'vue';

//compoenents
import UpArrow from '../../../Components/Icons/UpArrow.vue';
import DownArrow from '../../../Components/Icons/DownArrow.vue';
import RangedDatePicker from '../../../Components/Form/DatePickers/RangedDatePicker.vue';
import dayjs from 'dayjs';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    routeName: {
        type: String,
        required: true,
    },

    additionalParams: {
        type: Object,
        required: false,
        default: {},
    },

    labelKey: {
        type: String,
        default: 'label'
    },

    valueKey: {
        type: String,
        default: 'value'
    },

    columnName: {
        type: String,
        default: 'date'
    }
})

const attrs = useAttrs()
const showContent = ref(attrs.showContent ?? false)
const activeValue = ref(attrs.value ?? false);
const showDatePicker = ref(attrs.showDatePicker ?? false)

const labelText = computed(() => {
    const textTranslations = { 'last week': 'Letzte Woche', 'last month': 'Letzter Monat' }
    if (typeof activeValue.value == 'string') {
        return textTranslations[activeValue.value] ?? 'Datum'
    }

    if (typeof activeValue.value == 'object') {
        return `${dayjs(activeValue.value.startDate).format('DD.MM.YYYY')}-${dayjs(activeValue.value.endDate).format('DD.MM.YYYY')}`
    }
    return 'Datum'
})

const toggleContent = (status = undefined) => {
    if (status !== undefined) {
        showContent.value = status
        return
    }
    showContent.value = !showContent.value
}

const hideAll = () => {
    showDatePicker.value = false
    showContent.value = false
}


const setValue = (value) => {
    activeValue.value = value
}

const buildQueryParams = () => {
    let searchParams = Object.fromEntries(
        new URLSearchParams(location.search)
    );

    if (searchParams.hasOwnProperty("page")) {
        delete searchParams["page"];
    }
    activeValue.value ? searchParams[props.columnName] = toRaw(activeValue.value) : null

    return searchParams;
};

watch(activeValue, () => {
    const data = buildQueryParams();
    console.log(data)
    Inertia.visit(route(props.routeName,{...props.additionalParams, ...data}), {
        method: "GET",
        preserveScroll: true,
        preserveState: true,
    })
})

onMounted(() => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search))
})

</script>
