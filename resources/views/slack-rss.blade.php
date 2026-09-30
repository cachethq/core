@use('Cachet\Enums\IncidentStatusEnum')
<?=
'<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ $statusPageName }} status</title>
        <link>{{ route('cachet.status-page') }}</link>
        <description>{{ $statusPageName }} status page updates</description>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <docs>https://validator.w3.org/feed/docs/rss2.html</docs>
        <generator>Cachet</generator>
        @foreach($items as $item)
        @php
            $incident = $item['incident'];
            $update = $item['update'];
            $status = $update?->status ?? ($incident->updates->isEmpty() ? $incident->status : IncidentStatusEnum::unknown);
            $message = $update?->formattedMessage() ?? $incident->formattedMessage();
            $url = route('cachet.status-page.incident', $incident).'#'.($update ? 'update-'.$update->id : 'reported');
            $description = e($incident->name).'<br/><br/>Status: '.e($status?->getLabel()).'<br/><br/>'.$message;

            if ($incident->components->isNotEmpty()) {
                $components = $incident->components->pluck('name')->map(fn (string $name): string => e($name))->join(', ');
                $description .= '<br/><br/>⚠️ Affected components: '.$components;
            }

            $description = str_replace(']]>', ']]]]><![CDATA[>', $description);
        @endphp
        <item>
            @if($update?->status === IncidentStatusEnum::fixed)
            <title><![CDATA[✅ {{ $statusPageName }} - Incident resolved]]></title>
            @elseif($update)
            <title><![CDATA[🚨 {{ $statusPageName }} - Incident update]]></title>
            @else
            <title><![CDATA[🚨 {{ $statusPageName }} - New incident]]></title>
            @endif
            <link>{{ $url }}</link>
            <guid>{{ $url }}</guid>
            <pubDate>{{ $item['publishedAt']->toRssString() }}</pubDate>
            <description><![CDATA[{!! $description !!}]]></description>
            <content:encoded><![CDATA[{!! $description !!}]]></content:encoded>
        </item>
        @endforeach
    </channel>
</rss>
