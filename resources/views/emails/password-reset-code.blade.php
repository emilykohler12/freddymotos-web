<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0; padding:32px 16px; background:#F5F5F5; font-family: Arial, Helvetica, sans-serif; color:#111111;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:420px;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-bottom:20px; text-align:center; font-size:18px; font-weight:bold;">
                            {{ $nombreLocal ?? 'Freddy Motos' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff; border-radius:16px; padding:28px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:14px; color:#555555;">Usá este código para recuperar tu contraseña:</p>
                            <p style="margin:0 0 8px; font-size:32px; font-weight:bold; letter-spacing:8px;">{{ $code }}</p>
                            <p style="margin:16px 0 0; font-size:13px; color:#888888;">Vence en {{ $minutes }} minutos. Si no pediste este código, podés ignorar este correo.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
