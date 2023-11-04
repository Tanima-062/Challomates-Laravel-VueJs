<template>
    <div class="challo__card__tabs  -mt-5">
        <div class="challo__card__tabs--bottom">
            <Link :href="route('sales-partner.index')" class="challo__card__tabs--item w-1/2"
                :class="[activeTab == 'sales-partner' ? 'active pointer-events-none font-semibold' : 'font-normal']">
            Vertriebspartner</Link>
            <Link :href="route('contract.index')" class="challo__card__tabs--item w-1/2"
                :class="[activeTab == 'contract' ? 'active pointer-events-none font-semibold' : 'font-normal']">Verträge</Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { computed } from "@vue/runtime-core";

const activeTab = computed(() => {
    const path = location.pathname

    if (path.startsWith('/sales-partner'))
        return 'sales-partner'

    if (path.startsWith('/contract'))
        return 'contract'
})

</script>

<style lang="scss" scoped>
</style>
