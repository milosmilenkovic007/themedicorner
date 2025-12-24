# Single Package Template

## Overview

Profesionalan template za prikaz pojedinog package-a (CPT). Template automatski prikazuje sve dostupne podatke iz Package post type-a sa podruškom za ACF flexible layouts.

**Lokacija**: `single-package.php`
**Stilovi**: `assets/scss/pages/_single-package.scss`

---

## Sekcije Templatea

### 1. **Package Hero Section**
Prikazuje:
- Featured image (ako postoji)
- Naslov package-a
- Short description
- Cena (sa € simbolom)
- CTA dugme za scroll na detaljima

**CSS klase**: `.package-hero`, `.package-hero__*`

### 2. **Package Content**
Prikazuje main editor sadržaj iz WordPress editora.

**CSS klase**: `.package-content`, `.package-content__inner`

### 3. **Package Sections**
Prikazuje ACF `include_sections` polje sa:
- Naslovom (2 reda - title_line_1 i title_line_2)
- Listom stavki sa checkmark ikonama

Prikazuje se u grid layout-u (3 kolone na desktop, 1 na mobile).

**CSS klase**: `.package-sections`, `.package-section`, `.package-section__*`

### 4. **ACF Flexible Modules**
Ako paket ima `page_modules` flexible content polje, prikazuje sve module.

Primer: Hero sekcija, CTA blok, testimonijali, itd.

**CSS klase**: `.package-modules`

### 5. **Package CTA Footer**
Poziva korisnika na akciju sa:
- Naslovom
- Opisom
- "Book Now" dugmom

**CSS klase**: `.package-cta-footer`, `.package-cta-footer__*`

### 6. **Related Packages**
Prikazuje 3 random package-a (osim trenutnog) sa:
- Slike
- Naslov
- Short description
- Cena
- "View Details" link

Može se konfigurirati u kodu.

**CSS klase**: `.package-related`, `.package-card`, `.package-card__*`

---

## ACF Polja Koja Se Koriste

### Package CPT Polja:
- `price` (number) - Prikazuje se u hero sekciji
- `short_description` (textarea) - Prikazuje se u hero i related cards
- `include_sections` (repeater) - Prikazuje sekcije sa stavkama
- `page_modules` (flexible_content) - ACF flexible layouts
- Featured Image - Prikazuje se u hero sekciji

---

## CSS Breakpoints

```scss
// Responsive dizajn:
- Desktop: > 968px
- Tablet: 768px - 968px
- Mobile: < 768px
- Extra small: < 480px
```

---

## Kako Koristiti

### 1. **Kreiranja Package-a**
U WordPress admin:
1. Idi u Packages → Add New
2. Popuni:
   - Naslov
   - Featured image
   - Price (ACF polje)
   - Short description (ACF polje)
   - Content (main editor)
   - Include sections (ACF repeater)
   - Page modules (ACF flexible content) - opciono

3. Publish

### 2. **Prikazivanja na Frontend-u**
Template će biti automatski korišćen za sve package post type-a.
URL: `/packages/package-name/`

### 3. **Prilagođavanje Sadržaja**
Svi tekstovi i stilovi se mogu menjati kroz:
- ACF polja (sadržaj)
- SCSS fajl (dizajn)
- PHP template (struktura)

---

## Prilagođavanje

### Promena CTA Dugmeta
U `single-package.php`, red ~30:
```php
<a href="#package-sections" class="package-hero__cta btn btn--primary">
    View Full Details
</a>
```

### Promena "Book Now" dugmeta
Kod `single-package.php`, red ~80:
```php
<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--primary btn--lg">
    Book Now
</a>
```

### Promena Broja Related Packages
`single-package.php`, red ~130:
```php
'posts_per_page' => 3,  // Promeni na broj
```

### Promena Boja i Stilova
`assets/scss/pages/_single-package.scss`:
```scss
// Promeni ove vrednosti:
$hero-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
$accent-color: #667eea;
$success-color: #48bb78;
```

---

## Performance Napomene

- Template koristi `wp_get_attachment_image_url()` za optimizaciju slika
- Related packages koriste `WP_Query` sa `post__not_in` za efikasnu queriju
- Flexible layouts se renderuju preko dedicirane funkcije `hello_child_render_flexible_layout()`

---

## Kompatibilnost

- ✅ WordPress 5.9+
- ✅ ACF Pro
- ✅ Hello Elementor Theme
- ✅ All modern browsers

---

## Troubleshooting

### Sekcije se ne prikazuju
Proverite da li ACF `include_sections` polje ima podatke:
```php
<?php 
$sections = get_field( 'include_sections', get_the_ID() );
echo '<pre>' . print_r( $sections, true ) . '</pre>';
?>
```

### Flexible modules se ne prikazuju
Proverite:
1. Da li `page_modules` polje ima podatke
2. Da li postoji `hello_child_render_flexible_layout()` funkcija
3. Check `inc/acf-flexible-layouts.php` da vidiš dostupne module

### Stilovi se ne učitavaju
1. Rebuild SCSS: `npm run build`
2. Obriši WordPress cache
3. Proverite da je `_single-package.scss` importan u `main.scss`

---

**Last Updated**: 24 Dec 2025
