<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->published()
            ->with('author')
            ->latest()
            ->paginate(10);

        return view('tenant.announcements.index', compact('announcements'));
    }
}
