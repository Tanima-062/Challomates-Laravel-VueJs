<template>
    <a class="dropdown-item" href="#" @click.prevent="toggle">{{
            status == "active" || status == "new" ? "Deaktivieren" : "Aktivieren"
    }}</a>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";

import Confirmation from "../Modal/Content/Confirmation.vue";
export default {
    props: {
        routeName: {
            type: String,
            required: true,
        },
        value: {
            type: Number,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            default: "Sind Sie sicher?"
        },
        status: {
            type: String,
            default: "inactive",
        },
    },
    components: {
        Confirmation,
    },

    setup(props) {
        const modal = inject("modal");
        const toggle = () => {
            modal.show(Confirmation, {
                props: {
                    message: props.title,
                    text: props.message,
                },
                events: {
                    yesClick: () => {
                        modal.hide();
                        Inertia.post(
                            route(props.routeName, props.value),
                            {
                                _method: "put",
                                status: props.status == "active" ? "inactive" : "active",
                            },
                            { preserveScroll: true }
                        );
                    },
                    noClick: () => modal.hide(),
                },
            });
        };

        return {
            toggle,
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
