# Build & Deployment Guide

## Lokalna Instalacija & Build

### 1. Instalacija Node Dependencies

```bash
cd /Volumes/Data/Websites/medicorner/app/public/wp-content/themes/hello-elementor-child
npm install
```

### 2. Razvojni Build

```bash
# Watch mode - automatski rebuild na svaku promenu
npm start

# Ili koristi webpack direktno
npx webpack --watch --mode development
```

### 3. Production Build

```bash
# Minifikovani build za production
npm run build

# Ili sa webpack
npx webpack --mode production
```

## Webpack Output

Webpack kompajlira:
- `assets/js/main.js` → `dist/main.js`
- `assets/js/editor.js` → `dist/editor.js`
- `assets/scss/main.scss` → `dist/main.css`

## CSS Pipeline

SCSS → Webpack → PostCSS (autoprefixer) → CSSNANO (minify) → dist/main.css

## JS Pipeline

JS (ES6) → Babel (transpile) → Webpack → dist/main.js

## Git Workflow

```bash
# 1. Prvo build-uj
npm run build

# 2. Commit source files
git add assets/scss assets/js functions.php inc/

# 3. Commit compiled assets
git add dist/

git commit -m "Update theme styles and scripts"
```

## WordPress Enqueue

U `functions.php`, WordPress učitava iz `dist/`:

```php
wp_enqueue_style('hello-elementor-child-main', 
    get_stylesheet_directory_uri() . '/dist/main.css'
);

wp_enqueue_script('hello-elementor-child-main',
    get_stylesheet_directory_uri() . '/dist/main.js'
);
```

## Troubleshooting

### CSS nije primenjena

```bash
# Rebuildiraj CSS
npm run build

# Očisti WordPress cache
# Settings > General > Scroll do "Site Title" i save
```

### JS ne radi

```bash
# Proverite console za greške
# Dev tools: F12 > Console

# Rebuildiraj JS
npm run build
```

### Česti problemi

1. **node_modules nedostaje**
   ```bash
   npm install
   ```

2. **Webpack se ne pokreće**
   ```bash
   npm install
   npx webpack --version
   ```

3. **Permission denied**
   ```bash
   sudo chmod -R 755 node_modules/
   ```

## Kontinuirana Integracija

Za CI/CD pipeline-ove:

```bash
# Build pre deployovanja
npm run build

# Lint pre commita
npm run lint:scss
npm run lint:js
```

---

**Last Updated**: 18 Dec 2025
