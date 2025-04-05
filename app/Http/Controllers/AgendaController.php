<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgendaController extends Controller
{
    public function index()
    {
        return Inertia::render('Agenda/Index');
    }

    public function getItems(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get items that overlap with the requested date range
        $items = AgendaItem::where(function ($query) use ($startDate, $endDate) {
            // Items that start within the range
            $query->whereBetween('start_date', [$startDate, $endDate])
                // OR items that end within the range
                ->orWhereBetween('end_date', [$startDate, $endDate])
                // OR items that span across the range
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })
            ->orderBy('start_date')
            ->orderBy('end_date')
            ->get();

        return response()->json($items);
    }
}
