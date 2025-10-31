<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Wifi, WifiOff } from 'lucide-vue-next'

const isConnected = ref(false)
const connectionStatus = ref<'connecting' | 'connected' | 'disconnected'>('connecting')

const statusColor = computed(() => {
  switch (connectionStatus.value) {
    case 'connected':
      return 'bg-green-500'
    case 'connecting':
      return 'bg-yellow-500'
    case 'disconnected':
      return 'bg-red-500'
    default:
      return 'bg-gray-500'
  }
})

const statusText = computed(() => {
  switch (connectionStatus.value) {
    case 'connected':
      return 'Real-time connected'
    case 'connecting':
      return 'Connecting...'
    case 'disconnected':
      return 'Using polling (refresh every 3s)'
    default:
      return 'Unknown'
  }
})

onMounted(() => {
  // Check WebSocket connection status
  if (window.Echo) {
    try {
      const connector = (window.Echo as any).connector
      if (connector && connector.pusher) {
        connector.pusher.connection.bind('connected', () => {
          connectionStatus.value = 'connected'
          isConnected.value = true
          console.log('✅ WebSocket connected')
        })
        
        connector.pusher.connection.bind('disconnected', () => {
          connectionStatus.value = 'disconnected'
          isConnected.value = false
          console.log('⚠️  WebSocket disconnected, using polling fallback')
        })
        
        connector.pusher.connection.bind('connecting', () => {
          connectionStatus.value = 'connecting'
          console.log('🔄 WebSocket connecting...')
        })
        
        // Check initial state
        const state = connector.pusher.connection.state
        if (state === 'connected') {
          connectionStatus.value = 'connected'
          isConnected.value = true
        } else if (state === 'disconnected' || state === 'unavailable') {
          connectionStatus.value = 'disconnected'
          isConnected.value = false
        }
      } else {
        connectionStatus.value = 'disconnected'
      }
    } catch (error) {
      console.error('Error checking WebSocket status:', error)
      connectionStatus.value = 'disconnected'
    }
  } else {
    connectionStatus.value = 'disconnected'
    console.warn('Echo not initialized')
  }
})
</script>

<template>
  <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-2">
      <Wifi v-if="isConnected" class="w-4 h-4 text-green-600 dark:text-green-400" />
      <WifiOff v-else class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
      
      <div class="flex items-center gap-2">
        <div :class="['w-2 h-2 rounded-full', statusColor]" class="animate-pulse" />
        <span class="text-xs text-gray-600 dark:text-gray-400">
          {{ statusText }}
        </span>
      </div>
    </div>
  </div>
</template>
