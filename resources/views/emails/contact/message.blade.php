@extends('emails.layouts.admin')

@section('title', 'Nouveau message de contact')

@section('content')
    <!-- Header -->
    <h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600; color: #1a202c; line-height: 1.3;">
        Nouveau message de contact
    </h2>

    <!-- Sender info -->
    <div style="background-color: #f7fafc; border-left: 4px solid #4299e1; padding: 16px 20px; border-radius: 4px; margin-bottom: 24px;">
        <p style="margin: 0 0 8px 0; font-size: 14px; color: #2d3748;">
            <strong>De :</strong> {{ $senderName }}
        </p>
        <p style="margin: 0 0 8px 0; font-size: 14px; color: #2d3748;">
            <strong>Email :</strong> <a href="mailto:{{ $senderEmail }}" style="color: #4299e1; text-decoration: none;">{{ $senderEmail }}</a>
        </p>
        <p style="margin: 0; font-size: 14px; color: #2d3748;">
            <strong>Sujet :</strong> {{ $messageSubject }}
        </p>
    </div>

    <!-- Message content -->
    <div style="background-color: #ffffff; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
        <p style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">
            Message
        </p>
        <div style="margin: 0; font-size: 15px; color: #2d3748; line-height: 1.6; white-space: pre-wrap;">{{ $messageContent }}</div>
    </div>

    <!-- Quick reply button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 8px 0 24px 0;">
                <a href="mailto:{{ $senderEmail }}" style="display: inline-block; background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; text-align: center; box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);">
                    Répondre au message
                </a>
            </td>
        </tr>
    </table>

    <!-- Footer note -->
    <p style="margin: 0; font-size: 13px; color: #718096; line-height: 1.6;">
        Ce message a été envoyé via le formulaire de contact de <strong>Explo.space</strong>.
    </p>
@endsection
