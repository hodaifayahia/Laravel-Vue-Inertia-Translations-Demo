<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Calendar, Clock, Check, X } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import * as providerScheduleRoutes from '@/routes/provider/schedule'
import * as providerProfileRoutes from '@/routes/provider/profile'

interface Schedule {
  id: number
  day_of_week: number
  start_time: string
  end_time: string
  is_available: boolean
}

interface Profile {
  id: number
  slot_duration: number
}

interface Props {
  schedules: Schedule[]
  profile: Profile
}

const props = defineProps<Props>()

const days = [
  { id: 0, name: 'Sunday' },
  { id: 1, name: 'Monday' },
  { id: 2, name: 'Tuesday' },
  { id: 3, name: 'Wednesday' },
  { id: 4, name: 'Thursday' },
  { id: 5, name: 'Friday' },
  { id: 6, name: 'Saturday' },
]

// Initialize schedules for all days
const scheduleData = ref(
  days.map((day) => {
    const existing = props.schedules.find((s) => s.day_of_week === day.id)
    return {
      day_of_week: day.id,
      start_time: existing?.start_time || '09:00',
      end_time: existing?.end_time || '17:00',
      is_available: existing?.is_available ?? false,
    }
  })
)

const form = useForm({
  schedules: scheduleData.value,
})

const toggleDay = (dayId: number) => {
  const schedule = scheduleData.value.find((s) => s.day_of_week === dayId)
  if (schedule) {
    schedule.is_available = !schedule.is_available
  }
}

const setAllDays = (available: boolean) => {
  scheduleData.value.forEach((schedule) => {
    schedule.is_available = available
  })
}

const applyToAll = () => {
  const firstAvailable = scheduleData.value.find((s) => s.is_available)
  if (firstAvailable) {
    scheduleData.value.forEach((schedule) => {
      if (schedule.is_available) {
        schedule.start_time = firstAvailable.start_time
        schedule.end_time = firstAvailable.end_time
      }
    })
  }
}

const submit = () => {
  form.schedules = scheduleData.value
  form.post(providerScheduleRoutes.bulk.url())
}

const availableDaysCount = computed(() => {
  return scheduleData.value.filter((s) => s.is_available).length
})
</script>

<template>
  <AppLayout>
    <Head title="Provider Schedule" />

    <div class="container mx-auto px-4 py-8 max-w-6xl">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Configure Your Schedule</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Set your availability for each day of the week
        </p>
      </div>

      <!-- Info Card -->
      <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
        <div class="flex items-start space-x-3">
          <Calendar class="w-5 h-5 text-indigo-600 mt-0.5" />
          <div>
            <p class="text-sm text-gray-900 dark:text-white font-medium">
              Appointment Duration: {{ profile.slot_duration }} minutes
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
              Your schedule will be divided into {{ profile.slot_duration }}-minute time slots for appointments.
            </p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mb-6 flex flex-wrap gap-3">
        <button
          @click="setAllDays(true)"
          type="button"
          class="px-4 py-2 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/30 transition-all text-sm font-medium"
        >
          Enable All Days
        </button>
        <button
          @click="setAllDays(false)"
          type="button"
          class="px-4 py-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 transition-all text-sm font-medium"
        >
          Disable All Days
        </button>
        <button
          @click="applyToAll"
          type="button"
          class="px-4 py-2 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-all text-sm font-medium"
        >
          Apply First Day to All
        </button>
        <div class="ml-auto flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
          <Calendar class="w-4 h-4" />
          <span>{{ availableDaysCount }} days available</span>
        </div>
      </div>

      <!-- Schedule Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <div
          v-for="(schedule, index) in scheduleData"
          :key="schedule.day_of_week"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all"
          :class="schedule.is_available ? 'ring-2 ring-indigo-500' : ''"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-4">
                <button
                  @click="toggleDay(schedule.day_of_week)"
                  type="button"
                  class="w-10 h-10 rounded-lg flex items-center justify-center transition-all"
                  :class="
                    schedule.is_available
                      ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white'
                      : 'bg-gray-200 dark:bg-gray-700 text-gray-400'
                  "
                >
                  <Check v-if="schedule.is_available" class="w-5 h-5" />
                  <X v-else class="w-5 h-5" />
                </button>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ days[schedule.day_of_week].name }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ schedule.is_available ? 'Available' : 'Not available' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Time Inputs -->
            <div
              v-if="schedule.is_available"
              class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-14"
            >
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <Clock class="w-4 h-4 inline mr-1" />
                  Start Time
                </label>
                <input
                  v-model="schedule.start_time"
                  type="time"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <Clock class="w-4 h-4 inline mr-1" />
                  End Time
                </label>
                <input
                  v-model="schedule.end_time"
                  type="time"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4 pt-6">
          <a
            :href="providerProfileRoutes.show.url()"
            class="px-8 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-semibold"
          >
            Back to Profile
          </a>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold disabled:opacity-50"
          >
            {{ form.processing ? 'Saving...' : 'Save Schedule' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
