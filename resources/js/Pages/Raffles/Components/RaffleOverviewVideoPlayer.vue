<template>
    <div class="raffle_modal_video_paler_box">
        <div class="flex justify-center gap-[10px]">
            <!-- <video
                ref="videoTag"
                controls
                preload="none"
                :src="route('raffles.play-video', { video_name: videoName })"
            >

            </video> -->
            <video
                ref="videoTag"
                controls
                preload="none"
                :src="videoName"
            >

            </video>
            <span
                class="close cursor-pointer"
                @click="() => {
                    $emit('closeVideoPlayer', videoTag)
                    $refs.videoTag.load()
                    $refs.videoTag.currentTime = 0;
                }"
            >
                <Cross color="black" />
            </span>
        </div>
    </div>
</template>

<script setup>
import Cross from "./Cross";
import { ref } from "@vue/reactivity";
import { onMounted } from 'vue'

defineProps({
    videoName: {
        type: String,
        required: true,
    },
});

const videoTag = ref(null);

onMounted(() => {
    console.log('hellow orld')
})
</script>

<style lang="scss" scoped>
.raffle_modal_video_paler_box {
    max-width: 1400px;
    /*    background-color: #fff;
          box-shadow: 0 4px 4px rgb(0 0 0 / 10%);*/
    height: fit-content;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
    left: 0;
    right: 0;
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
