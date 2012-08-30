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
	    &nbsp;&nbsp;Last imported on <?=date( 'm/d/Y g:i a', get_option(PercolateImport::LASTIMPORT_OPTION))?> - <small>ID: <?php echo get_option(PercolateImport::STARTID_OPTION) ?> <a href="#" class="perc-debug-toggle" style=''>Debug</a></small>

	</p>
    </form>
</div>	


<div class="perc-debug" style="display:none;">
<pre>
<?php print_r(PercolateImport::LASTIMPORT_OPTION . ' = ' . get_option(PercolateImport::LASTIMPORT_OPTION)) ?>

<?php print_r(PercolateImport::IMPORT_OVERRIDE_OPTION . ' = ' . get_option(PercolateImport::IMPORT_OVERRIDE_OPTION)) ?>

<?php echo 'Next import in '; print_r((time() - get_option(PercolateImport::LASTIMPORT_OPTION)) - PercolateImport::IMPORT_INTERVAL); echo ' seconds';?>

</pre>

</div>