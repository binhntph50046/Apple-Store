import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

try {
    console.log('Initializing Echo...');
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '7b19b300217b6a904b9e',
        cluster: 'ap1',
        wsHost: 'ws-ap1.pusher.com',
        wsPort: 443,
        wssPort: 443,
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
        disableStats: true
    });

    console.log('Echo initialized successfully');

    window.Echo.connector.pusher.connection.bind('connecting', () => {
        console.log('Connecting to Pusher...');
    });

    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Connected to Pusher!');
        console.log('Socket ID:', window.Echo.connector.pusher.connection.socket_id);
    });

    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('Pusher connection error:', error);
    });

} catch (error) {
    console.error('Error initializing Echo:', error);
}

// Test Echo availability
document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        console.log('Echo is available in DOM');
    } else {
        console.error('Echo is not available in DOM');
    }
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

// import './echo';
// import './echo-admin';
    import axios from 'axios';
    window.axios = axios;
    import toastr from 'toastr'; // Ensure toastr is installed via npm
    // Mặc định: cho Chatify
    window.Pusher = require('pusher-js');

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
    });

    // Khác biệt: tạo 1 Echo riêng cho Notifications
    window.NotifyEcho = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_NOTIFY_KEY,
        cluster: import.meta.env.VITE_PUSHER_NOTIFY_CLUSTER,
        forceTLS: true,
    });
    import './noti';
