<template>
    <div class="raffle_winner_capture_modal flex flex-col" :class="attrs['class']" v-bind:style="attrs['style']">
        <h5 class="title">Verlosungsinformationen erfassen</h5>
        <h6 class="sub-title">Gewinnzahlen</h6>
        <div class="description flex flex-wrap gap-[26px]" ref="numberContainer">
            <div class="input-wrapper" v-for="(rank, index) in winning_number_position" :key="index">
                <TextInputWithPrefixAndSuffixText
                    v-model="winningNumber[index]"
                    ref="winningNumberArr[index]"
                    prefixClass="winner_cap_button_prefix"
                    :buttonPrefixText="rank.toString().concat( '. Zahl' )"
                    classLists="h-10"
                    placeholder="_"
                    maxlength="1"
                    inputType="number"
                />
            </div>
        </div>

        <div class="btns flex justify-between">
            <button class="no challo__btn btn-outline-primary  btn-block w-1/2 mr-4" style="color: #44B8F1" @click="openVideoPlayer">
                <VideoPlayIconButton
                    video-name=""
                    :show-label-text="true"
                    label-text="Video ansehen"
                />
            </button>
            <button class="yes challo__btn btn-success btn-block w-1/2 ml-4" @click="createWinner">Gewinnzahlen bestätigen</button>
        </div>

        <RaffleOverviewVideoPlayer
            v-show="propOpenVideoPlayer"
            :video-name="video_src_path"
            @closeVideoPlayer="closeVideoPlayerBox"
        />
    </div>
</template>

<script>

import TextInputWithPrefixAndSuffixText from "../../../Components/Form/TextInputWithPrefixAndSuffixText.vue"
import RaffleOverviewVideoPlayer from "./RaffleOverviewVideoPlayer"
import VideoPlayIconButton from "./VideoPlayIconButton"
import { useAttrs } from "vue";
import {computed, reactive, ref} from "@vue/reactivity";
import axios from "axios";
import route from "../../../../../vendor/tightenco/ziggy/src/js";

export default {

    components: {
        TextInputWithPrefixAndSuffixText,
        RaffleOverviewVideoPlayer,
        VideoPlayIconButton
    },

    data() {
        return {
            winningNumber: [],
            propOpenVideoPlayer: this.open_video_player
        }
    },

    props: {
        open_video_player: {
            type: Boolean,
            default: false,
        },
        raffle_id: {
            type: [String, Number],
            required: true,
        },

        winning_number_position: {
            type: [String, Number],
            required: true,
        },

        video_src_path: {
            type: String,
            default: '',
        }
    },

    setup(props) {
        const attrs = useAttrs();
        const winningNumberArr = ref(null);
        const numberContainer = ref(null);
        const media = ref(null);

        return {
            attrs,
            ref,
            winningNumberArr,
            numberContainer,
            media,
        }
    },

    methods: {
        createWinner() {
            /*for (let number in this.$refs.winningNumberArr) {
                console.log(this.$refs.winningNumberArr[number].value)
                if ( this.winningNumber[number] === undefined || this.winningNumber[number].toString().length === 0 ) {
                    this.$refs.winningNumberArr[number].classList.add( 'tingtong' )
                }
            }*/

            axios.put(
                route( 'raffles.capture-winner', { raffle_id: this.raffle_id } ),
                {
                    winning_number: this.winningNumber
                }
            ).then( res => {
                this.$emit('winnerCLick');
            } ).catch( error => {
            } );
        },

        closeVideoPlayerBox(media) {
            this.media = media;
            this.media.pause();
            this.propOpenVideoPlayer = false;
        },

        openVideoPlayer() {
            if ( this.media !== null ) {
                this.media.play();
            }
            this.propOpenVideoPlayer = true;
        }
    }
};
</script>

<style lang="scss">
    .raffle_winner_capture_modal {
        width: 959px;
        padding: 32px 26px 14px 26px;
        background-color: #fff;
        box-shadow: 0 4px 4px rgb(0 0 0 / 10%);
        height: fit-content;
        border-radius: 24px;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        margin: 0 auto;
        transform: translateY(-50%);
        /* border: 1px solid #FFBA49; */
        z-index: 3;
    }

    .title {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 600;
        font-size: 22px;
        line-height: 33px;
        text-align: left;
        color: #1AA1E4;
        margin-bottom: 40px;
    }

    .sub-title {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 600;
        font-size: 15px;
        line-height: 22px;
        text-align: left;
        color: #1AA1E4;
        margin-bottom: 16px;
    }

    .description {
        font-family: 'Poppins',serif;
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 24px;
        // display: flex;
        // align-items: center;
        // text-align: center;
        color: #135F84;
        text-align: center;


        .input-wrapper {
            width: 129px;

            .winner_cap_button_prefix {
                font-size: 16px;
                line-height: 17px;
                color: #1AA1E4;
            }

            .challo__input {
                padding-right: 2.45rem;
            }
        }
    }

    .btns {
        margin-top: 32px;
    }
</style>

