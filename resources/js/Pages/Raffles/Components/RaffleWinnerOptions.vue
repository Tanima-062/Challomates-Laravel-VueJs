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
                        v-if="inputType === 'checkbox'"
                        @click="toggleClass($event)"
                        class="mr-1 checkbox-status"
                        :class="[option[valueKey]]"
                        :type="inputType"
                        :value="option[valueKey]"
                        v-model="form.values"
                    />
                </label>
                <a
                    target="_blank"
                    :href="route( 'mobile-app-users.show', { mobile_app_user: option[valueKey] } )"
                >
                    {{ option[nameKey] }}
                </a>
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
    position: relative;
}

.filter-option__items li a {
    font-family: "Ropa Sans", serif;
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

.checkbox-status {
    /*width: 0.8em;*/
    height: 0.8em;
    border-radius: 50%;
    vertical-align: middle;
    outline: none;
    cursor: pointer;
}

.new_published {
    background-color: #1AA1E4;
    border: 1px solid #1AA1E4;
}

.active_published {
    background-color: #06CEB5;
    border: 1px solid #06CEB5;
}

.finished_published {
    background-color: #FF0000;
    border: 1px solid #FF0000;
}

.canceled_published {
    background-color: #FFA013;
    border: 1px solid #FFA013;
}

.drawn_published,
.drawn_not_published {
    background-color: #102327;
    border: 1px solid #102327;
}

.new_not_published {
    background-color: #1AA1E4;
}

.active_not_published {
    background-color: #06CEB5;
}

.finished_not_published {
    background-color: #FF0000;
}

.canceled_not_published {
    background-color: #FFA013;
}

.new_not_published::before,
.active_not_published::before,
.finished_not_published::before,
.canceled_not_published::before {
    content: url("data:image/svg+xml; utf8,%3Csvg%20width%3D'10'%20height%3D'10'%20viewBox%3D'0%200%206%206'%20fill%3D'none'%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%3E%3Ccircle%20cx%3D'3'%20cy%3D'3'%20r%3D'2.5'%20fill%3D'%23FF0000'%20stroke%3D'white'%2F%3E%3C%2Fsvg%3E");
    position: absolute;
    top: 0.05em;
    left: 0.1em;
}

.filter-option {
    top: 30px;
    z-index: 1;
}

</style>
