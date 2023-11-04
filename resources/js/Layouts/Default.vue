<template>
    <CustomModal>
        <div class="wrapper flex">
            <sidebar v-click-away="closeSidebar" class="w-480" />
            <!-- Page Content  -->
            <div id="content">
                <navbar @open="showSidebar" class="h-60 bg-background flex justify-between py-2.5 px-6" />

                <main class="px-6 py-12">
                    <slot />
                </main>
            </div>
        </div>
        <StatusShower />
    </CustomModal>
</template>

<script>
import Navbar from "../Components/Navbar";
import Sidebar from "../Components/Sidebar/Sidebar.vue";
import CustomModal from "../Components/Modal/CustomModal.vue";
import StatusShower from "../Components/StatusShower.vue";

import { computed, onMounted, reactive } from "@vue/runtime-core";
import { loadLanguageAsync, trans } from "laravel-vue-i18n";
import { usePage } from "@inertiajs/inertia-vue3";

export default {
    components: { Sidebar, Navbar, CustomModal, StatusShower },
    setup(props) {
        onMounted(() => {
            const user = usePage().props.value.auth_user;
            const lang = usePage().props.value.auth_user.language.code || "de";
            loadLanguageAsync(lang);

            // Echo.channel(`testChannel`)
            //     // Echo.private(`testChannel`)
            //     .listen(".TestEvent", (e) => {
            //         console.log("test event received", e);
            //         //alert('ok');
            //     });
            // Echo.channel(`like-update.2` )
            // .listen('.StoryLike', (e)=> {
            //     console.log('story like update', e);
            // })
            // Echo.channel(`comment-update.2` )
            // .listen('.StoryComment', (e)=> {
            //     console.log('story comment update', e);
            // })

            IOEcho.channel(`story-update-channel`).listen(".StoryUpdate", (e) => {
                console.log("story like comment update", e);
            });

            IOEcho.channel(`story-update-channel`).listen(".BroadcastStart", (e) => {
                console.log("broadcast url receive", e);
            });

            IOEcho.channel(`broadcast-notification`).listen(
                ".BroadcastNotification",
                (e) => {
                    console.log("Url has been received", e);
                }
            );
            IOEcho.channel(`broadcast-notification-2`).listen(
                ".BroadcastNotification",
                (e) => {
                    console.log("Url has been received", e);
                }
            );

            IOEcho.channel(`url`).listen(
                ".UrlSend",
                (e) => {
                    console.log("Url received", e.url);
                }
            );
        });

        const option = reactive({
            openSidebar: false,
        });

        const style = computed(() =>
            option.openSidebar ? "display:block" : "display:none;"
        );

        const showSidebar = () => {
            option.openSidebar = true;
            //   console.log("clicked");
        };

        const closeSidebar = () => {
            option.openSidebar = false;
            //   console.log("close");
        };
        return {
            showSidebar,
            style,
            closeSidebar,
        };
    },
};
</script>

<style lang="scss" scoped>
#content {
    width: calc(100% - 480px);
}
</style>
