<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Services\BusinessAdmin\DashboardService;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function __invoke(): View
    {
        return view('business-admin.dashboard.index', $this->dashboard->build(auth()->user()));
    }
}
