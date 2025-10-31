import type Echo from 'laravel-echo'
import type Pusher from 'pusher-js'

declare global {
  interface Window {
    axios: any
    Pusher: typeof Pusher
    Echo: Echo
  }
}

export {}
