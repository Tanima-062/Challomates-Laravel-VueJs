<template>
  <div
    :id="id"
    class="challo__modal"
    :class="[{ active: modalShowing, 'light-box': config.enableLightbox }]"
    ref="modalRoot"
  >
    <div
      class="overlay justify-center hidden w-full h-full"
      @click="checkThenHide"
      :style="config.overlayStyle"
      ref="overlay"
    >
      <component
        :is="ContentComponent"
        v-on="childEvents"
        v-bind="childProps"
        @hide="hide"
      >
      </component>
    </div>
  </div>
  <slot />
</template>

<script setup>
import { nextTick, provide, ref, shallowRef } from "@vue/runtime-core";

const initialInfo = {
  props: { class: "center" },
  events: {},
  config: {
    staticBackdrop: false,
    enableLightbox: false,
    timeOut: false,
    overlayStyle: "",
  },
};
const ContentComponent = shallowRef();
const overlay = ref(null);
const modalRoot = ref(null);

let childProps = ref(initialInfo.props);
let childEvents = ref(initialInfo.events);
let config = ref(initialInfo.config);
let modalShowing = ref(false);

defineProps({
  id: {
    type: String,
    required: false,
    default: "challo__modal--container",
  },
});

/**
 * This method will make new instance of the component that passed in as parameter and show it
 *
 * @param {Component} content
 * @param additionalData
 * @param {Object} additionalData.props This parameter will be passed to the Component as props and attributes
 * @param {Object} additionalData.events This parameter will be passed to the Component as event with v-on
 * @param {Object} additionalData.config This parameter will be passed to the Component as event with v-on
 */
function show(content, additionalData = initialInfo) {
  overlay.value.classList.remove("hidden");
  overlay.value.classList.add("flex");
  modalShowing.value = true;
  ContentComponent.value = undefined;
  childProps.value = { ...initialInfo.props, ...additionalData.props };
  childEvents.value = { ...initialInfo.events, ...additionalData.events };
  config.value = { ...initialInfo.config, ...additionalData.config };
  nextTick(() => {
    ContentComponent.value = content;
    if (config.value.timeOut) {
      setTimeout(hide, config.value.timeOut);
    }
  });
}

/**
 * This method will hide the modal
 *
 */
function hide() {
  modalShowing.value = false;
  overlay.value.classList.add("hidden");
  overlay.value.classList.remove("flex");
  disableLightBox();
}

/**
 * This method is going to check if the staticBackdrop option is true? If true it will not hide
 * the modal on backgorund click else it will hide the modal.
 */
function checkThenHide(e) {
  if (
    (e.target !== overlay.value && e.target.contains(e.target)) ||
    config.value.staticBackdrop
  ) {
    return;
  }
  hide();
}

function disableLightBox() {
  config.value.enableLightbox = false;
}

function addClass(className) {
  modalRoot.value.classList.add(className);
}

function removeClass(className) {
  modalRoot.value.classList.remove(className);
}

provide("modal", {
  show,
  hide,
  addClass,
  removeClass,
});
</script>

<style lang="scss" scoped>
.challo__modal {
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: -1;

  &.active {
    z-index: 3;
  }

  &.light-box {
    background: rgb(120 120 120 / 65%);
  }
}
</style>
