# 🧪 Booking System - Comprehensive Test Plan

## 📋 Test Users & Credentials

All users have the password: **`password`**

### 1. 👑 Super Admin
- **Email:** `superadmin@test.com`
- **Role:** super-admin
- **Permissions:** All permissions (full system access)
- **Expected Access:**
  - ✅ Can manage specializations (create, edit, delete)
  - ✅ Can configure provider profile
  - ✅ Can configure provider schedule
  - ✅ Can book appointments
  - ✅ Can manage all bookings
  - ✅ Can view all appointments
  - ✅ Full sidebar access

### 2. ⚙️ Admin
- **Email:** `admin@test.com`
- **Role:** admin
- **Permissions:** manage bookings, view bookings, can-book
- **Expected Access:**
  - ✅ Can manage all bookings
  - ✅ Can view all appointments
  - ✅ Can book appointments (as patient)
  - ❌ Cannot configure provider profile (not a provider)
  - ❌ Cannot manage specializations

### 3. 👨‍⚕️ Providers (3 doctors)

#### Dr. John Smith - Cardiologist
- **Email:** `dr.smith@test.com`
- **Role:** manager
- **Permissions:** book-sys, can-book, view bookings
- **Specialization:** Cardiology
- **Experience:** 15 years
- **Session Duration:** 45 minutes
- **Schedule:** Monday-Friday (9am-5pm, Thursday 10am-6pm, Friday 9am-3pm)
- **Expected Access:**
  - ✅ Can configure own provider profile
  - ✅ Can manage own schedule
  - ✅ Can book appointments (as patient)
  - ✅ Can view own bookings
  - ❌ Cannot manage other providers

#### Dr. Sarah Johnson - Dermatologist
- **Email:** `dr.johnson@test.com`
- **Role:** manager
- **Permissions:** book-sys, can-book, view bookings
- **Specialization:** Dermatology
- **Experience:** 10 years
- **Session Duration:** 30 minutes
- **Schedule:** Tuesday-Saturday (10am-6pm, Saturday 9am-2pm)
- **Expected Access:** Same as Dr. Smith

#### Dr. Emily Williams - Pediatrician
- **Email:** `dr.williams@test.com`
- **Role:** manager
- **Permissions:** book-sys, can-book, view bookings
- **Specialization:** Pediatrics
- **Experience:** 8 years
- **Session Duration:** 30 minutes
- **Schedule:** Monday, Wednesday, Friday (8am-4pm)
- **Expected Access:** Same as Dr. Smith

### 4. 🧑 Patients (2 users)

#### Alice Patient
- **Email:** `patient1@test.com`
- **Role:** user
- **Permissions:** can-book
- **Test Appointments:**
  - Pending: Dr. Smith (3 days from now, 10:00 AM)
  - Confirmed: Dr. Johnson (5 days from now, 2:00 PM)
  - Pending: Dr. Williams (10 days from now, 3:00 PM)
- **Expected Access:**
  - ✅ Can browse specializations
  - ✅ Can view provider details
  - ✅ Can book appointments
  - ✅ Can view own appointments
  - ❌ Cannot configure provider profile
  - ❌ Cannot manage schedules
  - ❌ Cannot view other users' appointments

#### Bob Patient
- **Email:** `patient2@test.com`
- **Role:** user
- **Permissions:** can-book
- **Test Appointments:**
  - Completed: Dr. Smith (7 days ago, 11:00 AM)
  - Cancelled: Dr. Williams (2 days ago, 9:00 AM)
- **Expected Access:** Same as Alice Patient

### 5. 👁️ Viewer
- **Email:** `viewer@test.com`
- **Role:** viewer
- **Permissions:** view users, view dashboard
- **Expected Access:**
  - ❌ No booking system access
  - ❌ Should not see booking menu in sidebar
  - ❌ Direct URL access should be denied

---

## 🧪 Test Cases

### Test Suite 1: Authentication & Authorization

#### TC-1.1: Login with Different Roles
- [ ] Login as Super Admin → Should succeed
- [ ] Login as Admin → Should succeed
- [ ] Login as Provider (Dr. Smith) → Should succeed
- [ ] Login as Patient (Alice) → Should succeed
- [ ] Login as Viewer → Should succeed
- [ ] Try invalid credentials → Should fail with error message

#### TC-1.2: Sidebar Menu Visibility
- [ ] Super Admin: Should see "Book Appointment", "My Appointments", "My Profile", "My Schedule", "Manage Specializations"
- [ ] Admin: Should see "Book Appointment", "My Appointments"
- [ ] Provider: Should see "Book Appointment", "My Appointments", "My Profile", "My Schedule"
- [ ] Patient: Should see "Book Appointment", "My Appointments"
- [ ] Viewer: Should NOT see booking menu items

#### TC-1.3: Direct URL Access Protection
Test unauthorized access by entering URLs directly:
- [ ] Viewer tries `/bookings/specializations` → Should get 403/redirect
- [ ] Patient tries `/bookings/provider/profile` → Should get 403/redirect
- [ ] Patient tries `/bookings/provider/schedule` → Should get 403/redirect
- [ ] Patient tries `/bookings/specializations` → Should get 403/redirect

---

### Test Suite 2: Specializations Management

#### TC-2.1: View Specializations (Admin Only)
Login as Super Admin:
- [ ] Navigate to "Manage Specializations"
- [ ] Should see 5 specializations with icons:
  - Cardiology ❤️
  - Dermatology 🩺
  - Pediatrics 👶
  - Orthopedics 🦴
  - Neurology 🧠
- [ ] Each card should show: name, description, active status
- [ ] Should see "Add Specialization" button

#### TC-2.2: Create New Specialization
Login as Super Admin:
- [ ] Click "Add Specialization"
- [ ] Fill in:
  - Name: "Ophthalmology"
  - Description: "Eye care specialists"
  - Icon: "👁️"
- [ ] Submit → Should create successfully
- [ ] New specialization should appear in list

#### TC-2.3: Edit Specialization
Login as Super Admin:
- [ ] Click edit on "Cardiology"
- [ ] Change description
- [ ] Submit → Should update successfully
- [ ] Changes should be visible

#### TC-2.4: Delete Specialization (with no providers)
Login as Super Admin:
- [ ] Create a test specialization (e.g., "Test Spec")
- [ ] Delete it → Should succeed
- [ ] Specialization should be removed from list

#### TC-2.5: Access Denied for Non-Admins
- [ ] Login as Provider → Try to access specializations URL → Should be denied
- [ ] Login as Patient → Try to access specializations URL → Should be denied

---

### Test Suite 3: Provider Profile Management

#### TC-3.1: View Own Profile (First Time)
Login as Super Admin (who doesn't have a provider profile yet):
- [ ] Navigate to "My Profile"
- [ ] Should see empty state or form to create profile
- [ ] Form should have fields: Specialization, Bio, Years of Experience, Session Duration

#### TC-3.2: Create Provider Profile
Continue as Super Admin:
- [ ] Select specialization: "Neurology"
- [ ] Enter bio: "Experienced neurologist with focus on headache treatment."
- [ ] Years of experience: 12
- [ ] Session duration: 60
- [ ] Submit → Should create successfully
- [ ] Profile should be displayed with entered information

#### TC-3.3: Update Provider Profile
Login as Dr. Smith:
- [ ] Navigate to "My Profile"
- [ ] Should see existing profile data
- [ ] Update bio to add more information
- [ ] Change session duration to 50 minutes
- [ ] Submit → Should update successfully
- [ ] Changes should be reflected

#### TC-3.4: Toggle Availability
Login as Dr. Johnson:
- [ ] Navigate to "My Profile"
- [ ] Toggle "Available for bookings" switch
- [ ] Save → Should update
- [ ] When unavailable, should not appear in booking search results

#### TC-3.5: Access Denied for Patients
Login as Alice Patient:
- [ ] Try to access provider profile URL directly → Should get 403/redirect
- [ ] Should not see "My Profile" in sidebar

---

### Test Suite 4: Provider Schedule Management

#### TC-4.1: View Schedule (First Time)
Login as Super Admin (new provider):
- [ ] Navigate to "My Schedule"
- [ ] Should see weekly schedule grid (Monday-Sunday)
- [ ] All days should be initially unavailable or empty

#### TC-4.2: Add Schedule - Single Day
Login as Dr. Smith:
- [ ] Navigate to "My Schedule"
- [ ] Click on Monday
- [ ] Set: Start Time 9:00 AM, End Time 5:00 PM
- [ ] Mark as "Available"
- [ ] Save → Should succeed
- [ ] Monday should show as available with times

#### TC-4.3: Add Schedule - Multiple Days
Continue as Dr. Smith:
- [ ] Add schedule for Tuesday (9am-5pm)
- [ ] Add schedule for Wednesday (9am-5pm)
- [ ] All three days should be visible and available

#### TC-4.4: Edit Existing Schedule
- [ ] Click edit on Monday's schedule
- [ ] Change end time to 6:00 PM
- [ ] Save → Should update
- [ ] New time should be displayed

#### TC-4.5: Mark Day as Unavailable
- [ ] Edit Tuesday's schedule
- [ ] Uncheck "Available"
- [ ] Save → Should update
- [ ] Tuesday should show as unavailable

#### TC-4.6: Validate Time Conflicts
- [ ] Try to create overlapping times (e.g., 9am-5pm and 3pm-7pm on same day)
- [ ] Should show error message
- [ ] Should not allow saving

#### TC-4.7: Access Denied for Patients
Login as Bob Patient:
- [ ] Try to access schedule URL directly → Should get 403/redirect
- [ ] Should not see "My Schedule" in sidebar

---

### Test Suite 5: Booking Appointments (Patient Flow)

#### TC-5.1: Start Booking Process
Login as Alice Patient:
- [ ] Click "Book Appointment" in sidebar
- [ ] Should see booking wizard (Step 1 of 4)
- [ ] Should see list of 5 specializations with icons and descriptions

#### TC-5.2: Select Specialization
- [ ] Click "Select" on "Cardiology ❤️"
- [ ] Should advance to Step 2
- [ ] Should see list of available cardiologists

#### TC-5.3: View Provider Cards
- [ ] Should see Dr. Smith's card with:
  - Avatar/initials
  - Name: "Dr. John Smith"
  - Experience badge: "15 years"
  - Session duration: "45 min"
  - Bio preview (truncated to 3 lines)
  - Two buttons: "Select Provider" and "View Full Profile"

#### TC-5.4: View Provider Details Page
- [ ] Click "View Full Profile" on Dr. Smith
- [ ] Should navigate to detailed profile page
- [ ] Should see:
  - Large avatar with gradient header
  - Full name and specialization
  - Experience, session duration, availability badges
  - Email (click to send email)
  - Full bio (not truncated)
  - Specialization details section
  - Availability schedule (sorted Monday-Sunday, only available days)
  - Session information card with gradient border
  - "Book Appointment" button
  - "Contact Provider" button
- [ ] Click "Back to Browse" → Should return to provider list

#### TC-5.5: Select Provider from Cards
- [ ] From provider list, click "Select Provider" on Dr. Smith
- [ ] Should advance to Step 3: Select Date
- [ ] Calendar should show only available dates (based on Dr. Smith's schedule)

#### TC-5.6: Select Appointment Date
- [ ] Select a date that falls on Monday (Dr. Smith works Mon-Fri)
- [ ] Should advance to Step 4: Select Time Slot
- [ ] Should see available time slots based on schedule (9am-5pm in 45-min intervals)

#### TC-5.7: Select Time Slot
- [ ] Select "10:00 AM" slot
- [ ] Should see confirmation summary showing:
  - Specialization: Cardiology
  - Provider: Dr. John Smith
  - Date: [selected date]
  - Time: 10:00 AM - 10:45 AM
  - Duration: 45 minutes
- [ ] Text area for notes

#### TC-5.8: Add Notes and Confirm Booking
- [ ] Enter notes: "Follow-up for annual checkup"
- [ ] Click "Confirm Booking"
- [ ] Should show success message
- [ ] Should redirect to "My Appointments"

#### TC-5.9: Book from Provider Details Page
- [ ] Navigate to provider details (from Step 2, click "View Full Profile")
- [ ] Click "Book Appointment" button
- [ ] Should jump to Step 3 with provider pre-selected
- [ ] Complete booking process

#### TC-5.10: Verify Slot Conflicts
- [ ] Try to book the same time slot again (10:00 AM on same date with Dr. Smith)
- [ ] Slot should not be available or show as taken
- [ ] Cannot double-book

---

### Test Suite 6: View Appointments

#### TC-6.1: View My Appointments (Patient)
Login as Alice Patient:
- [ ] Navigate to "My Appointments"
- [ ] Should see 3 appointments:
  - Pending: Dr. Smith (3 days from now, 10:00 AM)
  - Confirmed: Dr. Johnson (5 days from now, 2:00 PM)
  - Pending: Dr. Williams (10 days from now, 3:00 PM)
- [ ] Each card should show:
  - Provider name and specialization
  - Date and time
  - Status badge (color-coded)
  - Notes
  - Action buttons based on status

#### TC-6.2: Filter Appointments by Status
- [ ] Click "Pending" filter → Should show only pending appointments (2)
- [ ] Click "Confirmed" filter → Should show only confirmed (1)
- [ ] Click "All" → Should show all 3

#### TC-6.3: Cancel Appointment
- [ ] On a pending appointment, click "Cancel"
- [ ] Confirm cancellation → Should update status to "cancelled"
- [ ] Status badge should change

#### TC-6.4: View Appointment Details
- [ ] Click on an appointment card
- [ ] Should show detailed modal/page with full information
- [ ] Should see provider details, date, time, notes

#### TC-6.5: View Past Appointments
Login as Bob Patient:
- [ ] Navigate to "My Appointments"
- [ ] Should see:
  - Completed: Dr. Smith (7 days ago) - green badge
  - Cancelled: Dr. Williams (2 days ago) - red badge
- [ ] Should not have action buttons (past appointments)

---

### Test Suite 7: Manage All Appointments (Admin)

#### TC-7.1: View All Appointments
Login as Admin:
- [ ] Navigate to "My Appointments" (or dedicated admin view)
- [ ] Should see ALL appointments from all patients
- [ ] Should see appointments from Alice and Bob

#### TC-7.2: Filter by Provider
- [ ] Filter by "Dr. Smith" → Should show only Dr. Smith's appointments
- [ ] Filter by "Dr. Johnson" → Should show only Dr. Johnson's appointments

#### TC-7.3: Filter by Status
- [ ] Filter "Pending" → Should show all pending appointments
- [ ] Filter "Completed" → Should show all completed appointments
- [ ] Filter "Cancelled" → Should show all cancelled appointments

#### TC-7.4: Manage Appointment Status (Admin)
- [ ] Select a pending appointment
- [ ] Change status to "Confirmed" → Should update
- [ ] Select another appointment
- [ ] Change status to "Completed" → Should update

#### TC-7.5: Search Appointments
- [ ] Search by patient name "Alice" → Should show Alice's appointments
- [ ] Search by date → Should filter by date range

---

### Test Suite 8: Provider View Appointments

#### TC-8.1: View Own Bookings
Login as Dr. Smith:
- [ ] Navigate to "My Appointments"
- [ ] Should see only appointments where Dr. Smith is the provider
- [ ] Should see appointments from Alice and Bob

#### TC-8.2: Update Appointment Status
- [ ] Select Alice's pending appointment
- [ ] Mark as "Confirmed" → Should update
- [ ] Patient should see updated status

#### TC-8.3: Add Provider Notes
- [ ] Open appointment details
- [ ] Add notes: "Patient vitals checked, all normal"
- [ ] Save → Should update
- [ ] Notes should be visible to admin and patient

---

### Test Suite 9: Responsive Design & UI/UX

#### TC-9.1: Mobile View (Resize Browser)
- [ ] Test booking wizard on mobile viewport
- [ ] Provider cards should stack (1 column)
- [ ] Step indicators should be mobile-friendly
- [ ] Forms should be touch-friendly

#### TC-9.2: Dark Mode
- [ ] Toggle dark mode
- [ ] All booking pages should adapt properly
- [ ] Gradient headers should be visible
- [ ] Text should be readable
- [ ] Cards should have proper contrast

#### TC-9.3: Animations & Transitions
- [ ] Step navigation should have smooth transitions
- [ ] Hover effects on provider cards
- [ ] Button hover states
- [ ] Loading states when fetching data

---

### Test Suite 10: Edge Cases & Error Handling

#### TC-10.1: No Providers Available
- [ ] Create a new specialization with no providers
- [ ] Try to book appointment → Should show "No providers available"

#### TC-10.2: Provider Unavailable
- [ ] Mark Dr. Johnson as unavailable in profile
- [ ] Dr. Johnson should NOT appear in booking search
- [ ] Existing appointments should still be visible

#### TC-10.3: No Available Slots
- [ ] Book all available slots for a specific day
- [ ] Try to book again → Should show "No slots available"

#### TC-10.4: Past Date Selection
- [ ] Try to select a past date in calendar (if possible)
- [ ] Should be disabled or show error

#### TC-10.5: Form Validation
- [ ] Try to submit provider profile without required fields → Should show errors
- [ ] Try to submit schedule with invalid times → Should show errors
- [ ] Try to book without notes (if required) → Should validate

---

## 📊 Test Results Summary

### Roles & Permissions
- [ ] Super Admin - All features working
- [ ] Admin - Booking management working
- [ ] Providers - Profile, schedule, bookings working
- [ ] Patients - Booking flow working
- [ ] Viewer - Access properly denied

### Core Features
- [ ] Specializations CRUD
- [ ] Provider Profile Management
- [ ] Provider Schedule Configuration
- [ ] Appointment Booking (4-step wizard)
- [ ] Appointment Management
- [ ] Enhanced Provider Cards
- [ ] Provider Details Page

### UI/UX
- [ ] Responsive design
- [ ] Dark mode support
- [ ] Animations & transitions
- [ ] Loading states
- [ ] Error messages

### Data Integrity
- [ ] No double bookings
- [ ] Proper status transitions
- [ ] Appointment notes saved
- [ ] Schedule conflicts prevented

---

## 🐛 Bugs Found

| ID | Description | Severity | Status | Assigned To |
|----|-------------|----------|--------|-------------|
| BUG-001 | | | | |

---

## ✅ Test Sign-Off

- [ ] All test suites passed
- [ ] All bugs fixed
- [ ] Documentation updated
- [ ] Ready for production

**Tested By:** ___________________  
**Date:** ___________________  
**Version:** ___________________
