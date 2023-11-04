import Peer from "simple-peer";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from 'pusher-js';


const stream_view = document.getElementById("video-frame");
// const player = videojs(stream_view, {
//     autoplay: 'any',
//     control: true,
// })
// let vid = null;


let broadcasterPeer = null;

const echo = new Echo({
    broadcaster: "pusher",
    authEndpoint: `/public/presence/auth/${user_id}`,
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: process.env.MIX_PUSHER_HOST,
    wsPort: process.env.MIX_PUSHER_PORT,
    wssPort: process.env.MIX_PUSHER_PORT,
    forceTLS: false,
    encrypted: true,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    auth: {
        headers: {
            'X-CSRF-Token': csrf
        }
    }
});

const removeBroadcastVideo = () => {
    const tracks = vid.srcObject.getTracks();
    tracks.forEach((track) => {
        track.stop();
    });
    vid.srcObject = null;
};

const handlePeerEvents = (peer, incomingOffer, broadcaster, cleanupCallback) => {
    peer.on("signal", (data) => {
        console.log(user_id)
        axios
            .post("/stream-answer", {
                broadcaster,
                answer: data,
                user: user_id
            })
            .then((res) => {
                console.log(res);
            })
            .catch((err) => {
                console.log(err);
            });
    });
    peer.on("stream", (stream) => {
        stream_view.srcObject = stream;
        // vid = player.tech().el();
        // vid.srcObject = stream;
        // setTimeout(() => {
        //     stream_view.play()
        // }, 1000)
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
            iceServers: [{
                urls: "stun:stun.l.google.com:19302",
            },
            // {
            //     urls: 'turn:openrelay.metered.ca:80',
            //     username: 'openrelayproject',
            //     credential: 'openrelayproject'
            // },
            ],
        },
    });
    // Add Transceivers
    peer.addTransceiver("video", {
        direction: "recvonly"
    });
    peer.addTransceiver("audio", {
        direction: "recvonly"
    });

    // Initialize Peer events for connection to remote peer
    handlePeerEvents(peer, incomingOffer, broadcaster, removeBroadcastVideo);
    broadcasterPeer = peer;
};

const joinBroadcast = (identity, streamId) => {
    //join broadcast streaming channel this will signal the broadcaster that I have joined the channel
    echo.join(`streaming-channel.${streamId}`);

    //create private channel to listen to stream offers from boradcaster
    echo.private(`stream-signal-channel.${identity}`).listen("StreamOffer", ({
        data
    }) => {
        createViewerPeer(data.offer, data.broadcaster);
    });
};
joinBroadcast(user_id, stream_id);

