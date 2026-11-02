<?=
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<rss version="2.0">
    <channel>
        <title><![CDATA[ {{ __('cachet::cachet.rss_feed', ['name' => $statusPageName]) }} ]]></title>
        <link><![CDATA[ {{ $feedUrl }} ]]></link>
        @if($statusAbout)
        <description><![CDATA[ {{ $statusAbout  }} ]]></description>
        @endif
        <language>{{ config('app.locale') }}</language>
        <pubDate>{{ now()->toRssString() }}</pubDate>

        @foreach($items as $item)
        @php
            $incident = $item['model'];
            $isSchedule = $incident instanceof \Cachet\Models\Schedule;
            $publishedAt = $item['publishedAt'];
            $feedVersion = sha1(($isSchedule ? 'schedule-'.$incident->id : $incident->guid).$incident->updated_at?->format('Y-m-d H:i:s.u').$incident->updates->map(
                fn ($update) => $update->id.$update->updated_at?->format('Y-m-d H:i:s.u')
            )->join(''));
        @endphp
        <item>
            <title>[{{ $incident->status?->getLabel() }}] {{ $incident->name }}</title>
            <link>{{ $isSchedule ? route('cachet.status-page.schedule', $incident) : route('cachet.status-page.incident', $incident) }}</link>
            <description><![CDATA[
{{ ($isSchedule ? $incident->scheduled_at : $incident->created_at)->toRssString() }}: {!! str_replace(']]>', ']]]]><![CDATA[>', $incident->formattedMessage()) !!}
@foreach($incident->updates as $update)
[{{ $update->status?->getLabel() }}] {{ $update->updated_at->toRssString() }}: {!! str_replace(']]>', ']]]]><![CDATA[>', $update->formattedMessage()) !!}
@endforeach
]]></description>
            @foreach($incident->components as $component)
            <category domain="component:{{ $component->id }}">{{ $component->name }}</category>
            @endforeach
            <guid isPermaLink="false">{{ $feedVersion }}</guid>
            <pubDate>{{ $publishedAt->toRssString() }}</pubDate>
        </item>
        @endforeach
    </channel>
</rss>
