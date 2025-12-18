#!/bin/bash

# Pregled informacija o Our Packages stranici
# Ovaj script prikazuje sve relevantne informacije o stranici

MYSQL_CMD="/Applications/Local.app/Contents/Resources/extraResources/lightning-services/mysql-8.0.35+4/bin/darwin/bin/mysql"
SOCKET="/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock"
DB_USER="root"
DB_PASS="root"
DB_NAME="local"

echo "📄 INFORMACIJE O OUR PACKAGES STRANICI"
echo "======================================"
echo ""

echo "📋 Osnovno o stranici:"
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME -e "
SELECT 
    ID,
    post_title,
    post_name,
    post_status,
    post_modified
FROM wp_posts 
WHERE ID = 590;
" 2>/dev/null

echo ""
echo "🔧 Elementor meta podaci:"
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME -e "
SELECT 
    meta_key,
    LEFT(meta_value, 100) as meta_value_preview
FROM wp_postmeta 
WHERE post_id = 590 
AND meta_key LIKE '%elementor%'
ORDER BY meta_key;
" 2>/dev/null

echo ""
echo "📊 ACF meta podaci (ako postoje):"
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME -e "
SELECT 
    meta_key,
    meta_value
FROM wp_postmeta 
WHERE post_id = 590 
AND (meta_key LIKE '%acf%' OR meta_key = 'use_acf_template' OR meta_key = 'package_sections')
ORDER BY meta_key;
" 2>/dev/null

echo ""
echo "✨ Trenutna tema:"
$MYSQL_CMD -u $DB_USER -p$DB_PASS --socket="$SOCKET" -D $DB_NAME -e "
SELECT 
    option_name,
    option_value
FROM wp_options 
WHERE option_name IN ('template', 'stylesheet');
" 2>/dev/null
