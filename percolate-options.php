<div class="wrap">
    <h2>Percolate Options</h2>
    <form method="post" action="options.php">
        <?php settings_fields(PercolateImport::SETTINGS_PAGE); ?>
        <?php do_settings_sections(PercolateImport::SETTINGS_PAGE); ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>        
    </form>
</div>