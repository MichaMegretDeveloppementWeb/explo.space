# Sécurité & RGPD

- **Policies** pour actions sensibles (création/validation/modération).
- **reCAPTCHA v3** sur tous les formulaires publics (siteKey/secret via `.env`).
- CSRF/XSS, validation stricte (ne jamais faire confiance au client).
- Mots de passe hashés (Argon2/BCrypt), sessions sécurisées.
- Pages légales RGPD ; mails de contact/support/rgpd via `.env`.
- Ne jamais exposer de secrets ni dans les logs ni dans les erreurs renvoyées au client.
