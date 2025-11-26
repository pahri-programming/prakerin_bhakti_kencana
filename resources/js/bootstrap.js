import _ from 'lodash';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window._ = _;
window.axios = axios;

// Default axios config
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set Pusher to window
window.Pusher = require('pusher-js');

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1',
    forceTLS: true
});
