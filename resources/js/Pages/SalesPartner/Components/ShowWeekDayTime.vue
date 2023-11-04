<template>
  <div
    class="weekday-table rounded-[24px] border-[1px] border-gray-corner w-2/3 bg-[#ffffff80] overflow-hidden mb-[56px]"
  >
    <div class="head border-b-[1px] border-gray-corner">
      <div class="row flex divide-x-[1px] divide-gray-corner">
        <div
          class="column py-2 text-gray-3 text-15 font-semibold font-poppins w-[26%] !border-l-[0px]"
        ></div>
        <div
          class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins"
        >
          Von
        </div>
        <div
          class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins"
        >
          Bis
        </div>
        <div
          class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins"
        >
          Von
        </div>
        <div
          class="column w-[17%] py-2 px-5 text-gray-3 text-15 font-semibold font-poppins"
        >
          Bis
        </div>
      </div>
    </div>

    <div class="body divide-y-[1px] divide-gray-corner">
      <div
        class="row flex divide-x-[1px] divide-gray-corner"
        v-for="opening_hour in opening_hours"
      >
        <div
          class="column py-2 w-[26%] !border-l-[0px] text-18x font-ropa px-3 text-gray-3"
        >
          {{ getWeekNameTranslation(opening_hour.day) }}
        </div>
        <div class="column w-[17%]">
          <p class="text-tints-5 text-18x px-5 py-2">
            {{ opening_hour.first_start_time }}
          </p>
        </div>
        <div class="column w-[17%]">
          <p class="text-tints-5 text-18x px-5 py-2">
            {{ opening_hour.first_end_time }}
          </p>
        </div>
        <div class="column w-[17%]">
          <p class="text-tints-5 text-18x px-5 py-2">
            {{ opening_hour.last_start_time }}
          </p>
        </div>
        <div class="column w-[17%]">
          <p class="text-tints-5 text-18x px-5 py-2">
            {{ opening_hour.last_end_time }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  opening_hours: {
    type: Array,
    required: true,
  },
});
</script>
