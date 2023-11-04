<template>
  <a class="dropdown-item" href="#" @click.prevent="toggle">{{ status }}</a>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";

import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
export default {
  props: {
    routeName: {
      type: String,
      required: true,
    },
    status: {
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
      default: "Sind Sie sicher?",
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

<style lang="scss" scoped></style>
