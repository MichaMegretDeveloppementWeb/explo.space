{{-- Nom --}}
<x-admin.form.input
    label="Nom"
    name="name"
    wire:model.live.debounce.500ms="name"
    :required="true"
    placeholder="Ex: Fusées"
    :error="$errors->first('name')"
/>

{{-- Slug --}}
<x-admin.form.input
    label="Slug"
    name="slug"
    wire:model.live.debounce.500ms="slug"
    :required="true"
    placeholder="fusees"
    helperText="Le slug est généré automatiquement depuis le nom. Utilisez uniquement des lettres minuscules, chiffres et tirets."
    :error="$errors->first('slug')"
/>

{{-- Description --}}
<x-admin.form.textarea
    label="Description"
    name="description"
    wire:model="description"
    rows="4"
    placeholder="Description interne de la catégorie (optionnel)"
    helperText="{{ strlen($description ?? '') }}/2000 caractères"
    :error="$errors->first('description')"
/>
