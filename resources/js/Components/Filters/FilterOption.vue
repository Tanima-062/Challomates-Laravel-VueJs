<template>
    <div class="filter-option  absolute bg-white min-w-[150px] shadow-sm rounded-b-md" :class="classList">
        <ul class="filter-option__items max-h-[300px]  overflow-y-auto ">
            <li
                v-for="option in options"
                :key="option[valueKey]"
                class=" px-4 py-2 text-gray-3 border-b border-tints-1 last:border-0"
            >
                <label @click="filter" class="cursor-pointer">
                    <input
                        class="mr-1"
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
            classList: {
                type: [String, Object],
                default: "",
            }
        },
        setup(props, ctx) {
            const form = reactive({
                values: [],
            });

            let searchParams = Object.fromEntries(new URLSearchParams(location.search));

            if (searchParams.hasOwnProperty(props.columnKey)) {
                form.values = searchParams[props.columnKey].toString().split(",");
            }

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
                }, 500)
            );

            const buildQueryParams = () => {
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                if (props.inputType === 'checkbox') {
                    if (form.values.length) {
                        searchParams[props.columnKey] = form.values.join(",");
                    } else {
                        delete searchParams[props.columnKey];
                    }
                } else {
                    if (form.values.toString().length) {
                        searchParams[props.columnKey] = form.values;
                    } else {
                        delete searchParams[props.columnKey];
                    }
                }

                if (searchParams.hasOwnProperty("page")) {
                    delete searchParams["page"];
                }

                return searchParams;
            };



            return {
                form,
            };
        },
    };
</script>

<style lang="scss" scoped>
    .filter-option {
        top: 30px;
    }
    .filter-option__items li {

    }
    .filter-option__items li label{
        font-family: 'Ropa Sans',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 17px;
        color: #135F84;
    }
</style>
