<template>
    <div class="modal_video_paler_box">
        <div class="relative">
            <!-- <video
                ref="videoTag"
                class="relative"
                controls
                :src="route('raffles.play-video', { video_name: videoName })"
            ></video> -->
            <video
                ref="videoTag"
                class="relative"
                controls
                :src="videoName"
            ></video>
            <span
                class="close cursor-pointer absolute top-0 right-[-25px]"
                @click="hideModal"
            >
                <Cross color="#FFFFFF" />
            </span>
        </div>
    </div>
</template>

<script setup>
import Cross from "./Cross";
import { ref } from "@vue/reactivity";

const emit = defineEmits('hide')
defineProps({
    videoName: {
        type: String,
        required: true,
    },
});

const videoTag = ref(null);
const hideModal = () => {
    videoTag.value.currentTime = 0;
    videoTag.value.pause()
    emit('hide')
}
</script>

<style lang="scss" scoped>
.modal_video_paler_box {
    max-width: 1400px;
    /*    background-color: #fff;
        box-shadow: 0 4px 4px rgb(0 0 0 / 10%);*/
    height: fit-content;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;

    video {
        margin: 0 auto;
    }
}

.title {
    font-family: "Poppins", serif;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 27px;
    text-align: center;
    color: #1aa1e4;
    margin-bottom: 32px;
}

.message {
    font-family: "Poppins", serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    text-align: center;
    color: #135f84;
}

.buttons {
    width: 285px;
}
</style>
