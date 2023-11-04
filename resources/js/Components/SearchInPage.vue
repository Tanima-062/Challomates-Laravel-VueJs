<template>
    <input class="challo__input search__input" type="text" :placeholder="placeholder" v-model="form.query"
        minlength="1" />
</template>

<script>
import { reactive, ref } from "vue";
import { onMounted, watch } from "@vue/runtime-core";
import { Inertia } from "@inertiajs/inertia";
import { debounce as _debounce } from "lodash";

export default {
    props: {
        placeholder: {
            type: String,
            default: "",
        },
        routeName: {
            type: String,
            required: true,
        },
    },
    setup(props, ctx) {
        const form = reactive({
            query: "",
        });

        onMounted(() => {
            let searchParams = Object.fromEntries(
                new URLSearchParams(location.search)
            );

            form.query = searchParams.hasOwnProperty("query") ? searchParams.query : ''
        });

        watch(
            () => form.query,
            _debounce(function () {
                let searchParams = Object.fromEntries(
                    new URLSearchParams(location.search)
                );

                for (const key in searchParams) {
                    if (Object.hasOwnProperty.call(searchParams, key) && key !== 'page' && key !== 'query') {
                        const element = searchParams[key];
                        form[key] = element;
                    }
                }
                Inertia.visit(
                    route(props.routeName, {
                        ...form
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }, 500)
        );

        watch(() => form.query, () => {
            ctx.emit('update', form.query)
        })

        return {
            form,
        };
    },
};
</script>

<style lang="scss" scoped>
.search__input {
    @media only screen and (max-width: 600px) {
        margin-bottom: 10px;
    }
}
</style>
