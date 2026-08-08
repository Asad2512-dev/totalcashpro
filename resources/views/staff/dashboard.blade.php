<x-layouts.staff title="Dashboard" active="dashboard">
    <x-admin.mobile-page-header
        title="Home"
        description="Your shift, clock status and today's work at a glance."
    />

    <div class="admin-stat-grid--compact">
        <x-admin.stat compact label="Branch" :value="$branchName" />
        <x-admin.stat compact label="Clock" :value="str_replace('_', ' ', ucfirst($state['state']))" tone="info" :change="$state['hours'] !== null ? number_format((float) $state['hours'], 2).'h today' : null" />
        <x-admin.stat
            compact
            label="Today's shift"
            :value="$todayShift ? ($todayShift->start_time?->format('H:i').'–'.$todayShift->end_time?->format('H:i')) : 'No shift'"
            :change="$todayShift?->rotaSection?->name"
            tone="neutral"
        />
        <x-admin.stat compact label="This week" :value="number_format($weeklyHours ?? 0, 2).'h'" tone="success" />
    </div>

    <x-admin.action-grid class="mt-3">
        <x-admin.action-tile href="{{ route('staff.clock') }}" icon="clock" label="Clock" variant="primary" />
        <x-admin.action-tile href="{{ route('staff.shift') }}" icon="layout" label="My shift" />
        <x-admin.action-tile href="{{ route('staff.hours') }}" icon="chart" label="Hours" />
        <x-admin.action-tile href="{{ route('staff.leave') }}" icon="calendar" label="Leave" />
    </x-admin.action-grid>

    <div class="admin-section-grid lg:grid-cols-2">