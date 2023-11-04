<template>
    <div
        class="flex w-[1200px] items-center justify-center flex-col bg-white min-h-[300px] absolute top-[50%] translate-y-[-50%] z-10 border-[1px] border-secondary-2 shadow-[0_4px_4px_rgb(0 0 0 / 10%)]">
        <video
            ref="videoTag"
            class="relative w-full max-w-[1200px] max-h-[450px]"
            :class="[streamStatus == 'started' ? '' : 'hidden']"
            controls
            autoplay
            muted
            @pause="pauseStream"
            @play="resumeStream"
        ></video>
        asdfsdfsdfds

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
import Peer from "simple-peer";
import axios from "axios";
import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
import Cross from "../../../Components/Icons/Cross.vue"
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
            stream: null,
            mediaRecorder: null,
            options: {
                audioBitsPerSecond: 128000,
                videoBitsPerSecond: 2500000,
                framerate: 25,
                rtmp: `rtmp://127.0.0.1:1935/live/${this.streamId}`, // RTMP endpoint,
                // rtmp: "rtmp://dev.challomates.com:1935/live/stream", // RTMP endpoint,
                audioSampleRate: Math.round(this.audioBitsPerSecond / 4)
                // host: "localhost",
                // port: 1234
            }
        }
    },
    methods: {

            async getPermissions(){
                // Older browsers might not implement mediaDevices at all, so we set an empty object first
                if (navigator.mediaDevices === undefined) {
                    navigator.mediaDevices = {};
                }
                if (navigator.mediaDevices.getUserMedia === undefined) {
                    navigator.mediaDevices.getUserMedia = function (constraints) {
                        // First get ahold of the legacy getUserMedia, if present
                        const getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

                        // Some browsers just don't implement it - return a rejected promise with an error
                        // to keep a consistent interface
                        if (!getUserMedia) {
                            return Promise.reject(
                                new Error("getUserMedia is not implemented in this browser")
                            );
                        }

                        // Otherwise, wrap the call to the old navigator.getUserMedia with a Promise
                        return new Promise((resolve, reject) => {
                            getUserMedia.call(navigator, constraints, resolve, reject);
                        });
                    };
                }
                navigator.mediaDevices.getUserMedia =
                    navigator.mediaDevices.getUserMedia ||
                    navigator.webkitGetUserMedia ||
                    navigator.mozGetUserMedia;

                return new Promise((resolve, reject) => {
                    navigator.mediaDevices
                        // .getUserMedia({ video: true, audio: true })
                        .getUserMedia({ video: true, audio: true })
                        .then((stream) => {
                            resolve(stream);
                        })
                        .catch((err) => {
                            reject(err);
                        });
                });
            },
            async startStream(){
                const stream = await this.getPermissions();

                this.stream = stream;
                this.streamStatus = 'started'

                const video = this.$refs.videoTag
                video.srcObject = stream;
                video.play();
                this.lilveStreaming()
            },
            endStream(){
                console.log('stop calling');
                return new Promise((resolve, reject) => {
                    if (this.mediaRecorder) {
                        if (this.mediaRecorder.state === "inactive") {
                            console.log('media recorder inactive')
                            resolve();
                            return;
                        }
                        this.mediaRecorder.onstop = () => {
                            socket.emit('stop', () => {
                                this.mediaRecorder = undefined;
                                this.stream?.getTracks().forEach((track) => {
                                    track.stop();
                                });
                                this.streamStatus = "closed";

                                const video = this.$refs.videoTag
                                video.srcObject = null
                                this.showEndStreamModal = false;
                                this.stream = null

                                console.log('recorder stoped')
                                this.$emit("streamComplete", 'abcd.mp4');
                                console.log('after emit')

                                resolve();
                            });
                        };
                        this.mediaRecorder.stop();
                        this.mediaRecorder = null
                        console.log('media recorder stop')
                    }
                });
            },
            pauseStream(){
                if (this.mediaRecorder) {
                    this.mediaRecorder.pause();
                }
            },
            resumeStream(){
                if(this.mediaRecorder && this.mediaRecorder.state === "paused") {
                    this.mediaRecorder.resume();
                    console.log('video resumed');
                }
            },
            lilveStreaming(){
                console.log('streaming calling')
                return new Promise((resolve, reject) => {
                    if (this.mediaRecorder) {
                        if (this.mediaRecorder.state === "inactive") {
                            this.mediaRecorder.start();
                        } else if (this.mediaRecorder.state === "paused") {
                            this.mediaRecorder.resume();
                        }

                        resolve();
                        return;
                    }

                    this.mediaRecorder = new MediaRecorder(this.stream, {
                        audioBitsPerSecond: this.options.audioBitsPerSecond,
                        videoBitsPerSecond: this.options.videoBitsPerSecond
                    });

                    socket.emit('start', this.options, () => {
                        console.log('start')
                        resolve();
                        this.mediaRecorder.ondataavailable = (data) => this.onMediaRecorderDataAvailable(data);

                        try {
                            this.mediaRecorder.start(250);
                        } catch (e) {
                            this.socket.emit("stop");
                            throw e;
                        }
                    });
                });
            },
            onMediaRecorderDataAvailable(data){
                // console.log('data sending')
                console.log(this.options.rtmp)
                socket.emit('binarystream', data.data, (err) => {
                    if (err) {
                        console.log(err)
                        // this.onRemoteError(err);
                    }
                });
            }
    }
}

</script>
