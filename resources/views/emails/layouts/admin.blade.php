<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Explo.space')</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f6f9fc;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f6f9fc;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                    <!-- Header -->
                    <tr>
                        <td style="padding-bottom: 32px; text-align: center;">
                            <div style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 48px; height: 48px; border-radius: 12px; position: relative;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="white" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                                </svg>
                            </div>
                            <h1 style="margin: 16px 0 0 0; font-size: 24px; font-weight: 600; color: #1a202c; letter-spacing: -0.025em;">
                                Explo.space
                            </h1>
                        </td>
                    </tr>

                    <!-- Content Card -->
                    <tr>
                        <td>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                <tr>
                                    <td style="padding: 40px 32px;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top: 32px; text-align: center;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #8492a6; line-height: 1.6;">
                                © {{ date('Y') }} Explo.space. Tous droits réservés.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #a0aec0;">
                                Vous recevez cet email car vous êtes administrateur sur Explo.space.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
