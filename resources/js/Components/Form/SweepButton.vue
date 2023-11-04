<template>
    <button class="sweepButton" :class="[classList]" @click.prevent="cancel">{{ buttonText }}</button>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../Modal/Content/Confirmation.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    props: {

        classList: {
            type: String,
            required: false,
        },

        buttonText: {
            type: String,
            default: "I am Button",
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
            default: false,
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

<style lang="scss" scoped>
.sweepButton {
    height: 2.5rem;
    padding: 0.625rem 1.25rem 0.625rem 1.25rem;
    text-align: center;
    font-size: 15px;
    line-height: 22px;
    font-weight: 600;
    --tw-text-opacity: 1;
}
</style>
