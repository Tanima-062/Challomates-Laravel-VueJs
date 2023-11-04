<template>
  <a class="dropdown-item" href="#" @click.prevent="toggle">{{
    active ? "deaktiviert" : "Aktivieren"
  }}</a>
</template>

<script>
import { inject } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { trans } from "laravel-vue-i18n";

import Confirmation from "../Modal/Content/Confirmation.vue";
export default {
  props: {
    title: {
        type: String,
        default: "Are You Sure?",
    },
    active: {
      type: Boolean,
      default: false,
    },
    routeName: {
      type: String,
      required: true,
    },
    value: {
      type: Number,
      required: true,
    },
    column: {
      type: String,
      default: "status",
    },
    message: {
      type: String,
      required: true,
    },
    text: {
      type: String,
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
      modal.show(Confirmation, {
        props: {
          message: props.title,
          text: trans(`are_you_sure_you_really_want_to_${
            props.active ? "deactivate" : "activate"
          }_the_${props.message}_:company`, {'company': props.text}) + "?",
        },
        events: {
          yesClick: () => {
            modal.hide();
            Inertia.post(
              route(props.routeName, props.value),
              {
                _method: "put",
                column: props.column,
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
