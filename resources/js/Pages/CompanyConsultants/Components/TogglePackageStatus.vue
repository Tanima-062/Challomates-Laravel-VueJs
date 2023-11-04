<template>
    <a class="dropdown-item" href="#" @click.prevent="toggle">{{
            status == "active" ? 'Deaktivieren' : 'Aktivieren'
    }}</a>
</template>

<script>
import {inject} from "vue";
import {Inertia} from "@inertiajs/inertia";
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
        attributes: {
            type: Object,
            required: true,
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
            const text = props.status === 'active' ? props.deactivated_message.replace( ':consultant_name', props.attributes.consultant_name ) : props.activated_message.replace( ':consultant_name', props.attributes.consultant_name )
            modal.show(Confirmation, {
                props: {
                    message: props.title,
                    text,
                },
                events: {
                    yesClick: () => {
                        modal.hide();
                        Inertia.post(
                            route(props.routeName, props.value),
                            {
                                _method: "put",
                                ...buildQueryParams()
                            },
                            { preserveScroll: true }
                        );
                    },
                    noClick: () => modal.hide(),
                },
            });
        };

        const buildQueryParams = () => {
            return Object.fromEntries(
                new URLSearchParams(location.search)
            );
        };

        return {
            toggle,
            buildQueryParams
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
