<template>
  <slot />
</template>

<script setup>
import { inject, reactive, watch } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import FlashNotification from "../Components/Modal/Content/FlashNotification";

const sessionData = usePage().props;
const modal = inject("modal");

watch(sessionData, () => {
  if (sessionData.value.flash.message) {
    modal.show(FlashNotification, {
      props: {
        message: sessionData.value.flash.message,
        styles: sessionData.value.flash.message?.style,
        className: sessionData.value.flash.message?.class,
      },
      config: {
        timeOut: parseInt(sessionData.value.flash.message?.timeout) || 3000,
      },
    });
    sessionData.value.flash.message = null;
  }
});
</script>


