<template>
    <a class="dropdown-item" href="#" @click.prevent="toggle">{{
            status == "active" ? 'Deaktivieren' : 'Aktivieren'
    }}</a>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../../../Components/Modal/Content/Confirmation"
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
        activated_message: {
            type: String,
            required: true,
        },
        deactivated_message: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            default: "Are You Sure?"
        },
        status: {
            type: String,
            default: "inactive",
        },
        staticBackdrop: {
            type: Boolean,
            default: false,
        }
    },
    components: {
        Confirmation,
    },

    setup(props) {
        const modal = inject("modal");
        const toggle = () => {
            const text = props.status === 'active' ? props.deactivated_message : props.activated_message
            modal.show(Confirmation, {
                props: {
                    message: props.title,
                    text,
                },
                config: {
                    staticBackdrop: props.staticBackdrop
                },
                events: {
                    yesClick: () => {
                        modal.hide();
                        Inertia.post(
                            route(props.routeName, props.value),
                            {
                                _method: "put",
                                status: props.status === "active" ? "inactive" : "active",
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
