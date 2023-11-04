<template>
    <div
        class="tr py-3 border-b border-white font-normal hover:font-semibold"
        v-click-away="() => (option.expand = false)"
    >
        <div class="content" v-if="!option.expand">
            <div
                class="td"
                :style="hasPermission('raffles.edit') ? 'width: 12%' : 'width: 13%'"
            >
                <Link
                    class="dropdown-item"
                    :href="route('raffles.show', { raffle: raffle.raffle_id })"
                    v-if="hasPermission('sweepstakes.view')"
                >
                    {{ raffle.raffle_prefix_id }}
                </Link>
            </div>
            <div class="td" style="width: 14%">
        <span>
          <Link
              style="word-break: break-word;"
              class="dropdown-item"
              :href="
              route('sweepstakes.show', {
                sweepstake: raffle.raffle_sweepstake_id,
                ...buildQueryParams(),
              })
            "
              v-if="hasPermission('sweepstakes.view')"
          >
            {{ raffle.raffle_sweepstake_name }}
          </Link>
        </span>
            </div>
            <div
                class="td"
                :style="hasPermission('raffles.edit') ? 'width: 10%' : 'width: 12%'"
            >
                {{ raffle.raffle_sweepstake_type === "sweepstake" ? "Gewinnspiel" : "Jackpot" }}
            </div>
            <div
                class="td"
                :style="hasPermission('raffles.edit') ? 'width: 16%' : 'width: 18%'"
            >
                {{ formateDate(raffle.raffle_time, "DD.MM.YYYY&nbsp;&nbsp;&nbsp;HH:mm") }}
            </div>
            <div
                class="td"
                :style="hasPermission('raffles.edit') ? 'width: 15%' : 'width: 17%'"
            >
                {{ raffle.raffle_winning_number_position_from }} -
                {{ raffle.raffle_winning_number_position_to }}
            </div>
            <div v-if="hasPermission('raffles.edit')" class="td" style="width: 8%">
                <!-- {{ raffle.raffle_launch }} -->
                <VideoStreamingIconButton
                    v-if="!raffle.raffle_video_src_path"
                    @click="showStreamingModal"
                />
            </div>
            <div
                class="td"
                :style="hasPermission('raffles.edit') ? 'width: 15%' : 'width: 16%'"
            >
                <template v-if="raffle.raffle_mobile_app_user_id !== null">
                    <a
                        target="_blank"
                        :set="(mobile_app_user_list = raffle.raffle_mobile_app_user_id.split(','))"
                        v-for="n in (total_participant =
              raffle.raffle_mobile_app_user_id.split(',').length > 2 ? 2 : 1)"
                        :key="n"
                        :href="
              route('mobile-app-users.show', {
                mobile_app_user: mobile_app_user_list[n - 1],
              })
            "
                    >
                        {{ n > 1 ? ", " : "" }}
                        {{ raffle.mobile_app_user_full_name.split(",")[n - 1] }}
                    </a>

                    <ShowMoreWinnerList
                        v-if="raffle.raffle_mobile_app_user_id.split(',').length > 2"
                        :mobile_app_user_id="raffle.raffle_mobile_app_user_id.split(',')"
                        :mobile_app_user_full_name="raffle.mobile_app_user_full_name.split(',')"
                    />
                </template>
                <template v-else>-</template>
            </div>
            <div class="td" style="width: 8%">
                <!-- {{ raffle.video }} -->
                <VideoPlayIconButton
                    v-if="raffle.raffle_video_src_path !== null"
                    :video-name="raffle.raffle_video_src_path"
                />
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
                        class="dropdown absolute bg-white rounded-lg flex flex-col z-10 shadow-sm"
                        v-click-away="() => (showDropdown = false)"
                        v-show="showDropdown"
                    >
                        <Link
                            class="dropdown-item"
                            :href="route('raffles.show', { raffle: raffle.raffle_id })"
                            v-if="hasPermission('sweepstakes.view')"
                        >Details ansehen
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
import { reactive, ref } from "@vue/reactivity";
import VideoPlayIconButton from "./VideoPlayIconButton";
import VideoStreamingIconButton from "./VideoStreamingIconButton";
// import VideoRecorder from "./VideoRecorder.vue";
import VideoRecorder from "./LiveStreaming.vue";
// import VideoRecorder from "./RTMPVideoRecorder.vue";
import ShowMoreWinnerList from "./ShowMoreWinnerList";
import { inject } from "vue";
import RaffleWinnerCapture from "./RaffleWinnerCapture";
const props = defineProps({
    raffle: {
        type: Object,
        required: true,
    },
});

const buildQueryParams = () => {
    return Object.fromEntries(new URLSearchParams(location.search));
};

const modal = inject("modal");
const showDropdown = ref(false);
const option = reactive({
    expand: false,
});

const showStreamingModal = () => {
    modal.show(VideoRecorder, {
        props: {
            streamId: props.raffle.raffle_id,
        },
        config: {
            staticBackdrop: true,
            overlayStyle: "background: rgb(120, 120, 120, 0.5)",
        },
        events: {
            streamComplete: (videoFileName) => {
                //modal.hide();
                modal.show( RaffleWinnerCapture, {
                    props: {
                        video_src_path: videoFileName,
                        raffle_id: props.raffle.raffle_id,
                        winning_number_position: props.raffle.raffle_total_sweepstake_number_position,
                    },

                    config: {
                        staticBackdrop: true,
                        overlayStyle: "background: rgb(120, 120, 120, 0.5)",
                    },

                    events: {
                        winnerCLick: () => {
                            window.location.reload();
                        }
                    }
                } )
            },
            close: modal.hide
        },
    });
};
</script>

<style lang="scss" scoped>
.content {
    gap: 10px;

    .td {
        /*border: 1px solid green;*/
        font-family: "Poppins", serif;
        font-style: normal;
        // font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        color: #135f84;
        font-weight: inherit;
        word-break: break-word;
    }
}

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
        font-family: "Poppins", serif;
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

.remaining {
    display: flex;
    flex-direction: column;

    .title {
        font-family: "Poppins", serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;

        /* Tints / 5 */

        color: #135f84;
        display: block;
    }

    .subtitle {
        font-family: "Poppins", serif;
        font-style: normal;
        font-weight: 400;
        font-size: 9px;
        line-height: 14px;

        /* Gray / 3 */

        color: #787878;
    }
}
</style>
