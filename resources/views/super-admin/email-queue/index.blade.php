<x-layouts.admin title="Email Queue" active="email-queue">
    <x-admin.mobile-page-header
        class="lg:hidden"
        title="Email Queue"
        description="Pending and failed queued jobs."
    />

    <x-admin.toolbar description="Pending and failed queued jobs (emails, notifications)." class="hidden lg:flex" />

    <div class="admin-panel-grid mt-4 lg:mt-6">
        <x-admin.card>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Failed jobs</h3>
            <div class="admin-mobile-records mt-4 lg:hidden">
                @forelse ($failedJobs as $job)
                    <article class="admin-mobile-record">
                        <p class="admin-mobile-record__title">Job #{{ $job->id }}</p>
                        <dl class="mt-2 space-y-1">
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Queue</dt>
                                <dd class="admin-mobile-record__value">{{ $job->queue }}</dd>
                            </div>
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Failed at</dt>
                                <dd class="admin-mobile-record__value">{{ $job->failed_at }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">No failed jobs.</p>
                @endforelse
            </div>
            <div class="admin-table-wrap mt-4 hidden lg:block">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Queue</th>
                            <th>Failed at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($failedJobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->queue }}</td>
                                <td>{{ $job->failed_at }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-gray-500">No failed jobs.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>

        <x-admin.card>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pending jobs</h3>
            <div class="admin-mobile-records mt-4 lg:hidden">
                @forelse ($pendingJobs as $job)
                    <article class="admin-mobile-record">
                        <p class="admin-mobile-record__title">Job #{{ $job->id }}</p>
                        <dl class="mt-2 space-y-1">
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Queue</dt>
                                <dd class="admin-mobile-record__value">{{ $job->queue }}</dd>
                            </div>
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Attempts</dt>
                                <dd class="admin-mobile-record__value">{{ $job->attempts }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <p class="text-sm text-gray-500">Queue is empty.</p>
                @endforelse
            </div>
            <div class="admin-table-wrap mt-4 hidden lg:block">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Queue</th>
                            <th>Attempts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingJobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->queue }}</td>
                                <td>{{ $job->attempts }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-gray-500">Queue is empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    </div>
</x-layouts.admin>
