<?php
/**
 * Tabs Module - Render
 */

$tabs = $layout['tabs'] ?? [];

if ( empty( $tabs ) ) {
    return;
}

$tab_id = 'tabs-' . wp_unique_id();
?>

<div class="tabs-module" id="<?php echo esc_attr( $tab_id ); ?>">
    <div class="tabs-module__nav" role="tablist">
        <?php foreach ( $tabs as $index => $tab ) : ?>
            <button 
                role="tab"
                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                aria-controls="<?php echo esc_attr( $tab_id . '-panel-' . $index ); ?>"
                id="<?php echo esc_attr( $tab_id . '-tab-' . $index ); ?>"
                class="tabs-module__nav-item <?php echo $index === 0 ? 'active' : ''; ?>"
            >
                <?php echo esc_html( $tab['title'] ); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="tabs-module__content">
        <?php foreach ( $tabs as $index => $tab ) : ?>
            <div
                role="tabpanel"
                id="<?php echo esc_attr( $tab_id . '-panel-' . $index ); ?>"
                aria-labelledby="<?php echo esc_attr( $tab_id . '-tab-' . $index ); ?>"
                class="tabs-module__panel <?php echo $index === 0 ? 'active' : ''; ?>"
            >
                <?php echo wp_kses_post( $tab['content'] ); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function() {
    const tabId = '<?php echo esc_js( $tab_id ); ?>';
    const buttons = document.querySelectorAll('#' + tabId + ' [role="tab"]');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            buttons.forEach(b => b.setAttribute('aria-selected', 'false'));
            document.querySelectorAll('#' + tabId + ' [role="tabpanel"]').forEach(p => p.classList.remove('active'));
            this.setAttribute('aria-selected', 'true');
            const panelId = this.getAttribute('aria-controls');
            document.getElementById(panelId).classList.add('active');
        });
    });
})();
</script>
