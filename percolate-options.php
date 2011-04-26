<div class="wrap">
    <h2>Percolate Options</h2>
    <form method="post" action="options.php">
        <?php settings_fields(PercolateImport::SETTINGS_PAGE); ?>
        <?php do_settings_sections(PercolateImport::SETTINGS_PAGE); ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>

	<p class="submit">

	    <input type="hidden" name="percolateimport_override" value="0" id="override_import" />
	    <input type="button" onclick="jQuery('#override_import').val('1'); this.form.submit();" value="Import your stories now" />
	    &nbsp;&nbsp;Last imported on <?=date( 'm/d/Y g:i a', get_option(PercolateImport::LASTIMPORT_OPTION))?>

	</p>
    </form>
</div>	