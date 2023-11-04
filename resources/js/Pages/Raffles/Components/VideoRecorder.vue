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

<script setup>
import { ref } from "@vue/reactivity";
import { getCurrentInstance, inject, watch } from "vue";
import VideoStreamingStartButton from "./VideoStreamingStartButton";
import Peer from "simple-peer";
import axios from "axios";
import Confirmation from "../../../Components/Modal/Content/Confirmation.vue";
import Cross from "../../../Components/Icons/Cross.vue";
import { nextTick } from "process";

// const csrf = document.querySelector("[name=csrf-token]").getAttribute("content");

const ctx = getCurrentInstance().ctx;
const getAllSupportedMimeTypes = (...mediaTypes) => {
    if (!mediaTypes.length) mediaTypes.push(...["video", "audio"]);
    const FILE_EXTENSIONS = ["webm", "ogg", "mp4"];
    const CODECS = [
        "vp9",
        "vp9.0",
        "vp8",
        "vp8.0",
        "avc1",
        "av1",
        "h265",
        "h.265",
        "h264",
        "h.264",
        "opus",
    ];

    return [
        ...new Set(
            FILE_EXTENSIONS.flatMap((ext) =>
                CODECS.flatMap((codec) =>
                    mediaTypes.flatMap((mediaType) => [
                        { variation: `${mediaType}/${ext};codecs:${codec}`, ext: ext },
                        { variation: `${mediaType}/${ext};codecs=${codec}`, ext: ext },
                        { variation: `${mediaType}/${ext};codecs:${codec.toUpperCase()}`, ext: ext },
                        { variation: `${mediaType}/${ext};codecs=${codec.toUpperCase()}`, ext: ext },
                        { variation: `${mediaType}/${ext}`, ext: ext },
                    ])
                )
            )
        ),
    ].filter((item) => MediaRecorder.isTypeSupported(item.variation));
};

const props = defineProps({
    streamId: {
        type: [Number, String],
        required: true,
    },
});

const emit = defineEmits(["streamComplete", "close"]);

const videoTag = ref(null);
const startButton = ref(null);
const streamStatus = ref("closed");
const showEndStreamModal = ref(false);

const streamingUsers = ref([]);
const allPeers = ref({});

let stream = null;
let echo = null;
//channels
const streamingPresenceChannel = ref(undefined);

const getPermissions = async () => {
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
            .getUserMedia({ video: true, audio: true })
            .then((stream) => {
                resolve(stream);
            })
            .catch((err) => {
                reject(err);
            });
    });
};

const peerCreator = (stream, user, signalCallback) => {
    let peer;
    return {
        create: () => {
            peer = new Peer({
                initiator: true,
                trickle: false,
                stream: stream,
                iceServers: [
                    {
                        urls: "stun:stun.l.google.com:19302",
                    },
                ],
                // config: {
                //   iceServers: [
                //     {
                //       urls: "stun:stun.stunprotocol.org",
                //     },
                //     {
                //       urls: this.turn_url,
                //       username: this.turn_username,
                //       credential: this.turn_credential,
                //     },
                //   ],
                // },
            });
        },
        getPeer: () => peer,
        initEvents: () => {
            peer.on("signal", (data) => {
                // send offer over here.
                signalCallback(data, user);
            });
            peer.on("stream", (stream) => {
                console.log("onStream");
            });
            peer.on("track", (track, stream) => {
                console.log("onTrack");
            });
            peer.on("connect", () => {
                console.log("Broadcaster Peer connected");
            });
            peer.on("close", () => {
                console.log("Broadcaster Peer closed");
            });
            peer.on("error", (err) => {
                console.log("handle error gracefully");
            });
        },
    };
};

const initializeStreamingChannel = () => {
    const streamingPresenceChannel = window.Echo.join(
        `streaming-channel.${props.streamId}`
    );

    //set existing users
    streamingPresenceChannel.here((users) => {
        console.log(users, "users");
        // streamingUsers.value = users;
    });

    //set new joined user
    streamingPresenceChannel.joining((user) => {
        // if this new user is not already on the call, send your stream offer
        const joiningUserIndex = streamingUsers.value.findIndex(
            (data) => data.id === user.id
        );

        if (joiningUserIndex < 0) {
            streamingUsers.value.push(user);

            // A new user just joined the channel so signal that user
            allPeers.value[user.id] = peerCreator(
                videoTag.value.srcObject,
                user,
                signalCallback
            );

            //   Create Peer
            allPeers.value[user.id].create();
            // Initialize Events
            allPeers.value[user.id].initEvents();
        }
    });
    streamingPresenceChannel.leaving((user) => {
        console.log(user.name, "Left");
        // destroy peer
        allPeers.value[user.id]?.getPeer().destroy();
        // delete peer object
        delete allPeers.value[user.id];
        // if one leaving is the broadcaster set streamingUsers to empty array
        if (user.id === ctx.auth_user.id) {
            streamingUsers.value = [];
        } else {
            // remove from streamingUsers array
            const leavingUserIndex = streamingUsers.value.findIndex(
                (data) => data.id === user.id
            );
            streamingUsers.value.splice(leavingUserIndex, 1);
        }
    });
};

const initializeSignalAnswerChannel = () => {
    window.Echo.private(`stream-signal-channel.${ctx.auth_user?.id}`).listen(
        "StreamAnswer",
        ({ data }) => {
            console.log("Signal Answer from private channel");
            if (data.answer.renegotiate) {
                console.log("renegotating");
            }
            if (data.answer.sdp) {
                const updatedSignal = {
                    ...data.answer,
                    sdp: `${data.answer.sdp}\n`,
                };
                console.log(data);
                allPeers.value[data.user].getPeer()?.signal(updatedSignal);
            }
        }
    );
};

const signalCallback = (offer, user) => {
    axios
        .post("/stream-offer", {
            broadcaster: ctx.auth_user.id,
            receiver: user,
            offer,
        })
        .then((res) => {
            console.log(res);
        })
        .catch((err) => {
            console.log(err);
        });
};

const supported_video_type = getAllSupportedMimeTypes("video")[0].variation;
const supported_video_ext = getAllSupportedMimeTypes("video")[0].ext;
console.log({ type: supported_video_type, ext: supported_video_ext });

const unique_id = `${Date.now()}-${props.streamId}`;
const uploadChunk = async (chunk) => {
    let res = null;
    if (chunk.size > 0) {
        const form_data = new FormData();
        form_data.append("chunk", chunk);
        res = await axios.post(
            route("streaming.store-video", {
                raffle: props.streamId,
                streamId: unique_id,
                video_type: supported_video_ext,
            }),
            form_data
        );
    }
    if (streamStatus.value == "closed" && streamStatus.value !== 'complete') {
        if (streamStatus.value == 'complete') {
            return
        }

        streamStatus.value = 'complete'
        axios
            .post(
                route("streaming.store-video", {
                    raffle: props.streamId,
                    streamId: unique_id,
                    video_type: supported_video_ext,
                    last_byte: 1,
                })
            )
            .then(function (res) {
                emit("streamComplete", res.data.file_name);
                //emit("streamComplete", 'raffle/raffle_draw_1664180384474-1.webm');
            });
    }
    return res;
};

let mediaRecorder = null;
const record = (stream) => {
    mediaRecorder = new MediaRecorder(stream, {
        mimeType: supported_video_type,
        bitsPerSecond: 5000000, //720p video
    });
    mediaRecorder.start();
    let chunks = [];
    mediaRecorder.ondataavailable = (e) => {
        console.log(
            { recordblob: e, supported_video_type, supported_video_ext },
            "chunkUpload"
        );
        chunks.push(e.data);
    };
    mediaRecorder.onstop = () => {
        uploadChunk(new Blob(chunks, { type: supported_video_type }));
        chunks = [];
    };
};

let stopRecord = null;
const startStream = async () => {
    try {
        stream = await getPermissions();
        streamStatus.value = "started";
        videoTag.value.srcObject = stream;

        record(stream);
        stopRecord = setInterval(() => {
            mediaRecorder.stop();
            mediaRecorder.start();
        }, 3000);

        initializeStreamingChannel();
        initializeSignalAnswerChannel(); // a private channel where the broadcaster listens to incoming signalling answer
        axios.get(route("streaming.notify-users", { streamId: props.streamId }));

        //record media and store it to server
    } catch (error) {
        console.log(error);
    }

    setTimeout(() => {
        if (streamStatus.value != "closed") {
            endStream();
        }
    }, 1000 * 60 * 10);
};

const endStream = async () => {
    stream?.getTracks().forEach((track) => {
        track.stop();
    });

    streamStatus.value = "closed";
    if (stopRecord) {
        clearInterval(stopRecord);
        mediaRecorder.stop();
    }

    showEndStreamModal.value = false;
    videoTag.value.srcObject = null;
};

const pauseStream = () => {
    clearInterval(stopRecord);
    mediaRecorder.stop();
    stopRecord = null;

    const resumeStream = videoTag.value.addEventListener("play", () => {
        record(videoTag.value.srcObject);
        stopRecord = setInterval(() => {
            mediaRecorder.stop();
            mediaRecorder.start();
        }, 3000);
        videoTag.value.removeEventListener("play", resumeStream);
    });
};
</script>
