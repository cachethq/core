/* Base */

body,
body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
        'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    position: relative;
}

body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #52525b;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
}

p,
ul,
ol,
blockquote {
    line-height: 1.4;
    text-align: start;
}

a {
    color: #18181b;
}

a img {
    border: none;
}

/* Typography */

h1 {
    color: #18181b;
    font-size: 18px;
    font-weight: bold;
    margin-top: 0;
    text-align: start;
}

h2 {
    font-size: 16px;
    font-weight: bold;
    margin-top: 0;
    text-align: start;
}

h3 {
    font-size: 14px;
    font-weight: bold;
    margin-top: 0;
    text-align: left;
}

p {
    font-size: 16px;
    line-height: 1.5em;
    margin: 0 0 16px;
    text-align: left;
}

p.sub {
    color: #a1a1aa;
    font-size: 13px;
}

img {
    max-width: 100%;
}

/* Layout */

.wrapper {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: {{ $colors['accent-background'] }};
    margin: 0;
    padding: 0;
    width: 100%;
}

.content {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
}

/* Header */

.header {
    padding: 25px 0;
    text-align: center;
}

.header a {
    color: #18181b;
    font-size: 19px;
    font-weight: bold;
    text-decoration: none;
}

/* Logo */

.logo {
    height: 32px;
    margin-bottom: 8px;
    max-height: 32px;
    width: auto;
}

.logo-banner {
    height: 40px;
    max-height: 40px;
    width: auto;
}

.header-name {
    display: block;
}

/* Body */

.body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: {{ $colors['accent-background'] }};
    border-bottom: 1px solid {{ $colors['accent-background'] }};
    border-top: 1px solid {{ $colors['accent-background'] }};
    margin: 0;
    padding: 0;
    width: 100%;
}

.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 640px;
    background-color: #ffffff;
    border-color: #e4e4e7;
    border-radius: 12px;
    border-width: 1px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    margin: 0 auto;
    padding: 0;
    width: 640px;
}

.inner-body a {
    word-break: break-all;
}

/* Subcopy */

.subcopy {
    border-top: 1px solid #e4e4e7;
    margin-top: 25px;
    padding-top: 25px;
}

.subcopy p {
    font-size: 14px;
}

/* Footer */

.footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 640px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 640px;
}

.footer p {
    color: #a1a1aa;
    font-size: 12px;
    text-align: center;
}

.footer a {
    color: #a1a1aa;
    text-decoration: underline;
}

/* Tables */

.table table {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    width: 100%;
}

.table th {
    border-bottom: 1px solid #e4e4e7;
    margin: 0;
    padding-bottom: 8px;
}

.table td {
    color: #52525b;
    font-size: 15px;
    line-height: 18px;
    margin: 0;
    padding: 10px 0;
}

.content-cell {
    max-width: 100vw;
    padding: 32px;
}

/* Buttons */

.action {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    padding: 0;
    text-align: center;
    width: 100%;
    float: unset;
}

.button {
    -webkit-text-size-adjust: none;
    border-radius: 9999px;
    color: #fff;
    display: inline-block;
    font-weight: 600;
    overflow: hidden;
    padding: 12px 28px;
    text-align: center;
    text-decoration: none;
}

.button-blue,
.button-primary {
    background-color: {{ $colors['accent'] }};
    color: {{ $colors['accent-foreground'] }};
}

.button-green,
.button-success {
    background-color: #16a34a;
}

.button-red,
.button-error {
    background-color: #dc2626;
}

/* Panels */

.panel {
    border-left: {{ $colors['accent'] }} solid 4px;
    margin: 21px 0;
}

.panel-content {
    background-color: #fafafa;
    color: #52525b;
    padding: 16px;
}

.panel-content p {
    color: #52525b;
}

.panel-item {
    padding: 0;
}

.panel-item p:last-of-type {
    margin-bottom: 0;
    padding-bottom: 0;
}

/* Utilities */

.break-all {
    word-break: break-all;
}
