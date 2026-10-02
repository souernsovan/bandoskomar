<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
</head>
<body style="margin:0;padding:32px 16px;background:#EEF1FA;font-family:Arial,Helvetica,sans-serif;color:#1A1F3A">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden">
    <tr><td style="background:#1E2A6B;color:#ffffff;padding:20px 24px;font-size:18px;font-weight:bold">{{ $title }}</td></tr>
    <tr><td style="padding:8px 24px 24px">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach ($fields as $label => $value)
        <tr>
          <td style="padding:12px 0 4px;font-size:13px;color:#5A6180;border-top:1px solid #DCE1F2">{{ $label }}</td>
        </tr>
        <tr>
          <td style="padding:0 0 12px;font-size:15px;line-height:1.6">{!! nl2br(e($value)) !!}</td>
        </tr>
        @endforeach
      </table>
      <p style="font-size:13px;color:#5A6180;margin:16px 0 0">Reply to this email to answer the sender directly.</p>
    </td></tr>
  </table>
</body>
</html>
