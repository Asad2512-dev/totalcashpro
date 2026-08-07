<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

final class EmailQueueController extends Controller
{
    public function index(): View
    {
        $failed = DB::table('failed_jobs')->orderByDesc('failed_at')->limit(50)->get();
        $pending = DB::table('jobs')->orderByDesc('id')->limit(50)->get();

        return view('super-admin.email-queue.index', [
            'failedJobs' => $failed,
            'pendingJobs' => $pending,
        ]);
    }
}
