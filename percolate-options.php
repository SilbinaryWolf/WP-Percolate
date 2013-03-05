<?php 

// Blog Offset
$time_offset = get_option('gmt_offset');

// Last Imported
$last_imported = get_option(PercolateImport::LASTIMPORT_OPTION);
$formatted_last_imported = date('Y-m-d g:i:sa', $last_imported);
$formatted_date = date('Y-m-d g:i:sa', strtotime($time_offset . " hours", strtotime($formatted_last_imported)));


$next_wp_cron = wp_next_scheduled('percolate_import_event'); 
$formatted_next_wp_cron = date('Y-m-d g:i:sa', $next_wp_cron);
$offset_next_wp_cron = date('Y-m-d g:i:sa', strtotime($time_offset . " hours", strtotime($formatted_next_wp_cron)));

?>

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
        <select name="post_type">
            <?php
            $args=array(
            );
            $output = 'names'; // names or objects, note names is the default
            $operator = 'and'; // 'and' or 'or'
            $post_types=get_post_types($args,$output,$operator);
            foreach ($post_types as $post_type ) {
                echo '<option value="'. $post_type. '">'. $post_type. '</option>';
            }

            ?>
        </select>
	    <input type="button" id="import_stories_now" value="Import your stories now" />

	    &nbsp;&nbsp;Last imported on <?= $formatted_date ?> - ID: <?php echo get_option(PercolateImport::STARTID_OPTION) ?> <a href="#" class="perc-debug-toggle" style=''> Debug</a>

	</p>
    </form>
</div>	


<div class="perc-debug" style="display:none;">

<pre>
<?php
echo "\nLast imported at: <strong>" . $formatted_date. "</strong>";
echo "\nOverride import option: <strong>" . get_option(PercolateImport::IMPORT_OVERRIDE_OPTION) . "</strong>";
echo "\nNext import in <strong>T"; print_r((time() - get_option(PercolateImport::LASTIMPORT_OPTION)) - PercolateImport::IMPORT_INTERVAL); echo ' seconds</strong>';
echo "\nNext wp-cron scheduled for:<strong> ".$offset_next_wp_cron . "</strong>";
?>
</pre>

</div>