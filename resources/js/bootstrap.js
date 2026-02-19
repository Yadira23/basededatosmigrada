import _ from 'lodash';
window._ = _;

// Import all of Bootstrap's JS
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap;

// Import Axios
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'local',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',

    wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
    wsPort: Number(import.meta.env.VITE_PUSHER_PORT || 6001),
    wssPort: Number(import.meta.env.VITE_PUSHER_PORT || 6001),
    forceTLS: false,
    enabledTransports: ['ws'],
});

