<template>
    <div class="filter-option  absolute bg-white min-w-max shadow-sm" ref="parent">
        <ul class="filter-option__items max-h-[300px] overflow-y-auto">
            <li
                v-for="option in options"
                :key="option[valueKey]"
                class="py-1.5 text-gray-3 text-center border-b border-tints-1 last:border-0"
            >
                <label
                    class="py-1.5 px-6 cursor-pointer position-relative"
                    :class="'label-'.concat( option[valueKey] )"
                >
                    <input
                        @click="toggleClass($event);"
                        class="mr-1 hidden"
                        :class="[{ 'checkbox-active-round' : (option[valueKey] === 'active'), 'checkbox-inactive-round' : (option[valueKey] === 'inactive') }]"
                        type="radio"
                        :value="option[valueKey]"
                        v-model.number="form.values"
                        :checked="option[valueKey] == form.values"
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
            default: [
                {
                    name: '10',
                    value: '10'
                },
                {
                    name: '25',
                    value: '25'
                },
                {
                    name: '50',
                    value: '50'
                },
                {
                    name: '100',
                    value: '100'
                },
            ],
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

        watch(
            () => form.values,
            _debounce(function (cur, prev) {
                ctx.emit('closePerPageOption');
                Inertia.visit(
                    route(props.routeName, {
                        ...buildQueryParams(),
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => {
                            if(cur.length < prev.length) {
                                ctx.emit('instantUpdate', props.columnKey);
                            }
                        }
                    }
                );
            }, 1)
        );

        const buildQueryParams = () => {
            let searchParams = Object.fromEntries(
                new URLSearchParams(location.search)
            );

            if (form.values.length) {
                searchParams[props.columnKey] = form.values;
            } else {
                delete searchParams[props.columnKey];
            }

            if (searchParams.hasOwnProperty("page")) {
                delete searchParams["page"];
            }

            return searchParams;
        };



        return {
            form,
            parent
        };
    },

    methods: {
        
        toggleClass(e) {
            let querySelector = null;
            if (typeof this.form.values === 'object' && this.form.values !== null) {
                for (let selectedItem in this.form.values) {
                    querySelector = 'label.'.concat( 'label-'.concat( this.form.values[selectedItem] ) );
                }
            } else {
                querySelector = 'label.'.concat( 'label-'.concat( this.form.values ) );
            }

            if (querySelector !== null) this.parent.querySelector( querySelector ).classList.remove( 'checkbox-active-label' );
            e.target.parentElement.classList.add('checkbox-active-label');
            //this.$emit( 'closePerPageOption' );
            
        }
    }
};
</script>

<style lang="scss" scoped>

.filter-option__items li label {
    font-family: 'Poppins', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 20px;
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
    top: 25px;
    z-index: 1;
    right: 0;
    border-radius: 16px;
}

</style>
