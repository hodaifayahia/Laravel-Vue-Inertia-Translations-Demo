<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderProfileController extends Controller
{
    /**
     * Display the provider's profile configuration.
     */
    public function show()
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('book-sys')) {
            abort(403, 'You do not have permission to be a provider.');
        }

        $profile = $user->providerProfile()->with(['specialization', 'schedules' => function($query) {
            $query->orderBy('day_of_week');
        }])->first();

        $specializations = Specialization::active()->orderBy('name')->get();

        return Inertia::render('Dashboard/Bookings/Provider/Profile', [
            'profile' => $profile,
            'specializations' => $specializations,
        ]);
    }

    /**
     * Create or update provider profile.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('book-sys')) {
            abort(403, 'You do not have permission to be a provider.');
        }

        $validated = $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
            'bio' => 'nullable|string|max:1000',
            'years_experience' => 'required|integer|min:0|max:100',
            'slot_duration' => 'required|integer|in:15,30,45,60',
            'is_available' => 'boolean',
        ]);

        $profile = $user->providerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->back()->with('success', 'Provider profile updated successfully!');
    }

    /**
     * Get providers by specialization.
     */
    public function bySpecialization(Specialization $specialization)
    {
        $providers = ProviderProfile::where('specialization_id', $specialization->id)
            ->where('is_available', true)
            ->with(['user:id,name,email,avatar', 'specialization'])
            ->get();

        return response()->json($providers);
    }

    /**
     * Get all providers (for admin).
     */
    public function index()
    {
        $this->authorize('manage bookings');

        $providers = ProviderProfile::with(['user', 'specialization'])
            ->withCount('appointments')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Dashboard/Bookings/Providers/Index', [
            'providers' => $providers,
        ]);
    }

    /**
     * Show provider details page.
     */
    public function details(ProviderProfile $provider)
    {
        $provider->load([
            'user:id,name,email,avatar',
            'specialization',
            'schedules' => function($query) {
                $query->where('is_available', true)
                      ->orderBy('day_of_week');
            }
        ]);

        return Inertia::render('Dashboard/Bookings/Provider/Details', [
            'provider' => $provider,
        ]);
    }
}
