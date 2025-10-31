<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Calendar, Clock, User, MapPin, FileText, CheckCircle2, XCircle, Clock3 } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import * as appointmentsRoutes from '@/routes/appointments'

interface User {
  id: number
  name: string
  email: string
  avatar: string | null
}

interface Specialization {
  id: number
  name: string
}

interface ProviderProfile {
  id: number
  user: User
  specialization: Specialization
}

interface Appointment {
  id: number
  appointment_date: string
  start_time: string
  end_time: string
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed' | 'no_show'
  notes: string | null
  user: User
  provider_profile: ProviderProfile
}

interface Props {
  appointments: {
    data: Appointment[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  isProvider: boolean
}

const props = defineProps<Props>()

const getStatusColor = (status: string) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    confirmed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    no_show: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
  }
  return colors[status as keyof typeof colors] || colors.pending
}

const getStatusIcon = (status: string) => {
  if (status === 'confirmed') return CheckCircle2
  if (status === 'cancelled') return XCircle
  return Clock3
}

const cancelAppointment = (appointmentId: number) => {
  if (confirm('Are you sure you want to cancel this appointment?')) {
    router.post(appointmentsRoutes.cancel.url(appointmentId))
  }
}

const updateStatus = (appointmentId: number, status: string) => {
  router.post(appointmentsRoutes.updateStatus.url(appointmentId), { status })
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>

<template>
  <AppLayout>
    <Head title="My Appointments" />

    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ isProvider ? 'My Schedule' : 'My Appointments' }}
          </h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            {{ isProvider ? 'Manage your patient appointments' : 'View and manage your bookings' }}
          </p>
        </div>
        <Link
          v-if="!isProvider"
          :href="appointmentsRoutes.create.url()"
          class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold"
        >
          Book New Appointment
        </Link>
      </div>

      <!-- Appointments List -->
      <div v-if="appointments.data.length > 0" class="space-y-4">
        <div
          v-for="appointment in appointments.data"
          :key="appointment.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all p-6"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <!-- Header -->
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ isProvider
                      ? appointment.user.name.charAt(0).toUpperCase()
                      : appointment.provider_profile.user.name.charAt(0).toUpperCase()
                    }}
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                      {{ isProvider ? appointment.user.name : appointment.provider_profile.user.name }}
                    </h3>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusColor(appointment.status)"
                    >
                      <component :is="getStatusIcon(appointment.status)" class="w-3 h-3 mr-1" />
                      {{ appointment.status }}
                    </span>
                  </div>
                  <p v-if="!isProvider" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ appointment.provider_profile.specialization.name }}
                  </p>
                </div>
              </div>

              <!-- Details -->
              <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center space-x-3 text-gray-700 dark:text-gray-300">
                  <Calendar class="w-5 h-5 text-indigo-600" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Date</p>
                    <p class="text-sm font-medium">{{ formatDate(appointment.appointment_date) }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-3 text-gray-700 dark:text-gray-300">
                  <Clock class="w-5 h-5 text-indigo-600" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Time</p>
                    <p class="text-sm font-medium">{{ appointment.start_time }} - {{ appointment.end_time }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-3 text-gray-700 dark:text-gray-300">
                  <User class="w-5 h-5 text-indigo-600" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ isProvider ? 'Patient' : 'Provider' }}
                    </p>
                    <p class="text-sm font-medium">
                      {{ isProvider ? appointment.user.email : appointment.provider_profile.user.email }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="appointment.notes" class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-start space-x-2">
                  <FileText class="w-4 h-4 text-gray-500 mt-0.5" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ appointment.notes }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="ml-4 flex flex-col space-y-2">
              <Link
                :href="appointmentsRoutes.show.url(appointment.id)"
                class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 border border-indigo-600 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all"
              >
                View Details
              </Link>
              
              <!-- Patient Actions -->
              <button
                v-if="!isProvider && ['pending', 'confirmed'].includes(appointment.status)"
                @click="cancelAppointment(appointment.id)"
                class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 border border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
              >
                Cancel
              </button>

              <!-- Provider Actions -->
              <template v-if="isProvider && appointment.status === 'pending'">
                <button
                  @click="updateStatus(appointment.id, 'confirmed')"
                  class="px-4 py-2 text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400 border border-green-600 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all"
                >
                  Confirm
                </button>
                <button
                  @click="updateStatus(appointment.id, 'cancelled')"
                  class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 border border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                >
                  Decline
                </button>
              </template>
              <button
                v-if="isProvider && appointment.status === 'confirmed'"
                @click="updateStatus(appointment.id, 'completed')"
                class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 border border-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all"
              >
                Mark Complete
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="appointments.last_page > 1" class="flex justify-center space-x-2 mt-8">
          <Link
            v-for="page in appointments.last_page"
            :key="page"
            :href="appointmentsRoutes.index.url({ query: { page } })"
            class="px-4 py-2 rounded-lg transition-all"
            :class="
              page === appointments.current_page
                ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
            "
          >
            {{ page }}
          </Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
          <Calendar class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No appointments yet</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          {{ isProvider ? 'You have no scheduled appointments.' : 'Start by booking your first appointment.' }}
        </p>
        <Link
          v-if="!isProvider"
          :href="appointmentsRoutes.create.url()"
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold"
        >
          Book Appointment
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
