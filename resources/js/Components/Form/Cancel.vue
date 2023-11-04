<template>
    <button class="btn-block challo__btn btn-outline-primary" @click.prevent="cancel">
        Abbrechen
    </button>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../Modal/Content/Confirmation.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    props: {
        target: {
            type: String,
            required: true,
        },

        class: {
            type: String,
            required: false,
            default: "top",
        },

        message: {
            type: String,
            default: "Änderungen verwerfen?",
        },

        backPrevUrl: {
            type: Boolean,
            default: false,
        },

        showModal: {
            type: Boolean,
            default: true,
        },

        staticBackdrop: {
            type: Boolean,
            default: true,
        },

        text: {
            type: String,
            default:
                "Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?",
        },
    },
    components: {
        Confirmation,
    },

    setup(props) {
        const modal = inject("modal");
        let prev_url = usePage().props.value.prev_url;

        const cancel = (e) => {
            e.preventDefault();

            if (!props.showModal) {
                if (props.backPrevUrl && prev_url !== null) {
                    Inertia.visit(prev_url);
                } else {
                    Inertia.visit(props.target);
                }
                return;
            }

            modal.show(Confirmation, {
                config: {
                    staticBackdrop: props.staticBackdrop,
                },
                props: {
                    class: "top",
                    message: props.message,
                    text: props.text,
                },
                events: {
                    yesClick: () => {
                        modal.hide();
                        if (props.backPrevUrl && prev_url != null) {
                            Inertia.visit(prev_url);
                            return;
                        }

                        Inertia.visit(props.target);
                    },
                    noClick: () => {
                        modal.hide();
                    },
                },
            });
        };
        return {
            cancel,
        };
    },
};
</script>
