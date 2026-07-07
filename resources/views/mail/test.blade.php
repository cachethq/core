@component('cachet::mail.layout', ['title' => __('cachet::settings.manage_notifications.test_email_subject')])
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
@endcomponent
