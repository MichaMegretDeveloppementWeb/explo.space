# Design System - COSMAP (explo.space)

## Vue d'ensemble

Système de design basé sur **deux approches distinctes** selon le type de page, inspiré des standards SaaS modernes et des produits Google.

## 1. Pages Interface Utilisateur (Dashboard/Admin/Login)

### Inspiration
- **Stripe Dashboard** : minimalisme, hiérarchie claire
- **Laravel Cloud/Forge** : interface épurée, design system cohérent  
- **Google Tag Manager** : fonctionnalité avant esthétique
- **Templates Tailwind CSS** : composants système standardisés

### Principes de design
- **Minimalisme fonctionnel** : chaque élément a un but précis
- **Hiérarchie visuelle claire** : titres, sous-titres, contenu
- **Consistance des composants** : boutons, forms, cards standardisés
- **Couleurs neutres dominantes** : blanc, gris, accent coloré minimal
- **Typography** : lisibilité maximale, tailles cohérentes
- **Espacement** : grilles régulières, breathing room

### Palette couleurs
- **Primaire** : Bleu spatial (#1E40AF ou similaire)
- **Neutres** : Échelle de gris (#F8FAFC à #1E293B)
- **Success** : Vert (#10B981)
- **Warning** : Orange (#F59E0B)
- **Danger** : Rouge (#EF4444)

## 2. Pages Présentation/Marketing (Landing/Fonctionnalités/Communauté)

### Inspiration
- **Google Ads** : sections claires, CTA évidents
- **Google My Business** : mix content + visuels
- **Produits Google** : approche pédagogique, progressive disclosure

### Éléments de composition

#### A. Texte explicatif
- **Titres accrocheurs** : bénéfices utilisateur
- **Sous-titres pédagogiques** : comment ça marche
- **Paragraphes concis** : valeur ajoutée claire
- **CTA évidents** : "Découvrir", "Commencer", "Explorer"

#### B. Images illustratives

Si besoin d'ajouter une image, réserver l'espace avec un element grisé au bonne proportions avec un commentaire au dessus indiquant l'image souhaitée. Voici des exemples :

**Exemples d'images à réserver :**

1. **Décoratives spatiales** :
   ```html
   <!-- Image : Vue spectaculaire de la Terre depuis l'ISS, 1200x600px -->
   <div class="bg-gray-200 w-full h-96 rounded-lg"></div>
   ```

2. **Captures d'interface** :
   ```html
   <!-- Image : Capture écran carte interactive avec pins lieux spatiaux, 800x500px -->
   <div class="bg-gray-200 w-full h-80 rounded-lg"></div>
   ```

3. **Photos lieux emblématiques** :
   ```html
   <!-- Image : Photo Kennedy Space Center avec fusée, 600x400px -->
   <div class="bg-gray-200 w-full h-64 rounded-lg"></div>
   ```
   
Ce ne sont que des exmples il faudra bien sûr adapter.

#### C. Mockups "Skeleton Amélioré"

**Principe** : Contexte en gris (skeleton) + fonctionnalité mise en avant (colorée/détaillée)

**Exemple de mockup style skeleton amélioré** :
```html
<div class="order-2 lg:order-1">
    <div class="relative mx-auto w-[85%] max-w-md">
        {{-- Interface mockup --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
            {{-- Client rows in skeleton --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-24 rounded bg-gray-200"></div>
                            <div class="h-2 w-16 rounded bg-gray-100"></div>
                        </div>
                    </div>
                    <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                </div>

                {{-- Featured row with dropdown --}}
                <div class="relative flex items-center justify-between rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-blue-200"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-24 rounded bg-blue-300"></div>
                            <div class="h-2 w-16 rounded bg-blue-200"></div>
                        </div>
                    </div>
                    {{-- Dropdown menu (detailed) --}}
                    <div class="absolute right-4 top-16 z-10 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                        <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <x-heroicon-o-eye class="mr-3 h-4 w-4 text-gray-400" />
                            Voir le détail
                        </button>
                        <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <x-heroicon-o-pencil class="mr-3 h-4 w-4 text-gray-400" />
                            Modifier
                        </button>
                        <button class="flex w-full items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                            <x-heroicon-o-plus class="mr-3 h-4 w-4 text-blue-500" />
                            Nouveau projet
                        </button>
                        <button class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <x-heroicon-o-trash class="mr-3 h-4 w-4 text-red-500" />
                            Supprimer
                        </button>
                    </div>
                    {{-- Cursor --}}
                    <div class="absolute right-8 top-12">
                        <x-heroicon-o-cursor-arrow-rays class="h-5 w-5 text-blue-600" />
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-24 rounded bg-gray-200"></div>
                            <div class="h-2 w-16 rounded bg-gray-100"></div>
                        </div>
                    </div>
                    <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Usages recommandés** :
- Recherche géographique avec rayon
- Système de tags/filtres  
- Workflow validation admin
- Carrousel photos lieux
- Interface mobile responsive

## 3. Gestion des images

### Réservation d'espaces
```html
<!-- Format standard pour réserver espace image -->
<!-- Image : [Description précise] + [Dimensions] + [Usage] -->
<div class="bg-gray-200 w-[width] h-[height] rounded-lg flex items-center justify-center">
  <span class="text-gray-500 text-sm"></span>
</div>
```

### Exemples d'images par page

#### Landing Page
- **Hero** : Vue spatiale époustouflante (1920x800px)
- **Features** : Captures interface utilisateur (800x500px)
- **Testimonials** : Photos lieux emblématiques (300x200px)

#### Page Fonctionnalités  
- **Recherche** : Mockup skeleton carte + pins (1000x600px)
- **Modération** : Workflow admin en action (800x400px)
- **Mobile** : Interface responsive (375x667px)

#### Page Communauté
- **Galerie** : Mosaïque lieux spatiaux (grid responsive)
- **Contributeurs** : Avatars + stats (variées)

## 4. Composants système standardisés

### Boutons
- **Primary** : Fond coloré, texte blanc
- **Secondary** : Bordure colorée, fond transparent  
- **Ghost** : Texte coloré, hover subtil

### Cards/Panels
- **Shadow** : Subtle, consistent
- **Borders** : Rayon uniforme (8px standard)
- **Padding** : Multiples de 16px

### Forms
- **Inputs** : Bordure neutre, focus coloré
- **Labels** : Au-dessus, poids medium
- **Validation** : États error/success clairs

## 5. Responsive & Grilles

### Breakpoints (Tailwind standard)
- **Mobile** : < 768px
- **Tablet** : 768px - 1024px  
- **Desktop** : > 1024px

### Grilles
- **Mobile** : 1 colonne + padding latéral
- **Tablet** : 2-3 colonnes selon contenu
- **Desktop** : 3-4 colonnes maximum

## 6. Animation & Interactions

### Micro-interactions
- **Hover** : Transitions 150ms ease
- **Loading** : Skeleton loaders cohérents
- **CTA** : Scale/shadow subtils

### Animations lourdes
- **Éviter** : Animations complexes inutiles
- **Privilégier** : Feedback utilisateur utile

## 7. Accessibilité

### Contraste
- **Minimum** : 4.5:1 pour texte normal
- **Titre** : 3:1 minimum acceptable

### Navigation
- **Focus** : Outline visible, logique
- **Alt text** : Images informatives uniquement
- **Sémantique** : HTML structuré (h1, h2, nav...)

---

**Objectif** : Base solide développement + cohérence visuelle professionnelle inspirée des leaders du marché (Google/Stripe/Laravel).
