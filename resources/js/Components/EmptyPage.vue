<template>
  <div class="empty-page">
    <not-found />
    <h1 class="message">Keine Daten vorhanden</h1>
  </div>
</template>

<script>
import NotFound from "../Components/Icons/NotFound.vue";
export default {
  components: {
    NotFound,
  },
};
</script>

<style lang="scss" scoped>
.empty-page {
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.message {
  /* B.H2 - 32 px */
  font-family: 'Poppins', sans-serif;
  font-style: normal;
  font-weight: 700;
  font-size: 2.7rem;
  line-height: 38px;

  /* Shades/3 */
  color: #7292a9;
}
</style>
