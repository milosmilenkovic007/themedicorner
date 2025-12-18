#!/usr/bin/env python3
"""
Analyzer for Elementor modules and custom CSS
Pronalazi sve widget tipove i CSS iz Elementor JSON meta podataka
"""

import json
import mysql.connector
import sys
import re
from collections import defaultdict
from pathlib import Path

# Database connection
config = {
    'user': 'root',
    'password': 'root',
    'host': '127.0.0.1',
    'database': 'local',
    'unix_socket': '/Users/user/Library/Application Support/Local/run/skejlzMMw/mysql/mysqld.sock',
    'raise_on_warnings': False,
}

def connect_db():
    """Konektuj se na MySQL bazu"""
    try:
        conn = mysql.connector.connect(**config)
        return conn
    except mysql.connector.Error as err:
        print(f"❌ Greška pri konektovanju: {err}")
        sys.exit(1)

def extract_widgets_from_json(json_str):
    """Ekstraktuj sve widgete i custom CSS iz Elementor JSON"""
    widgets = defaultdict(int)
    custom_css = []
    
    try:
        data = json.loads(json_str)
        traverse_elements(data, widgets, custom_css)
    except json.JSONDecodeError as e:
        print(f"⚠️  JSON parse error: {e}")
    
    return widgets, custom_css

def traverse_elements(elements, widgets, custom_css):
    """Rekurzivno pretražuj element stablo"""
    if not isinstance(elements, list):
        elements = [elements]
    
    for element in elements:
        if not isinstance(element, dict):
            continue
        
        # Pronađi widget tip
        widget_type = element.get('widgetType')
        if widget_type:
            widgets[widget_type] += 1
        
        # Pronađi custom CSS
        settings = element.get('settings', {})
        if 'custom_css' in settings:
            css = settings['custom_css']
            if css and css not in custom_css:
                custom_css.append(css)
        
        # Rekurzivno pretražuj child elementi
        if 'elements' in element:
            traverse_elements(element['elements'], widgets, custom_css)

def get_all_widgets(conn):
    """Preuzmi sve Elementor module sa svih stranica"""
    cursor = conn.cursor(dictionary=True)
    
    query = """
        SELECT 
            p.ID as page_id,
            p.post_title as page_title,
            p.post_name as page_slug,
            pm.meta_value as elementor_data
        FROM wp_posts p
        INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
        WHERE pm.meta_key = '_elementor_data'
        AND p.post_type = 'page'
        AND p.post_status = 'publish'
        ORDER BY p.post_title
    """
    
    cursor.execute(query)
    return cursor.fetchall()

def main():
    print("\n🔍 Analiza Elementor modula i CSS-a...\n")
    
    conn = connect_db()
    
    all_pages_widgets = defaultdict(int)
    pages_custom_css = {}
    
    rows = get_all_widgets(conn)
    
    for row in rows:
        page_id = row['page_id']
        page_title = row['page_title']
        elementor_data = row['elementor_data']
        
        widgets, custom_css = extract_widgets_from_json(elementor_data)
        
        # Agreguj sve widgete
        for widget, count in widgets.items():
            all_pages_widgets[widget] += count
        
        # Sačuvaj custom CSS po stranici
        if custom_css:
            pages_custom_css[page_title] = custom_css
        
        if widgets:
            print(f"📄 {page_title} (ID: {page_id})")
            for widget, count in sorted(widgets.items()):
                print(f"   └─ {widget}: {count}")
    
    # Rezime
    print("\n" + "="*60)
    print("📊 REZIME - SVI WIDGET TIPOVI")
    print("="*60)
    for widget, count in sorted(all_pages_widgets.items(), key=lambda x: x[1], reverse=True):
        print(f"  {widget}: {count}")
    
    if pages_custom_css:
        print("\n" + "="*60)
        print("🎨 CUSTOM CSS")
        print("="*60)
        for page_title, css_list in pages_custom_css.items():
            print(f"\n📄 {page_title}:")
            for i, css in enumerate(css_list, 1):
                print(f"   [{i}] {css[:100]}...")
    
    conn.close()
    
    # Sačuvaj u JSON fajl
    report = {
        'total_pages': len(rows),
        'widgets': dict(sorted(all_pages_widgets.items(), key=lambda x: x[1], reverse=True)),
        'pages_custom_css': pages_custom_css
    }
    
    output_file = Path(__file__).parent / 'elementor-analysis.json'
    with open(output_file, 'w') as f:
        json.dump(report, f, indent=2)
    
    print(f"\n✅ Analiza sačuvana u: {output_file}")

if __name__ == '__main__':
    main()
