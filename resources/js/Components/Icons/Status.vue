<template>
  <svg
    width="10"
    height="10"
    viewBox="0 0 10 10"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <circle cx="5" cy="5" r="5" :fill="fillColor" />
  </svg>
</template>

<script>
export default {
  props: {
    active: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    fillColor() {
      return this.active ? "#00AC42" : "#C81717";
    },
  },
};
</script>

<style lang="sass" scoped>
</style>
