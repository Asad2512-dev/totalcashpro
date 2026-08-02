<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SuperAdminListingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PageController extends Controller
{
    public function __construct(
        private readonly SuperAdminListingService $listings,
    ) {}

    public function __invoke(Request $request): View
    {
        $routeName = (string) $request->route()?->getName();
        $page = str_replace('super-admin.', '', $routeName);

        return view('admin.page', $this->listings->page($page, $request));
    }
}
