<template>
  <a class="dropdown-item inline-flex gap-2" href="#">
    <svg
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <circle cx="12" cy="12" r="12" fill="#FFA013" />
      <path
        d="M7.25 18C7.25 18.4142 7.58579 18.75 8 18.75C8.41421 18.75 8.75 18.4142 8.75 18H7.25ZM8 6L8.38587 5.35688C8.15417 5.21786 7.86561 5.21422 7.63048 5.34735C7.39534 5.48048 7.25 5.7298 7.25 6H8ZM18 12L18.3859 12.6431C18.6118 12.5076 18.75 12.2634 18.75 12C18.75 11.7366 18.6118 11.4924 18.3859 11.3569L18 12ZM10.9475 15.3569C10.5923 15.57 10.4771 16.0307 10.6902 16.3859C10.9033 16.7411 11.364 16.8562 11.7192 16.6431L10.9475 15.3569ZM8.75 18V6H7.25V18H8.75ZM7.61413 6.64312L17.6141 12.6431L18.3859 11.3569L8.38587 5.35688L7.61413 6.64312ZM17.6141 11.3569L10.9475 15.3569L11.7192 16.6431L18.3859 12.6431L17.6141 11.3569Z"
        fill="white"
      />
    </svg>
  </a>

  <!--    <LiveEndConfirmation
        message="Verlosung beenden?"
        text="Das Beenden der Verlosung kann nicht  rückgängig gemacht werden. Sind Sie sicher, dass Sie die Verlosung wirklich beenden wollen?"
        @confirmCLick="liveVideoEndConfirmation"
    />-->
</template>

<script setup>
// import {inject} from "vue";
// import VideoRecorder from "./VideoRecorder"
// import LiveEndConfirmation from "./LiveEndConfirmation";
// import {ref} from "@vue/reactivity";
// import { onMounted } from "@vue/runtime-core";
//import Echo from 'laravel-echo';

// export default {
//   components: { LiveEndConfirmation },
//   /*props: {
//         showConfirmation: {
//             type: Boolean,
//             default: false,
//         }
//     },*/

//   setup(props) {
//     // const modal = inject("modal");
//     // let mediaRecorder;
//     // const showConfirmation = ref(false);
//     // let options = { mimeType: "video/webm" };
//     // let recordedChunks = [];
//     // let mediaStream;

//     onMounted(() => {
//       /*WebRtcEcho.channel( 'videoShow' ).listen( 'NewTrade', function(response) {
//                 console.log(response)
//             } )*/
//     });

//     // function sendVideoToServer() {
//     //   console.log(recordedChunks);
//     // }

//     // function showConfirmationPopup() {
//     //   showConfirmation.value = true;
//     // }

//     // const toggleVideoStreamingPopup = () => {
//     //   modal.show(VideoRecorder, {
//     //     props: {
//     //       videoName: props.videoName,
//     //     },

//     //     config: {
//     //       enableLightbox: true,
//     //       staticBackdrop: true,
//     //     },

//     //     events: {
//     //       videoStart: (media) => {
//     //         //console.log(Echo);
//     //         //console.log('start')
//     //         /*media.classList.remove('hidden')

//     //                         if ( hasGetUserMedia() ) {
//     //                             if ( hasPermission() ) {

//     //                                 navigator.mediaDevices.getUserMedia({ audio: true, video: true })
//     //                                     .then( function (stream) {
//     //                                         mediaStream = stream;
//     //                                         mediaRecorder = new MediaRecorder(mediaStream, options);

//     //                                         mediaRecorder.addEventListener('dataavailable', function(e) {
//     //                                             //console.log('test')
//     //                                             if (e.data.size > 0) {
//     //                                                 recordedChunks.push(e.data);
//     //                                             }
//     //                                         });

//     //                                         mediaRecorder.addEventListener('stop', sendVideoToServer);

//     //                                         media.srcObject=stream;
//     //                                         media.play();
//     //                                         mediaRecorder.start();

//     //                                         //console.log(recordedChunks)
//     //                                     } );
//     //                             } else {
//     //                                 console.log( 'not okay' )
//     //                             }
//     //                         } else {
//     //                             console.log( 'not support' )
//     //                         }*/
//     //       },

//     //       videoPause: (media) => {
//     //         //console.log('pause')
//     //       },

//     //       videoResume: (media) => {
//     //         //console.log('resume')
//     //       },

//     //       videoStop: (media) => {
//     //         showConfirmationPopup();
//     //         //this.showConfirmation = true;
//     //         /*const text = 'Das Beenden der Verlosung kann nicht  rückgängig gemacht werden. Sind Sie sicher, dass Sie die Verlosung wirklich beenden wollen?';
//     //                         modal2.show(Confirmation, {
//     //                             props: {
//     //                                 message: 'Verlosung beenden?',
//     //                                 text
//     //                             },
//     //                             config: {
//     //                                 staticBackdrop: true
//     //                             },
//     //                             events: {
//     //                                 yesClick: () => {
//     //                                     /!*modal.hide();
//     //                                     Inertia.post(
//     //                                         route(props.routeName, props.value),
//     //                                         {
//     //                                             _method: "put",
//     //                                             status: props.status === "active" ? "inactive" : "active",
//     //                                         },
//     //                                         { preserveScroll: true }
//     //                                     );*!/
//     //                                 },
//     //                                 noClick: () => modal.hide(),
//     //                             },
//     //                         });*/

//     //         /*const stream = media.srcObject;
//     //                         const tracks = stream.getTracks();
//     //                         media.srcObject = null;
//     //                         media.pause();
//     //                         media.currentTime = 0;
//     //                         tracks.forEach((track) => {
//     //                             track.stop();
//     //                         });
//     //                         modal.hide();*/
//     //       },
//     //     },
//     //   });
//     // };

//     return {
//       //   toggleVideoStreamingPopup,
//       //   showConfirmation,
//     };
//   },

//   //   methods: {
//   //     liveVideoEndConfirmation(payload) {
//   //       console.log(payload);
//   //     },
//   //   },
// };
</script>

<style lang="scss" scoped></style>
