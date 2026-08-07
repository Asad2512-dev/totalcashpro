<x-layouts.admin title="Email Queue" active="email-queue">
    <x-admin.toolbar description="Pending and failed queued jobs (emails, notifications)." />

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Failed jobs</h3>
        <div class="admin-table-wrap mt-4">
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

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pending jobs</h3>
        <div class="admin-table-wrap mt-4">
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
</x-layouts.admin>
