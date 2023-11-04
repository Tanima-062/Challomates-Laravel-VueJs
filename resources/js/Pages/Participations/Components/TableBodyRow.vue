<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)"
    >
        <div class="content" v-if="!option.expand">
            <div class="td" style="width: 15%">
                {{ formateDate( participant.created_at, 'DD.MM.YYYY&nbsp;HH:mm') }} Uhr
            </div>
            <div class="td" style="width: 15%">
                <a :href="route('mobile-app-users.show', { mobile_app_user: participant.mobile_app_user_id })" target="_blank" rel="noopener noreferrer">
                    <span style="word-break: break-word;">
                        {{ participant.mobile_app_users_first_name }} {{ participant.mobile_app_users_last_name }}
                    </span>
                </a>
            </div>
            <div class="td" style="width: 15%">
                <a :href="route('sweepstakes.show', { sweepstake : participant.sweepstake_id, ...buildQueryParams() })" target="_blank" rel="noopener noreferrer">
                    <span style="word-break: break-word;">
                        {{ participant.sweepstake_name }}
                    </span>
                </a>
            </div>
            <div class="td break-all flex" style="width: 17%">
                {{ participant.winning_number }}
            </div>
            <div class="td break-all" style="width: 18%">
                {{ formateDate( participant.sweepstakes_raffle_time, 'DD.MM.YYYY HH:mm')}}
            </div>
            <div class="td" style="width: 15%">
                <SweepstakeStatus :status="participant.status_with_published_status" />
            </div>
            <div class="td relative" style="width: 5%">
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
                            :href="route('participation.show', { participation: participant.id, ...buildQueryParams()})"
                            v-if="hasPermission('sweepstakes.view')"
                        >Details ansehen
                        </Link>
                        <!-- <Link
                            class="dropdown-item"
                            :href="route('sweepstakes.edit', { sweepstake: sweepstake.id})"
                            v-if="hasPermission('sweepstakes.edit')"
                            >{{ $t("Edit") }}
                        </Link>
                        <Link
                            class="dropdown-item"
                            href="#"
                            v-if="hasPermission('sweepstakes.edit')"
                            >{{ $t("Cancel") }}
                        </Link>
                        <Link
                            class="dropdown-item"
                            href="#"
                            v-if="hasPermission('sweepstakes.edit')"
                            >{{ $t("Publish") }}
                        </Link> -->
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
import SweepstakeStatus from "./SweepstakeStatus";
const props = defineProps({
    participant: {
        type: Object,
        required: true,
    },
});

const buildQueryParams = () => {
    return Object.fromEntries(
        new URLSearchParams(location.search)
    );
};

const showDropdown = ref(false);

const option = reactive({
    expand: false,
});
</script>

<style lang="scss" scoped>

.content {
    .td {
        font-family: 'Poppins';
        font-style: normal;
        // font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #135F84;
        font-weight: inherit;
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
        font-family: 'Poppins';
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
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135F84;
        display: block;
    }

    .subtitle {
        font-family: 'Poppins';
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}

</style>
