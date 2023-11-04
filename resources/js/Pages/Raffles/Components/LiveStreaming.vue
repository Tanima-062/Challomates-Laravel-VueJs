<template>
    <div
        class="flex w-[1200px] items-center justify-center flex-col bg-white min-h-[300px] absolute top-[50%] translate-y-[-50%] z-10 border-[1px] border-secondary-2 shadow-[0_4px_4px_rgb(0 0 0 / 10%)]">
        <video
            ref="videoTag"
            class="relative w-full max-w-[1200px] max-h-[450px]"
            :class="[streamStatus == 'started' ? '' : 'hidden']"
            autoplay
            muted
            controls
            @pause="pauseStream"
            @play="resumeStream"
            id="videoTag"
        ></video>
        <VideoStreamingStartButton
            v-show="streamStatus == 'closed'"
            ref="startButton"
            @click="startStream"
        />

        <button
            class="w-[282px] h-[39px] bg-primary-2 rounded-[45px] text-white my-5"
            v-if="streamStatus == 'started'"
            @click="showEndStreamModal = true"
            :disabled="showEndStreamModal"
        >
            Verlosung beenden
        </button>

        <span
            class="close cursor-pointer absolute top-0 right-[-25px]"
            @click="$emit('close', videoTag)"
            v-show="streamStatus == 'closed'"
        >
            <Cross
                height="14px"
                width="14px"
            />
        </span>

        <Confirmation
            message="Verlosung beenden?"
            text="Das Beenden der Verlosung kann nicht  rückgängig gemacht werden. Sind Sie sicher, dass Sie die Verlosung wirklich beenden wollen?"
            v-if="showEndStreamModal"
            @noClick="showEndStreamModal = false"
            @yesClick="endStream"
        />
    </div>
</template>

<script>
// import { reactive, ref } from "@vue/reactivity";
import { getCurrentInstance, inject, watch } from "vue";
import VideoStreamingStartButton from "./VideoStreamingStartButton";

import axios from "axios";
import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
import Cross from "../../../Components/Icons/Cross.vue"

import { WebRTCAdaptor} from '@antmedia/webrtc_adaptor'

export default {
    components: {
        VideoStreamingStartButton,
        Cross,
        Confirmation
    },
    emits: ['streamComplete'],
    props: {
         streamId: {
            type: [Number, String],
            required: true,
        },
    },
    data(){
        return {
            showEndStreamModal: false,
            streamStatus: 'closed',
            webRTCAdaptor: null,
            liveStreamName: null,
            file_url: null,
            playback_url: null,
        }
    },
    computed:{
        serverUrl(){
            // return `mithunhalder.com:5443/WebRTCApp`;
            // return `mithunhalder.com:5443/WebRTCAppEE`;
            return `strm.challomates.com:5443/WebRTCAppEE`;
        }
    },
    methods: {
        startStream(){
            const webRTCAdaptor = new WebRTCAdaptor({
                websocket_url: `wss://${this.serverUrl}/websocket`,
                mediaConstraints: {
                    video: true,
                    audio: true,
                },
                peerconnection_config: {
                    'iceServers': [
                        {'urls': 'stun:stun1.l.google.com:19302'},
                        {'urls': 'stun.services.mozilla.com:3478'},
                        {'urls': 'stun2.l.google.com:19302'},
                        {'urls': 'stun3.l.google.com:19302'},
                        {'urls': 'stun4.l.google.com:19302'},
                        {'urls': 'iphone-stun.strato-iphone.de:3478'},
                    ]
                },
                sdp_constraints: {
                    OfferToReceiveAudio : false,
                    OfferToReceiveVideo : false,
                },
                // debug:true,
                localVideoId: "videoTag", // <video id="id-of-video-element" autoplay muted></video>
                // bandwidth: int|string, // default is 900 kbps, string can be 'unlimited'
                // dataChannelEnabled: true|false, // enable or disable data channel
                dataChannelEnabled: true, // enable or disable data channel
                callback: (info, obj) => {
                    // console.log(info)
                    if (info == "publish_started") {
                        // alert("publish started");
                    } else if (info == "publish_finished") {
                        // alert("publish finished");
                    }
                    else {
                        // console.log(info + " notification received");
                    }
                    if(info == 'initialized'){
                        webRTCAdaptor.publish(liveStreamName, "", "", "", "", "");
                        this.streamStatus = 'started'
                    }
                }, // check info callbacks bellow
                callbackError: function(error, message) {
                    alert("error callback: " +  JSON.stringify(error));
                }, // check error callbacks bellow
            });
            const liveStreamName = `stream_${new Date().valueOf()}`
            this.liveStreamName = liveStreamName;
            this.webRTCAdaptor = webRTCAdaptor;
            this.notifyServerUsers()

            setTimeout(() => {
                if (this.streamStatus != "closed") {
                    this.endStream();
                }
            }, 1000 * 60 * 10);
        },
        notifyServerUsers(){
            this.file_url = `https://${this.serverUrl}/streams/${this.liveStreamName}.mp4`
            this.playback_url =`https://${this.serverUrl}/play.html?name=${this.liveStreamName}&autoplay=true&mute=false`

            axios.post(route(`start.live.strem`, { raffle: this.streamId,}), {
                // file_url: this.file_url,
                playback_url: this.playback_url
            })
        },
        endStream(){
            this.webRTCAdaptor.stop(this.liveStreamName)
            this.streamStatus = 'closed'
            this.showEndStreamModal = false;

            this.$emit('streamComplete', this.file_url)

            axios.post(route(`complete.live.strem`, { raffle: this.streamId,}), {
                playback_url: this.playback_url,
                file_url: this.file_url,
            }).then(res=> {
                this.liveStreamName = null;
                this.file_url = null;
                this.playback_url = null;
                this.webRTCAdaptor = null;
            })
        },
        pauseStream(){

        },
        resumeStream(){

        },
    }
}

</script>
