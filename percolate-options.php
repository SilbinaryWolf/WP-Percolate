<div class="wrap">
    <h2>Percolate Options</h2>
    <form method="post" action="options.php" id="percolate_options">
        <?php settings_fields(PercolateImport::SETTINGS_PAGE); ?>
        <?php do_settings_sections(PercolateImport::SETTINGS_PAGE); ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>

	<p class="submit">

	    <input type="hidden" name="percolateimport_override" value="0" id="override_import" />
	    <input type="button" id="import_stories_now" value="Import your stories now" />
	    &nbsp;&nbsp;Last imported on <?=date( 'm/d/Y g:i a', get_option(PercolateImport::LASTIMPORT_OPTION))?> - <small>ID: <?php echo get_option(PercolateImport::LASTID_OPTION) ?></small>

	</p>
    </form>
</div>	