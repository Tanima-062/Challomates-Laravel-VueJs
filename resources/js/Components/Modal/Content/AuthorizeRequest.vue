<template>
  <div
    class="modal__confirmation"
    :class="attrs['class']"
    v-bind:style="attrs['style']"
  >
    <div class="row">
      <h5 class="col-12 title">{{ message}}</h5>
      <p class="col-12 description" v-html="text"></p>
      <div class="col-12 reason-input">
          <input :placeholder="placeholder" type="text" class="challo__input" v-model="reason">
      </div>
      <div class="col-12 btns">
        <button
          class="btn yes challo__btn btn-navy-blue"
          @click="$emit('yesClick', reason)"
        >
          {{ "authorize" }}
        </button>
        <button class="btn no vebo_btn btn-mystic" @click="$emit('noClick')">
          {{ "abort" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, useAttrs } from "@vue/runtime-core";
const attrs = useAttrs();
defineProps(["message", "text", 'placeholder']);
</script>

<script>
export default {
  inheritAttrs: false,
  setup(){
      const reason = ref('reason')

      return {
          reason
      }
  }
};
</script>

<style lang="scss" scoped>
    .description {
        padding-left: 2rem;
        padding-right: 2rem;
        padding-top: 1rem;
    }
    .modal__confirmation {
        min-height: 23rem;
        height: auto;
    }
    .reason-input {
        padding: 3rem;
        margin-bottom: 0;
    }
</style>

