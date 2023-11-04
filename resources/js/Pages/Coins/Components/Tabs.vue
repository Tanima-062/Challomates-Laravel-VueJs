<template>
    <div class="challo__card__tabs">
        <div class="challo__card__tabs--bottom">
            <Link :href="route('coins-credit.index')" class="challo__card__tabs--item w-1/2"
                :class="[activeTab == 'coins-credit' ? 'active pointer-events-none font-semibold' : 'font-normal']">
            Gutschriften</Link>
            <Link :href="route('coins-debit.index')" class="challo__card__tabs--item w-1/2"
                :class="[activeTab == 'coins-debit' ? 'active pointer-events-none font-semibold' : 'font-normal']">Belastungen</Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { computed } from "@vue/runtime-core";

const activeTab = computed(() => {
    const path = location.pathname

    if (path.startsWith('/coins-credit'))
        return 'coins-credit'

    if (path.startsWith('/coins-debit'))
        return 'coins-debit'
})

</script>

<style lang="scss" scoped>
</style>
