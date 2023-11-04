<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
    >
        <div class="content">
            <div class="td" style="width: 20%">
                {{ formateDate(jackpot.check_out_time, 'DD.MM.YYYY&nbsp;&nbsp;&nbsp;HH:mm') }}
            </div>

            <div class="td" style="width: 20%">
                <a :href="route('mobile-app-users.show', { mobile_app_user: jackpot.mobile_app_user_id })" target="_blank" rel="noopener noreferrer">
                    <span style="word-break: break-word;">
                        {{ jackpot.mobile_app_user_full_name }}
                    </span>
                </a>
            </div>
            <div class="td" style="width: 15%">
                {{ mobile_app_user_type[jackpot.mobile_app_user_type] }}
            </div>
            <div class="td" style="width: 15%">
                <a :href="route('sales-partner.show', { sales_partner: jackpot.store_id })" target="_blank" rel="noopener noreferrer">
                    <span style="word-break: break-word;">
                        {{ jackpot.store_name }}
                    </span>
                </a>
            </div>
            <div class="td" style="width: 13%">
                CHF {{ ( parseFloat(jackpot.turn_over) || 0 ).toFixed(2) }}
            </div>
            <div class="td" style="width: 15%">
                CHF {{ ( parseFloat(jackpot.jackpot_share) || 0 ).toFixed(2) }}
            </div>
            <div class="td relative" style="width: 2%">
                <button
                    class="relative"
                    type="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                    @click="showDropdown = true"
                >
                    <menu-bar />
                    <div
                        class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm "
                        v-click-away="() => (showDropdown = false)"
                        v-show="showDropdown"
                    >
                        <Link
                            class="dropdown-item"
                            :href="route('jackpot.show', { jackpot: jackpot.visit_id})"
                            v-if="hasPermission('jackpot.view')"
                        >
                            Details ansehen
                        </Link>
                    </div>
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { trans } from "laravel-vue-i18n";
import { reactive, ref } from "@vue/reactivity";
import { computed } from "@vue/reactivity";

const props = defineProps({
    jackpot: {
        type: Object,
        required: true,
    },
});

const mobile_app_user_type = {
    'distribution_consumer' : 'Vertriebskonsument',
    'direct_consumer' : 'Direkter Konsument',
}

const showDropdown = ref(false);

</script>

<style lang="scss" scoped>


.content {
    gap: 10px;
    .td {
        font-family: 'Poppins',serif;
        font-style: normal;
        // font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #135F84;
        font-weight: inherit;
        /*border: 1px solid green;*/
    }
}

.dropdown {
    right: 0;
    top: 20px;
    width: max-content;

    .dropdown-item {
        border-bottom: 1px solid #8ED5F6;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 30px;
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        color: #135F84;
        text-align: left;

        &:hover {
            font-weight: 600;
        }

        &:last-child {
            border-bottom: none;
        }
    }
}

.remaining {
    display: flex;
    flex-direction: column;

    .title {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135F84;
        display: block;
    }

    .subtitle {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}

</style>
