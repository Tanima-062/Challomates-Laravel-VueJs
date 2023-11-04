<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <video autoplay ref="stream_view"></video>
      </div>
    </div>
  </div>
</template>

<script setup>
import Peer from "simple-peer";
import axios from "axios";
import { getCurrentInstance } from "vue";
import { ref, onMounted } from "vue";
import Echo from "laravel-echo";

const props = defineProps({
  stream_id: {
    type: [Number, String],
    required: true,
  },
  user_id: {
    type: [Number, String],
    required: true,
  },
});

const stream_view = ref("");
const broadcasterPeer = ref(null);

const echo = new Echo({
  broadcaster: "pusher",
  authEndpoint: `/public/presence/auth/${props.user_id}`,
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  wsHost: process.env.MIX_PUSHER_HOST,
  wsPort: process.env.MIX_PUSHER_PORT,
  wssPort: process.env.MIX_PUSHER_PORT,
  forceTLS: false,
  encrypted: true,
  disableStats: true,
  enabledTransports: ["ws", "wss"],
});

const removeBroadcastVideo = () => {
  const tracks = stream_view.value.srcObject.getTracks();
  tracks.forEach((track) => {
    track.stop();
  });
  stream_view.value.srcObject = null;
};

const handlePeerEvents = (peer, incomingOffer, broadcaster, cleanupCallback) => {
  peer.on("signal", (data) => {
    axios
      .post("/stream-answer", {
        broadcaster,
        answer: data,
      })
      .then((res) => {
        console.log(res);
      })
      .catch((err) => {
        console.log(err);
      });
  });
  peer.on("stream", (stream) => {
    stream_view.value.srcObject = stream;
  });
  peer.on("track", (track, stream) => {
    console.log("onTrack");
  });
  peer.on("connect", () => {
    console.log("Viewer Peer connected");
  });
  peer.on("close", () => {
    console.log("Viewer Peer closed");
    peer.destroy();
    cleanupCallback();
  });
  peer.on("error", (err) => {
    console.log("handle error gracefully");
  });

  const updatedOffer = {
    ...incomingOffer,
    sdp: `${incomingOffer.sdp}\n`,
  };
  peer.signal(updatedOffer);
};

const createViewerPeer = (incomingOffer, broadcaster) => {
  const peer = new Peer({
    initiator: false,
    trickle: false,
    config: {
      iceServers: [
        {
          //   urls: "stun.l.google.com:19302",
        },
        //     {
        //       urls: this.turn_url,
        //       username: this.turn_username,
        //       credential: this.turn_credential,
        //     },
      ],
    },
  });
  // Add Transceivers
  peer.addTransceiver("video", { direction: "recvonly" });
  peer.addTransceiver("audio", { direction: "recvonly" });

  // Initialize Peer events for connection to remote peer
  peer, incomingOffer, broadcaster, removeBroadcastVideo;
  broadcasterPeer.value = peer;
};

const joinBroadcast = (identity, streamId) => {
  //join broadcast streaming channel this will signal the broadcaster that I have joined the channel
  echo.join(`streaming-channel.${streamId}`);

  //create private channel to listen to stream offers from boradcaster
  echo.private(`stream-signal-channel.${identity}`).listen("StreamOffer", ({ data }) => {
    console.log(data, "recived stream offer");
    createViewerPeer(data.offer, data.broadcaster);
  });
};
onMounted(() => {
  joinBroadcast(props.user_id, props.stream_id);
});
</script>

<style scoped></style>
