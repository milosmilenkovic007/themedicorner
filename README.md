# Hello Elementor Child Theme

Child tema za Medicorner sajt sa podrškom za ACF (Advanced Custom Fields) i flexible content module.

## Instalacija

### 1. Node.js Dependencies

```bash
cd /Volumes/Data/Websites/medicorner/app/public/wp-content/themes/hello-elementor-child
npm install
```

### 2. Build Assets

```bash
# Production build
npm run build

# Development watch mode
npm start
```

### 3. Aktivirajte child temu

- Idite na `Appearance > Themes` u WordPress admin panelu
- Pronađite "Hello Elementor Child" temu
- Kliknite "Activate"

### 4. Instalirajte ACF Pro plugin (ako nije instaliran)

- ACF je potreban za rad sa flexible content modulima
- Download: https://www.advancedcustomfields.com/pro/
- Ili instalirajte preko `Plugins > Add New`

## Folder Struktura

```
hello-elementor-child/
├── assets/
│   ├── js/
│   │   ├── main.js                 # Frontend entry point
│   │   ├── editor.js               # Admin/editor entry point
│   │   └── frontend/               # Frontend modules
│   ├── scss/
│   │   ├── main.scss               # Main SCSS entry point
│   │   ├── abstracts/
│   │   │   ├── _variables.scss     # Design tokens
│   │   │   ├── _mixins.scss        # SCSS mixins
│   │   │   ├── _functions.scss     # SCSS functions
│   │   │   └── _utilities.scss     # Utility classes
│   │   ├── base/
│   │   │   ├── _reset.scss         # Reset/normalize
│   │   │   └── _typography.scss    # Typography styles
│   │   ├── layout/
│   │   │   ├── _grid.scss          # Grid system
│   │   │   └── _container.scss     # Container
│   │   ├── components/
│   │   │   ├── _buttons.scss       # Button component
│   │   │   ├── _forms.scss         # Form elements
│   │   │   ├── _cards.scss         # Card component
│   │   │   └── _modals.scss        # Modal component
│   │   ├── modules/                # ACF Flexible modules
│   │   │   ├── _hero-section.scss
│   │   │   ├── _text-image.scss
│   │   │   ├── _features.scss
│   │   │   ├── _testimonials.scss
│   │   │   ├── _packages.scss
│   │   │   └── ...
│   │   └── pages/
│   │       ├── _homepage.scss
│   │       └── _packages.scss
│   └── css/                        # Legacy CSS
├── dist/                           # Compiled assets (auto-generated)
│   ├── main.js
│   ├── main.css
│   └── editor.js
├── inc/
│   ├── modules/                    # ACF Flexible content modules
│   │   ├── hero-section/
│   │   │   ├── config.php          # ACF field configuration
│   │   │   └── render.php          # Template render
│   │   ├── text-image/
│   │   ├── features/
│   │   ├── testimonials/
│   │   ├── cta-block/
│   │   └── ...
│   ├── acf-fields.php              # Main ACF fields registration
│   └── acf-flexible-layouts.php    # Flexible content functions
├── page-templates/
│   └── packages-acf.php            # Custom page template
├── scripts/
│   ├── analyze-elementor.php       # Elementor analysis tools
│   └── analyze-elementor.sh
├── functions.php                   # Theme functions
├── style.css                       # Theme header
├── package.json                    # NPM dependencies
├── webpack.config.js               # Webpack configuration
├── .babelrc                        # Babel configuration
├── postcss.config.js               # PostCSS configuration
└── README.md                       # Ova datoteka
```

## Razvojna Procesa

### Build System

Koristi **Webpack** za bundling i kompajliranje asset-a.

```bash
# Watch mode (automatski prebuild na promene)
npm start

# Production build (minimalizovan)
npm run build

# Development server
npm run dev
```

### SCSS Architecture

Koristi **SMACSS** pristup sa sledećim slojevima:

1. **Abstracts** - Varijable, mixins, funkcije
2. **Base** - Reset, typography, globalni stilovi
3. **Layout** - Grid, container, major layout komponente
4. **Components** - Buttons, forms, cards, modals
5. **Modules** - ACF flexible content modules
6. **Pages** - Page-specific styles
7. **Utilities** - Helper classes

### Dodavanje Novog ACF Modula

1. Kreiranja folder: `inc/modules/module-name/`
2. Kreiranja `config.php` - ACF field konfiguracija
3. Kreiranja `render.php` - Template za prikaz
4. Kreiranja `_module-name.scss` u `assets/scss/modules/`

Primer strukture `config.php`:

```php
<?php
if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_flexible_module_name',
        'title' => 'Module Name',
        'fields' => array(
            // Field definitions
        ),
        // ... config
    ));
}
```

Primer strukture `render.php`:

```php
<?php
$title = $layout['title'] ?? '';
$content = $layout['content'] ?? '';
?>

<section class="module-name">
    <div class="container">
        <?php if ($title) : ?>
            <h2><?php echo wp_kses_post($title); ?></h2>
        <?php endif; ?>
    </div>
</section>
```

## ACF Flexible Content Layouts

Dostupni moduli:

- **Hero Section** - Full-width hero sa background, overlay, title i CTA buttons
- **Text Image** - Tekst sa slikom u dva reda
- **Features** - Feature grid ili lista
- **Testimonials** - Testimonijali / reviews
- **CTA Block** - Call-to-action sekcija
- **Accordion** - Collapsible content
- **Gallery** - Image gallery
- **Packages** - Package/pricing cards
- ...i više!

## Prenosive stranica sa Elementora na ACF

### Korak 1: Proverite sadržaj

```bash
npm run analyze
```

Ovo će analzirati sve Elementor module na sajtu.

### Korak 2: Kreiranje modula

Kreirajte ACF module za sve različite sekcije na stranici.

### Korak 3: Migracija

1. Na stranici, omogućite "Use ACF Template"
2. Popunite ACF flexible content polja sa sadržajem
3. Testirajte da sve radi kako treba
4. Obriške Elementor sadržaj kada ste sigurni

## CSS Prilagođava Elementoru

Sve custom CSS iz Elementora biće prenosivo u SCSS module:

```bash
npm run analyze
```

## NPM Scripts

```bash
npm start          # Watch mode
npm run build      # Production build
npm run dev        # Dev server
npm run lint:scss  # Lint SCSS
npm run lint:js    # Lint i fix JavaScript
npm run format     # Format code sa Prettier
npm run analyze    # Analiza Elementor modula
```

## Varijable za Konfiguraciju

Edit: `assets/scss/abstracts/_variables.scss`

- **Boje**: Primary, Secondary, Accent, Gray scale
- **Tipografija**: Font families, sizes, weights
- **Razmaci**: Spacing scale
- **Breakpoints**: Responsive breakpoints
- **Senke**: Shadow levels
- **Zaobljenost**: Border radius values

## Performance

- Webpack uglify minimization za JS
- CSSNANO minification za CSS
- PostCSS autoprefixer za browser compatibility
- Tree-shaking za nekorišćeni kod

## Kompatibilnost

- WordPress 6.0+
- PHP 8.0+
- Node.js 18.0+
- Moderna browsers (Chrome, Firefox, Safari, Edge)

## Podrška

Za dodatne upite ili implementaciju, pozovite dev tima.

---

**Verzija**: 1.0.0  
**Zadnja Ažuriranja**: 18 Dec 2025  
**Build System**: Webpack 5 + SCSS + Babel
