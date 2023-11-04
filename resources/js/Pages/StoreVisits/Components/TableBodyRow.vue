<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)">
        <div class="content" v-if="!option.expand">
            <div class="text-tints-5 w-[20%]">
                <a target="_blank"
                    :href="route('mobile-app-users.show', { mobile_app_user: store_visit.mobile_app_user_id })">
                    {{ store_visit.mobile_app_user.first_name }}
                    {{ store_visit.mobile_app_user.last_name }}
                </a>
            </div>
            <div class="text-tints-5 w-[20%]">
                <a target="_blank" :href="route('sales-partner.show', { sales_partner: store_visit.sales_partner_id })">
                    {{ store_visit.sales_partner.company_name }}
                </a>
            </div>
            <div class="text-tints-5 w-[20%]">
                {{ dayjs(store_visit.check_in_time).format("DD.MM.YYYY HH:mm") }} Uhr
            </div>
            <div class="text-tints-5 w-[20%]">
                {{ store_visit.check_out_time ? dayjs(store_visit.check_out_time).format("DD.MM.YYYY HH:mm") + ' Uhr' :
                        ''
                }}
            </div>
            <div class="text-tints-5 w-[15%]">
                CHF {{ store_visit.turnover }}
            </div>
            <div class="relative" style="width: 5%">
                <button type="button" aria-haspopup="true" aria-expanded="false" @click="showDropdown = true">
                    <menu-bar />
                </button>
                <div class="
              dropdown
              absolute
              bg-white
              rounded-lg
              flex flex-col
              z-10
              shadow-sm
            " v-click-away="() => (showDropdown = false)" @click="showDropdown = false" v-show="showDropdown">
                    <Link class="dropdown-item" :href="
                        route('store-visits.show', {
                            store_visit: store_visit.id,
                        })
                    ">Details ansehen</Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import MenuBar from "../../../Components/Icons/MenuBar.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { reactive, ref } from "@vue/reactivity";
import dayjs from "dayjs";

const props = defineProps({
    store_visit: {
        type: Object,
        required: true,
    },
});

const showDropdown = ref(false);

const option = reactive({
    expand: false,
});
</script>

<style lang="scss" scoped>
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
