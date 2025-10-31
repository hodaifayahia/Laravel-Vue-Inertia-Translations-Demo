<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Stethoscope, User, Award, Clock } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import * as providerProfileRoutes from '@/routes/provider/profile'
import * as providerScheduleRoutes from '@/routes/provider/schedule'

interface Specialization {
  id: number
  name: string
  description: string | null
}

interface Profile {
  id: number
  user_id: number
  specialization_id: number
  bio: string | null
  years_experience: number
  slot_duration: number
  is_available: boolean
  specialization: Specialization
}

interface Props {
  profile: Profile | null
  specializations: Specialization[]
}

const props = defineProps<Props>()

const form = useForm({
  specialization_id: props.profile?.specialization_id || '',
  bio: props.profile?.bio || '',
  years_experience: props.profile?.years_experience || 0,
  slot_duration: props.profile?.slot_duration || 30,
  is_available: props.profile?.is_available ?? true,
})

const submit = () => {
  form.post(providerProfileRoutes.store.url())
}
</script>

<template>
  <AppLayout>
    <Head title="Provider Profile" />

    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Provider Profile</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Configure your profile to start accepting appointments
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Specialization -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <Stethoscope class="w-5 h-5 text-white" />
            </div>
            <div>
              <label class="block text-lg font-semibold text-gray-900 dark:text-white">
                Specialization
              </label>
              <p class="text-sm text-gray-600 dark:text-gray-400">Select your area of expertise</p>
            </div>
          </div>
          <select
            v-model="form.specialization_id"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="">Select a specialization</option>
            <option v-for="spec in specializations" :key="spec.id" :value="spec.id">
              {{ spec.name }}
            </option>
          </select>
          <p v-if="form.errors.specialization_id" class="mt-2 text-sm text-red-600">
            {{ form.errors.specialization_id }}
          </p>
        </div>

        <!-- Bio -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <User class="w-5 h-5 text-white" />
            </div>
            <div>
              <label class="block text-lg font-semibold text-gray-900 dark:text-white">
                Bio
              </label>
              <p class="text-sm text-gray-600 dark:text-gray-400">Tell patients about yourself</p>
            </div>
          </div>
          <textarea
            v-model="form.bio"
            rows="4"
            placeholder="Describe your experience, approach, and what patients can expect..."
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
          />
          <p v-if="form.errors.bio" class="mt-2 text-sm text-red-600">
            {{ form.errors.bio }}
          </p>
        </div>

        <!-- Years of Experience -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <Award class="w-5 h-5 text-white" />
            </div>
            <div>
              <label class="block text-lg font-semibold text-gray-900 dark:text-white">
                Years of Experience
              </label>
              <p class="text-sm text-gray-600 dark:text-gray-400">How long have you been practicing?</p>
            </div>
          </div>
          <input
            v-model.number="form.years_experience"
            type="number"
            min="0"
            max="100"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
          />
          <p v-if="form.errors.years_experience" class="mt-2 text-sm text-red-600">
            {{ form.errors.years_experience }}
          </p>
        </div>

        <!-- Slot Duration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <Clock class="w-5 h-5 text-white" />
            </div>
            <div>
              <label class="block text-lg font-semibold text-gray-900 dark:text-white">
                Appointment Duration
              </label>
              <p class="text-sm text-gray-600 dark:text-gray-400">How long is each appointment?</p>
            </div>
          </div>
          <select
            v-model.number="form.slot_duration"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
          >
            <option :value="15">15 minutes</option>
            <option :value="30">30 minutes</option>
            <option :value="45">45 minutes</option>
            <option :value="60">60 minutes</option>
          </select>
          <p v-if="form.errors.slot_duration" class="mt-2 text-sm text-red-600">
            {{ form.errors.slot_duration }}
          </p>
        </div>

        <!-- Availability -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
          <label class="flex items-center space-x-3 cursor-pointer">
            <input
              v-model="form.is_available"
              type="checkbox"
              class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <div>
              <span class="text-lg font-semibold text-gray-900 dark:text-white">
                Currently accepting appointments
              </span>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Patients can book appointments with you
              </p>
            </div>
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold disabled:opacity-50"
          >
            {{ form.processing ? 'Saving...' : 'Save Profile' }}
          </button>
        </div>
      </form>

      <!-- Schedule Link -->
      <div v-if="profile" class="mt-8 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
          Next Step: Configure Your Schedule
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Set your availability to let patients know when they can book appointments with you.
        </p>
        <a
          :href="providerScheduleRoutes.index.url()"
          class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold"
        >
          Configure Schedule →
        </a>
      </div>
    </div>
  </AppLayout>
</template>
