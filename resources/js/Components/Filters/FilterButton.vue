<template>
    <button
        class="flex justify-between items-center w-130 px-7 py-2 text-tints-2  rounded-35 mr-14 border"
        :class="{ active: showFilter, 'border-tints-2': showFilter,  'border-0': !showFilter}"
        @click="toggleFilter"
    >
            <filter-icon fill="currentColor" />
        <span class="ml-4">Filter</span>
    </button>
</template>

<script>
import FilterIcon from "../Icons/Filter.vue";
export default {
    components: {
        FilterIcon,
    },
    data() {
        return {
            showFilter: false,
        };
    },

    methods: {
        toggleFilter() {
            this.showFilter = !this.showFilter;
            this.$emit("toggleShowFilter", this.showFilter);
        },
    },
};
</script>

<style lang="scss" scoped>
</style>
