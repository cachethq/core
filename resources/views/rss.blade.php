<?=
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<rss version="2.0">
    <channel>
        <title><![CDATA[ {{ __('cachet::cachet.rss_feed', ['name' => $statusPageName]) }} ]]></title>
        <link><![CDATA[ {{ route('cachet.rss') }} ]]></link>
        @if($statusAbout)
        <description><![CDATA[ {{ $statusAbout  }} ]]></description>
        @endif
        <language>{{ config('app.locale') }}</language>
        <pubDate>{{ now()->toRssString() }}</pubDate>

        @foreach($incidents as $incident)
        <item>
            <title>{{ $incident->name }}</title>
            <link>{{ route('cachet.status-page.incident', $incident) }}</link>
            <description><![CDATA[{!! $incident->message !!}]]></description>
            <guid>{{ $incident->guid }}</guid>
            <pubDate>{{ $incident->created_at->toRssString() }}</pubDate>
        </item>
        @endforeach
    </channel>
</rss>
