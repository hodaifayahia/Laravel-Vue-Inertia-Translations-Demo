<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpecializationController extends Controller
{
    /**
     * Display a listing of specializations.
     */
    public function index()
    {
        $specializations = Specialization::withCount('providerProfiles')
            ->orderBy('name')
            ->get();

        return Inertia::render('Dashboard/Bookings/Specializations/Index', [
            'specializations' => $specializations,
        ]);
    }

    /**
     * Store a newly created specialization.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:specializations,slug',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $specialization = Specialization::create($validated);

        return redirect()->back()->with('success', 'Specialization created successfully!');
    }

    /**
     * Update the specified specialization.
     */
    public function update(Request $request, Specialization $specialization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:specializations,slug,' . $specialization->id,
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $specialization->update($validated);

        return redirect()->back()->with('success', 'Specialization updated successfully!');
    }

    /**
     * Remove the specified specialization.
     */
    public function destroy(Specialization $specialization)
    {
        if ($specialization->providerProfiles()->count() > 0) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete specialization with active providers.'
            ]);
        }

        $specialization->delete();

        return redirect()->back()->with('success', 'Specialization deleted successfully!');
    }

    /**
     * Get active specializations for booking.
     */
    public function active()
    {
        $specializations = Specialization::active()
            ->has('activeProviders')
            ->withCount('activeProviders')
            ->orderBy('name')
            ->get();

        return response()->json($specializations);
    }
}
