<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ __('cachet::settings.manage_notifications.test_email_subject') }}</title>
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
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" width="64" height="64" style="width: 64px; height: 64px; border-radius: 50%; background-color: #16a34a; background-color: {{ $colors['accent'] }}; color: #ffffff; color: {{ $colors['accent-foreground'] }}; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 30px; line-height: 64px; font-weight: 700;">
                                        &check;
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 28px 0 12px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 24px; font-weight: 700; color: #18181b;">
                                {{ __('cachet::settings.manage_notifications.test_email_heading') }}
                            </h1>
                            <p style="margin: 0 0 32px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 15px; line-height: 24px; color: #52525b;">
                                {{ __('cachet::settings.manage_notifications.test_email_body', ['app' => $appName]) }}
                            </p>
                            <a href="{{ $statusPageUrl }}" style="display: inline-block; padding: 12px 28px; border-radius: 9999px; background-color: #16a34a; background-color: {{ $colors['accent'] }}; color: #ffffff; color: {{ $colors['accent-foreground'] }}; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none;">
                                {{ __('cachet::settings.manage_notifications.test_email_button') }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-top: 24px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 13px;">
                            <a href="https://cachethq.io" rel="noopener" style="color: #a1a1aa; text-decoration: underline;">
                                {{ __('cachet::settings.manage_notifications.test_email_footer') }}
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
