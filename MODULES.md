# ACF Flexible Content Modules

Sve dostupne ACF flexible content module za stranicu.

## Dostupni Moduli

### 1. Hero Section
**Folder**: `inc/modules/hero-section/`

Sekcija sa pozadinom, tekstom i CTA dugmima.

**Polja**:
- Title (text)
- Subtitle (textarea)
- Background Image (image)
- Height (select: auto, small, medium, large, full)
- Overlay Opacity (slider: 0-100)
- Buttons (repeater):
  - Button Text (text)
  - Button Link (url)
  - Button Style (select: primary, secondary, white)

**Stilovi**: `assets/scss/modules/_hero-section.scss`
**Template**: `inc/modules/hero-section/render.php`

---

### 2. Text Image
**Folder**: `inc/modules/text-image/` (u razvoju)

Tekst sa slikom, može biti tekst levo/desno.

**Polja**:
- Title (text)
- Description (wysiwyg)
- Image (image)
- Layout (select: left, right)
- Button Link (url, opciono)

---

### 3. Features
**Folder**: `inc/modules/features/` (u razvoju)

Grid od feature-a sa ikonicama.

**Polja**:
- Title (text)
- Features (repeater):
  - Icon (select ili upload)
  - Feature Title (text)
  - Description (text)

---

### 4. Testimonials
**Folder**: `inc/modules/testimonials/` (u razvoju)

Carousel od testimonijala.

**Polja**:
- Title (text)
- Testimonials (repeater):
  - Quote (textarea)
  - Author (text)
  - Author Role (text)
  - Author Avatar (image)
  - Rating (select: 1-5)

---

### 5. CTA Block
**Folder**: `inc/modules/cta-block/` (u razvoju)

Call-to-action sekcija sa boji i dugmima.

**Polja**:
- Title (text)
- Description (wysiwyg)
- Background Color (color)
- Buttons (repeater)

---

### 6. Accordion
**Folder**: `inc/modules/accordion/` (u razvoju)

Collapsible sekcije.

**Polja**:
- Title (text)
- Items (repeater):
  - Item Title (text)
  - Item Content (wysiwyg)

---

### 7. Gallery
**Folder**: `inc/modules/gallery/` (u razvoju)

Image gallery sa filter opcijama.

**Polja**:
- Title (text)
- Gallery Images (gallery)
- Columns (select: 2, 3, 4)
- Enable Filter (true/false)

---

### 8. Packages
**Folder**: `inc/modules/packages/`

Package/pricing cards - već implementirano za Our Packages stranicu.

**Polja**:
- Package Sections (repeater):
  - Section Title (text)
  - Section Description (wysiwyg)
  - Packages (repeater):
    - Package Name (text)
    - Package Price (text)
    - Package Features (wysiwyg)
    - Button Text (text)
    - Button Link (url)
    - Is Featured (true/false)

---

## Kako Kreirati Novi Modul

### Korak 1: Kreirajte Folder

```bash
mkdir inc/modules/module-name
cd inc/modules/module-name
```

### Korak 2: Kreirajte config.php

```php
<?php
if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( array(
        'key' => 'group_flexible_module_name',
        'title' => 'Module Display Name',
        'fields' => array(
            array(
                'key' => 'field_module_title',
                'label' => 'Title',
                'name' => 'title',
                'type' => 'text',
            ),
            // Dodaj više polja...
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'active' => false, // Koristi se kao flexible layout
    ));
}
```

### Korak 3: Kreirajte render.php

```php
<?php
$title = $layout['title'] ?? '';
?>

<section class="module-name">
    <div class="container">
        <?php if ($title) : ?>
            <h2><?php echo wp_kses_post($title); ?></h2>
        <?php endif; ?>
    </div>
</section>
```

### Korak 4: Kreirajte SCSS

```scss
// assets/scss/modules/_module-name.scss

.module-name {
  // Stilovi ovde
}
```

### Korak 5: Dodajte Import u main.scss

```scss
// assets/scss/main.scss
@import 'modules/module-name';
```

### Korak 6: Build

```bash
npm run build
```

---

## Flexible Content Field Setup

Fleksibilni sadržaj se primenjuje na stranicama kroz polje:

```php
array(
    'key' => 'field_page_content',
    'label' => 'Page Content',
    'name' => 'page_content',
    'type' => 'flexible_content',
    'layouts' => array(
        // Svaki layout bira se iz dostupnih modula
        'hero_section',
        'text_image',
        'features',
        'testimonials',
        // ... itd
    ),
)
```

---

## Renderiranje na Stranici

U template-ama:

```php
<?php
if ( have_rows( 'page_content' ) ) :
    while ( have_rows( 'page_content' ) ) : the_row();
        $layout = get_row();
        hello_child_render_flexible_layout( $layout );
    endwhile;
endif;
?>
```

---

## Stilizacija Modula

Svaki modul ima pristup globalnim SCSS varijablama:

```scss
.module-name {
  color: $primary-color;
  padding: $spacing-lg;
  font-size: $font-size-base;
  
  @include respond-to(md) {
    padding: $spacing-2xl;
  }
}
```

Dostupne varijable vidi u: `assets/scss/abstracts/_variables.scss`

---

## Testing Modula

1. Kreirajte test stranicu
2. Dodajte modul u flexible content
3. Popunite polja
4. Testirajte na frontend-u

---

**Last Updated**: 18 Dec 2025
