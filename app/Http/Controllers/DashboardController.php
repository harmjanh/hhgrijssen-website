<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Add any data you want to pass to the dashboard view
        $data = [
            'stats' => [
                'total_users' => User::count(),
                // Add more stats as needed
            ],
        ];

        return Inertia::render('Dashboard', $data);
    }
}
