<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Award, Clock, Calendar, Mail, Star, ArrowLeft, CheckCircle } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import * as appointmentsRoutes from '@/routes/appointments'

interface Specialization {
  id: number
  name: string
  description: string | null
}

interface User {
  id: number
  name: string
  email: string
  avatar: string | null
}

interface Schedule {
  id: number
  day_of_week: number
  start_time: string
  end_time: string
  is_available: boolean
  day_name: string
}

interface Provider {
  id: number
  user_id: number
  specialization_id: number
  bio: string | null
  years_experience: number
  slot_duration: number
  is_available: boolean
  user: User
  specialization: Specialization
  schedules: Schedule[]
}

interface Props {
  provider: Provider
}

const props = defineProps<Props>()

const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
const sortedSchedules = props.provider.schedules
  .filter(s => s.is_available)
  .sort((a, b) => dayOrder.indexOf(a.day_name) - dayOrder.indexOf(b.day_name))
</script>

<template>
  <AppLayout>
    <Head :title="`Provider: ${provider.user.name}`" />

    <div class="container mx-auto px-4 py-8 max-w-6xl">
      <!-- Back Button -->
      <Link
        :href="appointmentsRoutes.create.url()"
        class="inline-flex items-center space-x-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 mb-6"
      >
        <ArrowLeft class="w-5 h-5" />
        <span>Back to Booking</span>
      </Link>

      <!-- Provider Header -->
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-2xl p-8 md:p-12 text-white mb-8">
        <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div class="w-32 h-32 md:w-40 md:h-40 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white text-5xl md:text-6xl font-bold border-4 border-white/30 shadow-xl">
              {{ provider.user.name.charAt(0).toUpperCase() }}
            </div>
          </div>

          <!-- Info -->
          <div class="flex-1 text-center md:text-left">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ provider.user.name }}</h1>
            <p class="text-xl opacity-90 mb-4">{{ provider.specialization.name }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-4 mb-6">
              <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                <Award class="w-5 h-5" />
                <span class="font-medium">{{ provider.years_experience }} years</span>
              </div>
              <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                <Clock class="w-5 h-5" />
                <span class="font-medium">{{ provider.slot_duration }} min sessions</span>
              </div>
              <div v-if="provider.is_available" class="flex items-center space-x-2 bg-green-500/30 backdrop-blur-sm px-4 py-2 rounded-full">
                <CheckCircle class="w-5 h-5" />
                <span class="font-medium">Available</span>
              </div>
            </div>

            <!-- Contact -->
            <div class="flex items-center justify-center md:justify-start space-x-2 text-white/90">
              <Mail class="w-5 h-5" />
              <a :href="`mailto:${provider.user.email}`" class="hover:text-white transition-colors">
                {{ provider.user.email }}
              </a>
            </div>
          </div>

          <!-- Book Button -->
          <div class="flex-shrink-0">
            <Link
              :href="appointmentsRoutes.create.url() + `?provider=${provider.id}`"
              class="px-8 py-4 bg-white text-indigo-600 rounded-xl hover:shadow-2xl transition-all font-bold text-lg"
            >
              Book Appointment
            </Link>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
          <!-- About Section -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
              <User class="w-6 h-6 mr-2 text-indigo-600" />
              About
            </h2>
            <p v-if="provider.bio" class="text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
              {{ provider.bio }}
            </p>
            <p v-else class="text-gray-500 dark:text-gray-500 italic">
              No biography available yet.
            </p>
          </div>

          <!-- Specialization Details -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
              <Star class="w-6 h-6 mr-2 text-indigo-600" />
              Specialization
            </h2>
            <div class="space-y-4">
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                  {{ provider.specialization.name }}
                </h3>
                <p v-if="provider.specialization.description" class="text-gray-600 dark:text-gray-400">
                  {{ provider.specialization.description }}
                </p>
              </div>
              <div class="flex items-center space-x-2 text-sm">
                <Award class="w-5 h-5 text-indigo-600" />
                <span class="text-gray-600 dark:text-gray-400">
                  {{ provider.years_experience }} years of professional experience in {{ provider.specialization.name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
          <!-- Availability Schedule -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
              <Calendar class="w-6 h-6 mr-2 text-indigo-600" />
              Availability
            </h2>
            <div v-if="sortedSchedules.length > 0" class="space-y-3">
              <div
                v-for="schedule in sortedSchedules"
                :key="schedule.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
              >
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ schedule.day_name }}
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                  {{ schedule.start_time }} - {{ schedule.end_time }}
                </span>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <Calendar class="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <p class="text-gray-500 dark:text-gray-500">
                Schedule not set yet
              </p>
            </div>
          </div>

          <!-- Session Info -->
          <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl shadow-lg p-6 border-2 border-indigo-200 dark:border-indigo-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Session Information</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Duration:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ provider.slot_duration }} minutes</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Experience:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ provider.years_experience }} years</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                <span
                  class="px-3 py-1 rounded-full text-sm font-medium"
                  :class="provider.is_available 
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                >
                  {{ provider.is_available ? 'Available' : 'Unavailable' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <Link
                :href="appointmentsRoutes.create.url() + `?provider=${provider.id}`"
                class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold text-center block"
              >
                Book Now
              </Link>
              <a
                :href="`mailto:${provider.user.email}`"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-medium text-center block"
              >
                Contact Provider
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
