<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold">
        <div class="content flex">
            <div class="text-tints-5 w-[15%]">
                <a target="_blank"
                    :href="route('mobile-app-users.show', { mobile_app_user: credit.mobile_app_user_id })">
                    {{  credit.mobile_app_user.first_name  }} {{  credit.mobile_app_user.last_name  }}
                </a>
            </div>

            <div class="text-tints-5 w-[15%]">
                <a target="_blank" :href="route('sales-partner.show', { sales_partner: credit.sales_partner_id })">
                    {{  credit.sales_partner.company_name  }}
                </a>
            </div>

            <div class="text-tints-5 w-[25%]">
                {{  dayjs(credit.check_in_time).format('DD.MM.YYYY HH:mm')  }}
                -
                {{  dayjs(credit.check_out_time).format('DD.MM.YYYY HH:mm')  }}

            </div>


            <div class="text-tints-5 w-[15%]">
                {{credit.posting_coins}}
            </div>

            <div class="text-tints-5 w-[10%]">
                {{  credit.coin  }}
            </div>

            <div class="text-tints-5 w-[15%]">
                {{  credit.total_coins  }}
            </div>

            <div class="text-tints-5 relative w-[5%]">
                <button class="relative" type="button" aria-haspopup="true" aria-expanded="false"
                    @click="showDropdown = true">
                    <menu-bar />
                    <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)" v-show="showDropdown"
                        @click.stop="showDropdown = false">
                        <Link class="dropdown-item" :href="route('coins-credit.show', { coins_credit: credit.id })">
                        Details ansehen
                        </Link>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import dayjs from "dayjs";
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../../../Components/Icons/MenuBar.vue";
import { Inertia } from "@inertiajs/inertia";
import { ref } from "@vue/reactivity"
const props = defineProps({
    credit: {
        type: Object,
        required: true,
    },
});
const showDropdown = ref(false);

</script>

<style lang="scss">
.dropdown {
    right: 0;
    top: 20px;
    width: max-content;

    .dropdown-item {
        border-bottom: 1px solid #8ed5f6;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 30px;
        font-family: "Poppins";
        font-style: normal;
        font-weight: 400;
        font-size: 13px;
        line-height: 20px;
        color: #135f84;
        text-align: left;

        &:hover {
            font-weight: 600;
        }

        &:last-child {
            border-bottom: none;
        }
    }
}
</style>
