import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY || '33pk7gdrm1ojvlls1x7v',
  wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
  wsPort: import.meta.env.VITE_REVERB_PORT || 8082,
  wssPort: import.meta.env.VITE_REVERB_PORT || 8082,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      Accept: 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    },
  },
  cluster: '',
  disableStats: true,
})

// Log connection status for debugging
console.log('Echo initialized with config:', {
  wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
  wsPort: import.meta.env.VITE_REVERB_PORT || 8082,
  key: import.meta.env.VITE_REVERB_APP_KEY || '33pk7gdrm1ojvlls1x7v',
})
