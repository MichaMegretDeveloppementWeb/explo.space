@extends('emails.layouts.admin')

@section('title', 'Vérification de votre adresse email')

@section('content')
    <!-- Greeting -->
    <h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600; color: #1a202c; line-height: 1.3;">
        Bonjour {{ $userName }},
    </h2>

    <!-- Main message -->
    <p style="margin: 0 0 24px 0; font-size: 15px; color: #4a5568; line-height: 1.6;">
        Veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous. Cette étape est nécessaire pour accéder à votre espace d'administration Explo.space.
    </p>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 8px 0 32px 0;">
                <a href="{{ $verificationUrl }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600; text-align: center; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); transition: all 0.2s;">
                    Vérifier mon adresse email
                </a>
            </td>
        </tr>
    </table>

    <!-- Info box -->
    <div style="background-color: #fffaf0; border-left: 4px solid #f6ad55; padding: 16px 20px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #744210; line-height: 1.6;">
            <strong style="color: #744210;">⏱️ Lien temporaire :</strong> Ce lien de vérification expire dans 60 minutes pour des raisons de sécurité.
        </p>
    </div>

    <!-- Help text -->
    <p style="margin: 0 0 24px 0; font-size: 14px; color: #718096; line-height: 1.6;">
        Si vous n'avez pas créé de compte ou modifié votre email sur Explo.space, vous pouvez ignorer cet email en toute sécurité.
    </p>

    <!-- Alternative link -->
    <p style="margin: 0 0 8px 0; font-size: 13px; color: #718096; line-height: 1.6;">
        Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
    </p>
    <p style="margin: 0; font-size: 12px; color: #a0aec0; word-break: break-all; line-height: 1.6;">
        {{ $verificationUrl }}
    </p>
@endsection
