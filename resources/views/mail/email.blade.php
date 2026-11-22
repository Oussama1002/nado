<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $data['subject'] ?? 'Demande expert NADO' }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f6f9;padding:24px 0;">
    <tr><td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
               style="width:600px;max-width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.05);">

            <tr>
                <td style="background:#0d6efd;padding:20px 28px;color:#fff;">
                    <div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">NADO · Nouvelle demande</div>
                    <div style="font-size:20px;font-weight:600;margin-top:4px;">{{ $data['subject'] ?? 'Demande expert NADO' }}</div>
                </td>
            </tr>

            <tr>
                <td style="padding:28px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:15px;line-height:1.55;">
                        <tr>
                            <td style="padding:6px 0;color:#6c757d;width:120px;">Nom</td>
                            <td style="padding:6px 0;font-weight:600;">{{ $data['name'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:#6c757d;">Téléphone</td>
                            <td style="padding:6px 0;">
                                <a href="tel:{{ $data['phone'] ?? '' }}" style="color:#0d6efd;text-decoration:none;font-weight:600;">
                                    {{ $data['phone'] ?? '—' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:#6c757d;">Email</td>
                            <td style="padding:6px 0;">
                                <a href="mailto:{{ $data['email'] ?? '' }}" style="color:#0d6efd;text-decoration:none;">
                                    {{ $data['email'] ?? '—' }}
                                </a>
                            </td>
                        </tr>
                        @if(!empty($data['ville']))
                        <tr>
                            <td style="padding:6px 0;color:#6c757d;">Ville</td>
                            <td style="padding:6px 0;">{{ $data['ville'] }}</td>
                        </tr>
                        @endif
                    </table>

                    @if(!empty($data['message']))
                    <div style="margin-top:20px;padding:16px;background:#f8f9fa;border-left:3px solid #0d6efd;border-radius:6px;white-space:pre-wrap;">{{ $data['message'] }}</div>
                    @endif

                    <div style="margin-top:24px;">
                        <a href="https://www.nado.ma/admin/demande-experts"
                           style="display:inline-block;background:#0d6efd;color:#fff;text-decoration:none;padding:10px 20px;border-radius:6px;font-weight:600;font-size:14px;">
                            Voir dans l'admin
                        </a>
                    </div>
                </td>
            </tr>

            <tr>
                <td style="padding:16px 28px;background:#f8f9fa;color:#6c757d;font-size:12px;text-align:center;border-top:1px solid #e9ecef;">
                    Message automatique — <a href="https://www.nado.ma" style="color:#0d6efd;text-decoration:none;">www.nado.ma</a>
                </td>
            </tr>

        </table>
    </td></tr>
</table>
</body>
</html>
