<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notice::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by title or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notices = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $noticeTypes = ['General', 'Important', 'Urgent', 'Policy Update', 'Event', 'Other'];
        
        return view('notices.create', compact('noticeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'type' => 'required|string|max:255',
            'status' => 'required|in:Draft,Published,Archived',
            'publish_date' => 'nullable|date|after_or_equal:today',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_featured' => 'boolean',
            'target_audience' => 'nullable|string|max:255',
        ]);

        $notice = Notice::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'publish_date' => $validated['publish_date'],
            'expiry_date' => $validated['expiry_date'],
            'is_featured' => $validated['is_featured'] ?? false,
            'target_audience' => $validated['target_audience'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('notices.index')
            ->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        return view('notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        $noticeTypes = ['General', 'Important', 'Urgent', 'Policy Update', 'Event', 'Other'];
        
        return view('notices.edit', compact('notice', 'noticeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'type' => 'required|string|max:255',
            'status' => 'required|in:Draft,Published,Archived',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_featured' => 'boolean',
            'target_audience' => 'nullable|string|max:255',
        ]);

        $notice->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'publish_date' => $validated['publish_date'],
            'expiry_date' => $validated['expiry_date'],
            'is_featured' => $validated['is_featured'] ?? false,
            'target_audience' => $validated['target_audience'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('notices.index')
            ->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        
        return redirect()->route('notices.index')
            ->with('success', 'Notice deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Notice $notice)
    {
        $notice->update([
            'is_featured' => !$notice->is_featured,
        ]);

        return redirect()->back()
            ->with('success', 'Notice featured status updated successfully.');
    }

    /**
     * Export notices to CSV
     */
    public function export(Request $request)
    {
        $query = Notice::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notices = $query->orderBy('created_at', 'desc')->get();

        $filename = 'notices_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($notices) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Title',
                'Type',
                'Status',
                'Content',
                'Publish Date',
                'Expiry Date',
                'Is Featured',
                'Target Audience',
                'Created At'
            ]);

            // Add data
            foreach ($notices as $notice) {
                fputcsv($file, [
                    $notice->title,
                    $notice->type,
                    $notice->status,
                    strip_tags($notice->content),
                    $notice->publish_date ? $notice->publish_date->format('Y-m-d') : '',
                    $notice->expiry_date ? $notice->expiry_date->format('Y-m-d') : '',
                    $notice->is_featured ? 'Yes' : 'No',
                    $notice->target_audience ?? '',
                    $notice->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 