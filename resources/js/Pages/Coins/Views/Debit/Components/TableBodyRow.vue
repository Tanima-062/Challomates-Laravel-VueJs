<template>
    <div class="tr py-3 border-b border-white font-normal hover:font-semibold">
        <div class="content gap-[10px]">
            <div class="text-tints-5 w-[20%]">
                {{  dayjs(debit.created_at).format('DD.MM.YYYY HH:mm')  }} Uhr
            </div>
            <div class="text-tints-5 w-[20%]">
                <a target="_blank" :href="route('mobile-app-users.show', { mobile_app_user: debit.mobile_app_user_id })">
                    {{  `${debit.mobile_app_user.first_name} ${debit.mobile_app_user.last_name}`  }}
                </a>
            </div>
            <div class="text-tints-5 w-[25%]">
                {{  debit.sweepstake.description}}
            </div>
            <div class="text-tints-5 w-[15%]">
                <a :href="route('sweepstakes.show', { sweepstake: debit.sweepstake_id })">{{  debit.sweepstake.name
                    }}</a>
            </div>
            <div class="text-tints-5 w-[15%]">
                {{  debit.total_coins  }}
            </div>

            <div class="relative" style="width: 5%">
                <button class="relative" type="button" aria-haspopup="true" aria-expanded="false"
                    @click="showDropdown = true">
                    <menu-bar />
                    <div class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)" v-show="showDropdown"
                        @click.stop="showDropdown = false">
                        <Link class="dropdown-item" :href="route('coins-debit.show', { coins_debit: debit.id })">
                        Details ansehen
                        </Link>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
//vue core
import { ref } from "@vue/reactivity"

//javascript libraries
import dayjs from "dayjs";

// vue components
import { Link } from "@inertiajs/inertia-vue3";
import MenuBar from "../../../../../Components/Icons/MenuBar.vue";

const props = defineProps({
    debit: {
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
