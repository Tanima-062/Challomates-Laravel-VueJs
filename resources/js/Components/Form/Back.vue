<template>
    <div class="back-link text-primary-3 mb-9 text-15 font-poppins font-semibold w-fit">
        <a
            @click.prevent="goBack"
            :href="target"
            class="flex items-center cursor-pointer"

        >
            <back-arrow style="" class="mr-3" />Zurück
        </a>
    </div>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Confirmation from "../Modal/Content/Confirmation.vue";
import BackArrow from "../Icons/BackArrow.vue";

import { usePage } from "@inertiajs/inertia-vue3";

export default {
    props: {
        backPrevUrl: {
            type: Boolean,
            default: false,
        },

        target: {
            type: String,
            required: false,
        },

        showModal: {
            type: Boolean,
            requried: false,
            default: true,
        },

        staticBackdrop: {
            type: Boolean,
            default: false,
        },

        message: {
            type: String,
            required: false,
            default: "Änderungen verwerfen?",
        },

        text: {
            type: String,
            requried: false,
            default:
                "Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?",
        },
    },

    components: {
        BackArrow,
    },

    setup(props) {
        const modal = inject("modal");
        let prev_url = usePage().props.value.prev_url;

        const goBack = (e) => {
            e.preventDefault();
            if (!props.showModal) {
                if (props.backPrevUrl && prev_url != null) {
                    Inertia.visit(prev_url);
                    return;
                }

                Inertia.visit(props.target);
                return;
            }
            modal.show(Confirmation, {
                config: {
                    staticBackdrop: props.staticBackdrop,
                },
                props: {
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
                        return;
                    },
                    noClick: () => modal.hide(),
                },
            });
        };

        return {
            goBack,
        };
    },
};
</script>
