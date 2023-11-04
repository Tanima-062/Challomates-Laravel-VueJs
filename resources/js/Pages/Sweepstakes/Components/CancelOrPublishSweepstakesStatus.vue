<template>
    <a class="dropdown-item" href="#" @click.prevent="toggle">{{ label }}</a>
</template>

<script>
import {inject} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Confirmation from "../../../Components/Modal/Content/Confirmation"
import FieldMissing from "../../../Components/Modal/Content/FieldMissing";

export default {
    props: {
        routeName: {
            type: String,
            required: true,
        },
        staticBackdrop: {
            type: Boolean,
            default: false
        },
        value: {
            type: Number,
            required: true,
        },
        canceled_message: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            default: 'Gewinnspiel abbrechen?'
        },
        attributes: {
            type: Object,
            required: true,
        },
        status: {
            type: String,
            default: 'canceled',
        },
        label: {
            type: String,
            required: true
        },
        showError: {
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
            const messageText = props.canceled_message.replace(':sweepstake_name', props.attributes.sweepstake_name);
            const title = props.title;
            modal.show(Confirmation, {
                props: {
                    text: messageText,
                    message: title
                },
                config: {
                    staticBackdrop: false
                },
                events: {
                    yesClick: () => {
                        modal.hide();
                        Inertia.post(
                            route(
                                props.routeName, props.value),
                            {
                                    _method: "put",
                                ...buildQueryParams()
                            },
                            {
                                preserveScroll: true,
                                onError: (errors) => {
                                    if (props.showError) {
                                        modal.show(FieldMissing, {
                                            config: {
                                                staticBackdrop: true,
                                            },
                                            props: {
                                                title: 'Start des Gewinnspiels in der Vergangenheit',
                                                description: 'Das Gewinnspiel kann nicht publiziert werden, da der\n' +
                                                    'Start-Zeitpunkt (Laufzeit “Von”) in der Vergangenheit liegt.\n' +
                                                    'Bitte erfassen Sie ein Datum, welches aktuell in der Zukunft liegt, um dieses Gewinnspiel publizieren zu können.',
                                            }
                                        });
                                    }
                                },
                            }
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
        };
    },
};
</script>

<style lang="scss" scoped>
</style>
