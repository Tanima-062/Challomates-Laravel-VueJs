<template>
  <div class="challo__card py-8 px-6 -mt-2">
    <video ref="videoTag" class="relative w-full hidden" controls autoplay></video>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { ref } from "@vue/reactivity";
</script>
