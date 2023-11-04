

// import _ from 'lodash';
// window._ = _;

// /**
//  * We'll load the axios HTTP library which allows us to easily issue requests
//  * to our Laravel back-end. This library automatically handles sending the
//  * CSRF token as a header based on the value of the "XSRF" token cookie.
//  */

// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    // broadcaster: 'pusher',
    // key: process.env.MIX_PUSHER_APP_KEY,
    // cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    // wsHost: process.env.MIX_PUSHER_HOST,
    // wsPort: process.env.MIX_PUSHER_PORT,
    // wssPort: process.env.MIX_PUSHER_PORT,
    // forceTLS: false,
    // encrypted: true,
    // disableStats: true,
    // enabledTransports: ['ws', 'wss'],
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: process.env.MIX_PUSHER_HOST,
    wsPort: process.env.MIX_PUSHER_PORT,
    wssPort: process.env.MIX_PUSHER_PORT,
    forceTLS: false,
    encrypted: true,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});


// console.log(process.env.MIX_SOCKET_IO_HOST, 'socket io server')




window.io = require('socket.io-client')


// window.socket = io.connect('http://localhost:3000', {
//     transports: ['websocket', 'polling', 'flashsocket']
// });

// socket.on('connect', () => {
//     console.log('Successfully connected!');
// });

// socket.on("connect_error", () => {
//     console.log('Connection failed');
// });


window.IOEcho = new Echo({
    // namespace: "App\Events",
    broadcaster: 'socket.io',
    // host: window.location.hostname + ':3001'
    // host: window.location.hostname
    // host: "http://89.145.164.99:3001"
    host: process.env.MIX_SOCKET_IO_HOST || "127.0.0.1:6001",
    transports: ['websocket', 'polling', 'flashsocket'] // Fix CORS error!
});
