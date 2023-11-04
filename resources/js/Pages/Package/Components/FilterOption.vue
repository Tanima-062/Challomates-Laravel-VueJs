<template>
    <div class="filter-option  absolute bg-white  min-w-max shadow-sm rounded-b-md" ref="parent">
        <ul class="filter-option__items max-h-[300px] overflow-y-auto">
            <li
                v-for="option in options"
                :key="option[valueKey]"
                class=" px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0"
            >
                <label
                    class="cursor-pointer position-relative"
                    :class="'label-'.concat( option[valueKey] )"
                >
                    <input
                        v-if="inputType === 'radio'"
                        @click="toggleClass($event)"
                        class="mr-1"
                        :class="[{ 'checkbox-active-round' : (option[valueKey] == 'active'), 'checkbox-inactive-round' : (option[valueKey] == 'inactive') }]"
                        :type="inputType"
                        :value="option[valueKey]"
                        v-model.number="form.values"
                        :checked="option[valueKey] == form.values"
                    />
                    <input
                        v-if="inputType === 'checkbox'"
                        @click="toggleClass($event)"
                        class="mr-1"
                        :class="[{ 'checkbox-active-round' : (option[valueKey] == 'active'), 'checkbox-inactive-round' : (option[valueKey] == 'inactive') }]"
                        :type="inputType"
                        :value="option[valueKey]"
                        v-model="form.values"
                    />
                    {{ option[nameKey] }}
                </label>
            </li>
        </ul>
    </div>
</template>

<script>
import {reactive} from "@vue/reactivity";
import { ref, onMounted } from "vue";
import {watch} from "@vue/runtime-core";
import {debounce as _debounce} from "lodash";
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        options: {
            type: [Array, Object],
            default: [],
        },
        inputType: {
            type: String,
            default: "checkbox",
        },
        nameKey: {
            type: [String, Number],
            default: "name",
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
    },
    setup(props, ctx) {
        const form = reactive({
            values: [],
        });
        const parent = ref(null);

        let searchParams = Object.fromEntries(new URLSearchParams(location.search));
        if (searchParams.hasOwnProperty(props.columnKey)) {
            form.values = searchParams[props.columnKey].toString().split(",");
        }

        onMounted(() => {
            for (let selectedItem in form.values) {
                let querySelector = 'label.'.concat( 'label-'.concat( form.values[selectedItem] ) );
                parent.value.querySelector( querySelector ).classList.toggle( 'checkbox-active-label' );
            }
        })

        const toggleClass = (e) => {
            if ( e.target.type === 'radio' ) {
                if (typeof form.values === 'object' && form.values !== null) {
                    for (let selectedItem in form.values) {
                        let querySelector = 'label.'.concat( 'label-'.concat( form.values[selectedItem] ) );
                        parent.value.querySelector( querySelector ).classList.remove( 'checkbox-active-label' );
                    }
                } else {
                    let querySelector = 'label.'.concat( 'label-'.concat( form.values ) );
                    parent.value.querySelector( querySelector ).classList.remove( 'checkbox-active-label' );
                }
                e.target.parentElement.classList.add('checkbox-active-label');
            } else {
                e.target.parentElement.classList.toggle('checkbox-active-label');
            }
        };

        watch(
            () => form.values,
            _debounce(function (cur, prev) {
                Inertia.visit(
                    route(props.routeName, {
                        ...buildQueryParams(),
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => {
                            if(cur.length < prev.length) {
                                ctx.emit('instantUpdate', props.columnKey)
                            }
                        }
                    }
                );
            }, 250)
        );

        const buildQueryParams = () => {
            let searchParams = Object.fromEntries(
                new URLSearchParams(location.search)
            );

            if (form.values.length) {
                searchParams[props.columnKey] = (props.inputType === 'checkbox') ? form.values.join(",") : form.values;
            } else {
                delete searchParams[props.columnKey];
            }

            /*if (searchParams.hasOwnProperty("page")) {
                delete searchParams["page"];
            }*/

            return searchParams;
        };



        return {
            form,
            toggleClass,
            parent
        };
    },
};
</script>

<style lang="scss" scoped>

.filter-option__items li label {
    font-family: 'Ropa Sans', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 17px;
    color: #135F84;
}

.package-status {
    ::before {
        content: "Test";
    }
}

.filter-option__items li label.checkbox-active-label {
    font-weight: 600;
}

.checkbox-active-round {
    width: 0.7em;
    height: 0.7em;
    background-color: #06CEB5;
    border-radius: 50%;
    vertical-align: middle;
    border: 1px solid #06CEB5;
    appearance: none;
    outline: none;
}

.checkbox-inactive-round {
    @extend .checkbox-active-round;
    background-color: #FF0000;
    border: 1px solid #FF0000;
}


.filter-option {
    top: 30px;
    z-index: 1;
}

</style>
