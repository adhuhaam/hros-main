<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Holiday::query();

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $holidays = $query->orderBy('date')->paginate(15);
        $years = range(date('Y') - 2, date('Y') + 2);

        return view('holidays.index', compact('holidays', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $holidayTypes = ['Public Holiday', 'Company Holiday', 'Optional Holiday', 'Other'];
        
        return view('holidays.create', compact('holidayTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_month' => 'nullable|integer|min:1|max:12',
            'recurring_day' => 'nullable|integer|min:1|max:31',
        ]);

        $holiday = Holiday::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_month' => $validated['recurring_month'],
            'recurring_day' => $validated['recurring_day'],
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        return view('holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        $holidayTypes = ['Public Holiday', 'Company Holiday', 'Optional Holiday', 'Other'];
        
        return view('holidays.edit', compact('holiday', 'holidayTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_month' => 'nullable|integer|min:1|max:12',
            'recurring_day' => 'nullable|integer|min:1|max:31',
        ]);

        $holiday->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_month' => $validated['recurring_month'],
            'recurring_day' => $validated['recurring_day'],
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        
        return redirect()->route('holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }

    /**
     * Export holidays to CSV
     */
    public function export(Request $request)
    {
        $query = Holiday::query();

        // Apply filters
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $holidays = $query->orderBy('date')->get();

        $filename = 'holidays_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($holidays) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Name',
                'Date',
                'Type',
                'Description',
                'Is Recurring',
                'Recurring Month',
                'Recurring Day',
                'Created At'
            ]);

            // Add data
            foreach ($holidays as $holiday) {
                fputcsv($file, [
                    $holiday->name,
                    $holiday->date->format('Y-m-d'),
                    $holiday->type,
                    $holiday->description ?? '',
                    $holiday->is_recurring ? 'Yes' : 'No',
                    $holiday->recurring_month ?? '',
                    $holiday->recurring_day ?? '',
                    $holiday->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 