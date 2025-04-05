<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Declaration;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Add any data you want to pass to the dashboard view
        $data = [
            'stats' => [
                'total_declarations' => Declaration::where('user_id', $user->id)->count(),
                // Add more stats as needed
            ],
        ];

        return Inertia::render('Dashboard', $data);
    }
}
