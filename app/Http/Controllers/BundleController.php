<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BundleController extends Controller
{
    /**
     * Display a listing of available bundles
     */
    public function index(): View
    {
        $bundles = Bundle::available()
            ->with(['courses' => function ($query) {
                $query->where('courses.status', 'published')
                      ->where('courses.is_active', true)
                      ->orderBy('bundle_courses.order');
            }])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('bundles.index', compact('bundles'));
    }

    /**
     * Display the specified bundle
     */
    public function show(Bundle $bundle): View
    {
        if (!$bundle->isAvailable()) {
            abort(404, 'Bundle not available');
        }

        $bundle->load(['courses' => function ($query) {
            $query->where('courses.status', 'published')
                  ->where('courses.is_active', true)
                  ->withCount('lessons')
                  ->orderBy('bundle_courses.order');
        }]);

        $user = auth()->user();
        $hasPurchased = $user ? $user->hasPurchasedBundle($bundle) : false;
        $hasSubscription = $user ? $user->hasActiveSubscription() : false;

        return view('bundles.show', compact('bundle', 'hasPurchased', 'hasSubscription'));
    }
}