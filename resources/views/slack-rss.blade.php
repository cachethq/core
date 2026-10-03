<?=
'<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ $statusPageName }} incident updates</title>
        <link>{{ route('cachet.status-page') }}</link>
        <description>Latest incident reports and updates from {{ $statusPageName }}</description>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <docs>https://validator.w3.org/feed/docs/rss2.html</docs>
        <generator>Cachet</generator>
        @foreach($items as $item)
        @php
            $incident = $item['incident'];
            $update = $item['update'];
            $status = $update ? ($update->status?->getLabel() ?? 'Updated') : 'Reported';
            $message = $update?->formattedMessage() ?? $incident->formattedMessage();
            $url = route('cachet.status-page.incident', $incident).'#'.($update ? 'update-'.$update->id : 'reported');
            $title = str_replace(']]>', ']]]]><![CDATA[>', '['.$status.'] '.$incident->name);
            $description = $message.'<br/><br/><strong>Status:</strong> '.e($status);

            if ($incident->components->isNotEmpty()) {
                $components = $incident->components->pluck('name')->map(fn (string $name): string => e($name))->join(', ');
                $description .= '<br/><strong>Affected components:</strong> '.$components;
            }

            $description = str_replace(']]>', ']]]]><![CDATA[>', $description);
        @endphp
        <item>
            <title><![CDATA[{!! $title !!}]]></title>
            <link>{{ $url }}</link>
            <guid>{{ $url }}</guid>
            <pubDate>{{ $item['publishedAt']->toRssString() }}</pubDate>
            <description><![CDATA[{!! $description !!}]]></description>
            <content:encoded><![CDATA[{!! $description !!}]]></content:encoded>
        </item>
        @endforeach
    </channel>
</rss>
