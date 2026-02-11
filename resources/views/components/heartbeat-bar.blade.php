@props(['heartbeats' => [], 'uptime' => null, 'sslDays' => null])

@php
    $heartbeats = is_array($heartbeats) ? $heartbeats : [];
    $displayHeartbeats = array_pad($heartbeats, -30, null);
    $displayHeartbeats = array_slice($displayHeartbeats, -30);
@endphp

<div class="flex flex-col gap-1.5 mt-2">
    {{-- Heartbeat bar visualization --}}
    <div class="flex items-center gap-0.5">
        @foreach($displayHeartbeats as $hb)
            @php
                $status = is_array($hb) ? ($hb['status'] ?? null) : null;
                $ping = is_array($hb) ? ($hb['ping'] ?? null) : null;
                $time = is_array($hb) ? ($hb['time'] ?? null) : null;

                // Color based on status
                $colorClass = match($status) {
                    1 => 'bg-green-500',
                    0 => 'bg-red-500',
                    2 => 'bg-yellow-500',
                    3 => 'bg-blue-500',
                    default => 'bg-zinc-300 dark:bg-zinc-600',
                };

                $tooltip = match($status) {
                    1 => 'Online' . ($ping ? " ({$ping}ms)" : ''),
                    0 => 'Offline',
                    2 => 'Pending',
                    3 => 'Maintenance',
                    default => 'No data',
                };
            @endphp
            <div
                class="w-1.5 h-4 rounded-sm {{ $colorClass }} transition-all hover:scale-y-125"
                title="{{ $tooltip }}"
            ></div>
        @endforeach
    </div>

    {{-- Status info row --}}
    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
        <div class="flex items-center gap-3">
            @if($uptime !== null)
                @php
                    $uptimePercent = round($uptime * 100, 2);
                    $uptimeColor = $uptimePercent >= 99 ? 'text-green-600 dark:text-green-400' :
                                   ($uptimePercent >= 95 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                @endphp
                <span class="font-medium {{ $uptimeColor }}">{{ $uptimePercent }}% uptime (24h)</span>
            @endif
            @if($sslDays !== null && $sslDays > 0)
                @php
                    $sslColor = $sslDays > 30 ? 'text-green-600 dark:text-green-400' :
                                ($sslDays > 7 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                @endphp
                <span class="{{ $sslColor }}">
                    <x-heroicon-o-lock-closed class="inline-block w-3 h-3 mr-0.5" />
                    SSL: {{ $sslDays }} days
                </span>
            @endif
        </div>
        <span class="text-zinc-400 dark:text-zinc-500">Last 30 checks</span>
    </div>
</div>
