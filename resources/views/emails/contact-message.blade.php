<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .wrap {
            width: 100%;
            padding: 40px 16px;
            box-sizing: border-box;
        }

        .card {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .bar {
            height: 6px;
            background: linear-gradient(90deg, #0f766e 0%, #14b8a6 100%);
        }

        .header {
            padding: 32px 36px 8px;
        }

        .body {
            padding: 0 36px 32px;
            line-height: 1.65;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 26px;
            line-height: 1.2;
            color: #0f172a;
        }

        p {
            margin: 0 0 16px;
            font-size: 15px;
            color: #374151;
        }

        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
            overflow: hidden;
            margin: 24px 0;
        }

        .row {
            display: flex;
            gap: 16px;
            justify-content: space-between;
            padding: 14px 16px;
            border-top: 1px solid #e5e7eb;
        }

        .row:first-child {
            border-top: none;
        }

        .label {
            font-weight: 700;
            color: #0f172a;
            min-width: 120px;
        }

        .value {
            color: #475569;
            word-break: break-word;
            text-align: right;
        }

        .message-box {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 18px;
            background: #ffffff;
            color: #334155;
            white-space: pre-wrap;
        }

        .footer {
            padding: 24px 36px 32px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .footer-text {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        @media only screen and (max-width: 600px) {
            .header,
            .body,
            .footer {
                padding-left: 22px !important;
                padding-right: 22px !important;
            }

            .row {
                display: block;
            }

            .value {
                text-align: left;
                margin-top: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="bar"></div>
            <div class="header">
                <h1>New contact message</h1>
                <p>A visitor submitted the contact form on <strong>{{ $pageTitle }}</strong>.</p>
            </div>

            <div class="body">
                <div class="panel">
                    <div class="row">
                        <div class="label">Name</div>
                        <div class="value">{{ $name }}</div>
                    </div>
                    <div class="row">
                        <div class="label">Email</div>
                        <div class="value"><a href="mailto:{{ $email }}">{{ $email }}</a></div>
                    </div>
                    <div class="row">
                        <div class="label">Page</div>
                        <div class="value">{{ $pageTitle }}</div>
                    </div>
                </div>

                <p style="font-weight: 700; color: #0f172a; margin-bottom: 10px;">Message</p>
                <div class="message-box">{!! nl2br(e($message)) !!}</div>

                <p style="margin-top: 24px;">
                    You can reply directly to this email or contact {{ $name }} at
                    <a href="mailto:{{ $email }}">{{ $email }}</a>.
                </p>
            </div>

            <div class="footer">
                <p class="footer-text">
                    Sent by {{ $siteName }} contact form.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
