<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title ?? $appName }}</title>
</head>
<body style="margin: 0; padding: 0; word-break: break-word; -webkit-font-smoothing: antialiased; background-color: #fafafa; background-color: {{ $colors['accent-background'] }};">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #fafafa; background-color: {{ $colors['accent-background'] }};">
        <tr>
            <td align="center" style="padding: 48px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 480px;">
                    <tr>
                        <td align="center" style="padding-bottom: 24px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 18px; font-weight: 700; color: #27272a; color: {{ $colors['accent-content'] }};">
                            {{ $appName }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background-color: #ffffff; border-radius: 16px; padding: 48px 40px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-top: 24px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 13px;">
                            @isset($unsubscribeUrl)
                                <a href="{{ $unsubscribeUrl }}" style="color: #a1a1aa; text-decoration: underline;">{{ __('cachet::subscriber.mail.unsubscribe') }}</a>
                                <span style="color: #d4d4d8;">&middot;</span>
                            @endisset
                            <a href="https://cachethq.io" rel="noopener" style="color: #a1a1aa; text-decoration: underline;">{{ __('cachet::cachet.mail.powered_by') }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
