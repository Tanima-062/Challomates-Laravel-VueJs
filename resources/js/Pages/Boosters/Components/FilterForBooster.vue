<template>
    <div v-click-away="() => emit('closeMenu')">
        <span v-if="opend" class="filter-menu__label--title" @click="emit('toggleMenu')">
            {{textShow}}
            <up-arrow />
        </span>
        <span v-else class="filter-menu__label--title" @click="emit('toggleMenu')">
            {{textShow}}
            <down-arrow />
        </span>
        <div class="filter-option  absolute bg-white  min-w-max shadow-sm rounded-b-md z-10" v-if="opend">
            <ul class="filter-option__items max-h-[300px] overflow-y-auto">
                <li v-for="option in options" :key="option[value]"
                    class=" px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0">
                    <label @click="filter(option[name]);" class="cursor-pointer">
                         {{option['name']}}
                    </label>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "@vue/reactivity";
import { computed, watch } from "@vue/runtime-core";
import { debounce as _debounce } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import { onMounted } from "vue";
import DownArrow from "../../../Components/Icons/DownArrow.vue";
import UpArrow from "../../../Components/Icons/UpArrow.vue";

const emit = defineEmits(['closeMenu', 'toggleMenu', 'instantUpdate'])
const props = defineProps({
    options: {
        type: [Array, Object],
        default: [],
    },
    value: {
        type: [String, Number],
        default: "id",
    },
    name: {
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
    key_prefix: {
        type: [String,Number],
        required: false,
    },
    title:{
        type:String,
        default:"Vertriebspartner"
    }
})

const search_key = computed(()=>props.key_prefix ? `${props.key_prefix}` : '')
const textShow = ref(props.title)

const form = reactive({
    value: undefined,
});


watch(
    () => form.value,
    _debounce(function (cur) {

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

    searchParams[search_key.value] = form.value

    if (searchParams.hasOwnProperty("page")) {
        delete searchParams["page"];
    }

    return searchParams;
};

const filter = (name) => {
    textShow.value = name
    form.value = name
}


onMounted(() => {
    let searchParams = Object.fromEntries(new URLSearchParams(location.search));
    if (searchParams.hasOwnProperty(search_key.value)) {
        form.value = searchParams[search_key.value];
        textShow.value = searchParams[search_key.value];
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

.filter-option__items li label:hover {
    font-weight: bold ;
}

</style>
