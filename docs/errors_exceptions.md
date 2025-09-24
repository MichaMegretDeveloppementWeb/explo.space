# Validation, erreurs, exceptions, réponses

## Validation
- Form Requests dédiées ; messages clairs côté utilisateur ; intégration Livewire propre.

## Exceptions métier
- Lever des exceptions métier (ex. `PlaceRequestAlreadyProcessedException`) dans les Services.
- Mapping centralisé vers codes HTTP adaptés (404, 409, 422…).

## Journalisation & alertes
- Monolog channel `daily`.
- Email d’alerte **uniquement** sur erreurs **critiques (500)** (adresses via `.env`).

## Réponses UI
- Messages lisibles, sans fuite d’infos sensibles.
- Structure d’erreurs uniforme pour les formulaires.
