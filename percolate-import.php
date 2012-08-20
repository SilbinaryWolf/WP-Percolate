<?php
/**
 * @package Percolate_Import
 */
/*
Plugin Name: WP Percolate
Plugin URI: http://percolate.org
Description: This plugin turns Percolate posts into Wordpress entries.
Author: Percolate Industries, Inc.
Version: 3.1.2
Author URI: http://percolate.org
*/

class PercolateImport
{
	const SETTINGS_SECTION='percolate_api_settings';
	const SETTINGS_PAGE='percolate';

	const IMPORT_INTERVAL=300;

	const USERTYPE_OPTION='percolateimport_usertype';
	const GROUPID_OPTION='percolateimport_groupid';
	const DEFGRPAUTHORID_OPTION='percolateimport_defgrpauthorid';
	const GROUPAUTHORS_OPTION='percolateimport_groupauthorids';
	const USERID_OPTION='percolateimport_userid';
	const AUTHORID_OPTION='percolateimport_authorid';

	const LASTIMPORT_OPTION='percolateimport_lastimported';
	// const LASTID_OPTION='percolateimport_lastid';

	const STARTID_OPTION='percolateimport_startid';
	const APIKEY_OPTION='percolateimport_apikey';

	const POSTSTATUS_OPTION='percolateimport_poststatus';
	const CATEGORY_OPTION='percolate_category';
	const EX_CATEGORY_OPTION='ex_percolate_category';
	const ALLSOURCES_OPTION='percolate_allsources';

	const IMPORT_OVERRIDE_OPTION='percolateimport_override';

	//const IMPORT_MOSTRECENT_OPTION='percolateimport_recent';

	const API_BASE='http://www.qa.prclt.net/api/v3/';

	const M_LINKID='percolate_link_id';
	const M_ADDEDON='percolate_added_on';
	const M_ORIGINALTITLE='percolate_original_title';
	const M_USERTITLE='percolate_user_title';
	//const M_DOMAIN='percolate_domain';
	const M_ORIGINALDESCRIPTION='percolate_original_description';
	const M_USERDESCRIPTION='percolate_user_description';
	const M_URL='percolate_url';
	const M_SHORTURL='percolate_shorturl';
	const M_SOURCES='percolate_sources';
	const M_FEATUREDSOURCE='percolate_featured_source';
	const M_SOURCETITLES='percolate_source_titles';
	const M_USE='percolate_use';
	const IMPORT_VERSION = '1.0';
	const M_MEDIA='percolate_media';
	const M_POSTEDPERMALINK = 'posted_permalink';
	const M_PERCOLATEID = 'percolate_id';

	/** INSTALL AND INIT CODE **/

	public function install()
	{
		// Check to see if the plugin options were already installed, if not then set the default values.
		if (get_option(self::USERID_OPTION) == FALSE ) update_option(self::USERID_OPTION, '0');
		if (get_option(self::USERTYPE_OPTION) == FALSE ) update_option(self::USERTYPE_OPTION, '0');
		if (get_option(self::GROUPID_OPTION) == FALSE ) update_option(self::GROUPID_OPTION, '0');
		if (get_option(self::LASTIMPORT_OPTION) == FALSE ) update_option(self::LASTIMPORT_OPTION, '0');
		if (get_option(self::STARTID_OPTION) == FALSE ) update_option(self::STARTID_OPTION, '0');
		if (get_option(self::POSTSTATUS_OPTION) == FALSE ) update_option(self::POSTSTATUS_OPTION, 'draft');

    // $recentOption = get_option(self::IMPORT_MOSTRECENT_OPTION);
    // if (!isset($recentOption) || !$recentOption || $recentOption == '') update_option(self::IMPORT_MOSTRECENT_OPTION,0);
		if (get_option(self::ALLSOURCES_OPTION) == FALSE ) update_option(self::ALLSOURCES_OPTION, '0');
	}




	public function init()
	{
		//echo "<pre>"; print_r(self::getPercolateStories()); echo "</pre>";
	}

	public function addRewrite($wpRewrite)
	{

	}

	public function queryVars($pubicQueryVars)
	{

	}

	public function adminScripts()
	{
		echo '<script type="text/javascript" src="' . plugin_dir_url(__File__) . 'percimport.js"></script>';
	}

	public function adminInit()
	{
		//Edit form additions
		add_meta_box(
			'percolate-info',
			'Original Description',
			array('PercolateImport','infoMetaBox'),
			'post',
			'normal',
			'high'
		);


		add_meta_box(
			'percolate-url',
			'Percolate URL',
			array('PercolateImport','urlMetaBox'),
			'post',
			'normal',
			'high'
		);

		add_meta_box(
			'percolate-media',
			'Percolate Media',
			array('PercolateImport','mediaMetaBox'),
			'post',
			'normal',
			'high'
		);

		add_meta_box(
			'percolate-short-url',
			'Percolate Short Url',
			array('PercolateImport','shortUrl'),
			'post',
			'normal',
			'high'
		);
		
		add_meta_box(
			'percolate_id',
			'Percolate ID',
			array('PercolateImport','percolateId'),
			'post',
			'normal',
			'high'
		);
		
		add_meta_box(
			'posted_permalink',
			'Posted Permalink to Percolate',
			array('PercolateImport','postedPermalink'),
			'post',
			'normal',
			'high'
		);
		
		//Settings
		add_settings_section(
			self::SETTINGS_SECTION,
			"Percolate API Settings",
			array('PercolateImport','settingsSectionHeader'),
			self::SETTINGS_PAGE
		);


		add_settings_field(
			self::USERTYPE_OPTION,
			"Percolate User Type",
			array('PercolateImport', 'settingsUserTypeDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);


		add_settings_field(
			self::APIKEY_OPTION,
			"Percolate API KEY",
			array('PercolateImport','settingsApiKeyDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::USERID_OPTION,
			"Percolate User ID",
			array('PercolateImport', 'settingsUserIdDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::GROUPID_OPTION,
			"Percolate Group ID",
			array('PercolateImport', 'settingsGroupIdDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::POSTSTATUS_OPTION,
			"Publish Status",
			array('PercolateImport', 'postStatusDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::AUTHORID_OPTION,
			"Author",
			array('PercolateImport', 'settingsAuthorDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::DEFGRPAUTHORID_OPTION,
			"Default Group Author",
			array('PercolateImport', 'settingsDefGrpAuthorDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::GROUPAUTHORS_OPTION,
			"Group Authors",
			array('PercolateImport', 'settingsGroupAuthorsDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);

		add_settings_field(
			self::CATEGORY_OPTION,
			"Category",
			array('PercolateImport', 'settingsCategoryDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);


/*
		add_settings_field(
			self::EX_CATEGORY_OPTION,
			"Exclude Category",
			array('PercolateImport', 'settingsExcludeCategoryDisplay'),
			self::SETTINGS_PAGE,
			self::SETTINGS_SECTION
		);
*/


    // add_settings_field(
    //  self::IMPORT_MOSTRECENT_OPTION,
    //  "On initial import start from the most recent posts and go forward",
    //  array('PercolateImport', 'settingsImportRecentDisplay'),
    //  self::SETTINGS_PAGE,
    //  self::SETTINGS_SECTION
    // );


		register_setting(self::SETTINGS_PAGE, self::USERTYPE_OPTION);
		register_setting(self::SETTINGS_PAGE, self::GROUPID_OPTION);
		register_setting(self::SETTINGS_PAGE, self::APIKEY_OPTION);
		register_setting(self::SETTINGS_PAGE, self::DEFGRPAUTHORID_OPTION);
		register_setting(self::SETTINGS_PAGE, self::GROUPAUTHORS_OPTION);
		register_setting(self::SETTINGS_PAGE, self::USERID_OPTION);
		register_setting(self::SETTINGS_PAGE, self::POSTSTATUS_OPTION);
		register_setting(self::SETTINGS_PAGE, self::AUTHORID_OPTION);
		register_setting(self::SETTINGS_PAGE, self::CATEGORY_OPTION);
		//register_setting(self::SETTINGS_PAGE, self::EX_CATEGORY_OPTION);
		//register_setting(self::SETTINGS_PAGE, self::IMPORT_MOSTRECENT_OPTION);
		register_setting(self::SETTINGS_PAGE, self::ALLSOURCES_OPTION);
		register_setting(self::SETTINGS_PAGE, self::IMPORT_OVERRIDE_OPTION);

		//Import process
	    self::checkImport();
		// TODO: do we still need this?

		// self::checkUpdate();
	}

/*
function percoalte_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/percolate-import.php' ) ) {
		$links[] = '<a href="'.admin_url('options-general.php?page=percolate').'">'.__('Settings').'</a>';
	}

	return $links;
}

add_filter( 'plugin_action_links', 'percoalte_plugin_action_links');
*/






	/** POST META BOXES **/
	public function urlMetaBox($post)
	{
		$url = get_post_meta($post->ID, self::M_URL, true);
?>
        <?php echo 	$url ?>
        <?php
	}


		public function shortUrl($post)
		{
			$url = get_post_meta($post->ID, self::M_SHORTURL, true);
	?>
	        <?php echo $url ?>
	        <?php
		}

	public function infoMetaBox($post)
	{
		$userTitle = get_post_meta($post->ID, self::M_USERTITLE, true);
		$originalTitle = get_post_meta($post->ID, self::M_ORIGINALTITLE, true);
		$userDescription = get_post_meta($post->ID, self::M_USERDESCRIPTION, true);
		$originalDescription = get_post_meta($post->ID, self::M_ORIGINALDESCRIPTION, true);
		//use post id
		$linkId = get_post_meta($post->ID, self::M_LINKID, true);
		if (!$linkId) {
			echo '<p class="nonessential">Not a Percolate post.</p>';
			return;
		}
?>
        <?php echo $originalDescription; ?>
        <?php
	}

	public function mediaMetaBox($post){
		$mediaMeta = get_post_meta($post->ID, self::M_MEDIA);

		if ($mediaMeta) {
		foreach($mediaMeta as $media_js){
			// Get the media type
			//$medias= json_decode($media_js);
			$media=$media_js;
			$mediaType = $media['type'];
			echo "<h4>Type: " . $mediaType . "</h4>";
			echo "<input type='hidden' value='" . $mediaType . "' id='media_type' />";

			if ($mediaType === "image") {
				$p_img = $media['src']; //$p_img = $media['p_img']; //apiV3 feature
				$p_img_width = $media['images']['large']['width'];//apiV3 feature
				$p_img_height = $media['images']['large']['height']; //apiV3 feature
				
				echo "<img src='$p_img' id='m_media' width='$p_img_width' height='$p_img_height' />"; //echo "<img src='$p_img' id='m_media' />"; narada
			}
			if ($mediaType === "video") {
				$video_url = $media['url'];

				if(strstr($video_url, "vimeo")) {
					echo '<iframe src="'.$video_url.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="520" height="290" frameborder="0" param="" name="wmode" value="opaque"></iframe><br /><br /><br /><h4>Copy This Embed Code.</h4><textarea style="width:90%;color:#CCC;" id="m_media_video"><iframe src="'.$video_url.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="520" height="290" frameborder="0" param="" name="wmode" value="opaque"></iframe></textarea>';
				}
				if(strstr($video_url, "youtube")) {
					echo '<iframe title="YouTube video player" width="520" height="320" src="'.$video_url.'?wmode=transparent&amp;rel=0" frameborder="0" type="text/html"></iframe><br /><br /><br /><h4>Copy This Embed Code.</h4><textarea style="width:90%;color:#CCC;" id="m_media_video"><iframe title="YouTube video player" width="520" height="320" src="'.$video_url.'?wmode=transparent&amp;rel=0" frameborder="0" type="text/html"></iframe></textarea>';
				}
			}
			if ($mediaType === "quote") {
				$quote_text = $media['text'];
				echo '<blockquote>$quote_text</blockquote><textarea style="width:90%;color:#CCC;" id="m_media_quote"><blockquote>' . $quote_text . '</blockquote></textarea>';
			}
			}
		}

	?>
	<script type="text/javascript">
	    jQuery(function () {
	        (function($){

	            if(!$("#media_type").val()){
	              $('.select-media-button').hide();
	            }

	            $('.select-media-button').click(function () {

	            	mType = $("#media_type").val();

	            	if (mType == 'image' ) {
		              p_img =$("#m_media").attr('src');
		              embedContent = '<img src="'+ p_img +'" alt="" />';
								} else if (mType == 'video') {
									embedContent = $("textarea#m_media_video").val();
								}	else if (mType == 'quote') {
		              embedContent = $("textarea#m_media_quote").val();
		            }

		              switchEditors.go('content', 'html');
									//edInsertContent(edCanvas, embedContent); // This stopped working in 3.3.1
		              send_to_editor(embedContent);
		              switchEditors.go('content', 'tinymce');

	            });



	        })(jQuery);
	    });
	    </script>
		 <div class="add-source-input">
	  	<br /><br />
	  	<input type="button" class="select-media-button" value="Insert <?php echo $mediaType ?> into post body." />
	   </div>
	<?php
	}

	//posted permalink to percolate
	public function postedPermalink($post)
	{
		$plink = get_post_meta($post->ID, self::M_POSTEDPERMALINK, true);
 		echo $plink;
	}
	// percolate id
	public function percolateId($post)
	{
		$percolateId = get_post_meta($post->ID, self::M_PERCOLATEID, true);
 		echo $percolateId;
	}



	public function updatePost($postId)
	{

		if (wp_is_post_revision($postId)) {
			return;
		}



    // if (!empty($_POST[self::M_DOMAIN])) {
    //  update_post_meta($postId, self::M_DOMAIN, $_POST[self::M_DOMAIN]);
    // }

		if (!empty($_POST[self::M_URL])) {
			update_post_meta($postId, self::M_URL, $_POST[self::M_URL]);
		}

		if (!empty($_POST[self::M_SHORTURL])) {
			update_post_meta($postId, self::M_SHORTURL, $_POST[self::M_SHORTURL]);
		}


	}


/** PERCOLATE SETTINGS **/

	public function settingsSectionHeader()
	{
		echo "<p>Settings for Percolate API Integration</p>";
	}

	public function settingsUserTypeDisplay()
	{
		$userType = get_option(self::USERTYPE_OPTION);
?>
		<span class="percapi-user-type">
		   	<input type="radio" name="<?php echo self::USERTYPE_OPTION; ?>"
		   	     	id="percapi-user-type-individual"  value="0"
					 <?php
		echo $userType != 1 ?  "checked=\"checked\"" :  "" ;
		?> />
		   	     	Individual
		   	<input type="radio" name="<?php echo self::USERTYPE_OPTION; ?>"
		   	     	id="percapi-user-type-group" value="1"
					 <?php
		echo $userType == 1 ?  "checked=\"checked\"" : "" ;
?>
		   	      />
		   	     	Group
			<input type="hidden" name="init_user_type" id="init_user_type" value="<?php echo $userType;?>">
		</span>
	<?php
	}


	public function settingsUserIdDisplay()
	{
?>
        <span class="percapi-userid-control user-type-indi">
        <input size="5" type="text" class="user-id" name="<?php echo self::USERID_OPTION; ?>"
            id="percapi_user_id"
            value="<?php echo get_option(self::USERID_OPTION != '0' ? self::USERID_OPTION : ''); ?>" />
            User ID for the Percolate API.
        </span>
        <?php
	}

	public function settingsGroupIdDisplay()
	{
?>
        <span class="percapi-usergroupid-control user-type-grp">
        <input size="5" type="text" class="user-group-id" name="<?php echo self::GROUPID_OPTION; ?>"
            id="percapi_user_id"
            value="<?php echo get_option(self::GROUPID_OPTION != '0' ? self::GROUPID_OPTION : ''); ?>" />
            Group ID for the Percolate API.
        </span>
        <?php
	}


	public function settingsApiKeyDisplay()
	{
		?>
    <span class="percapi-apikey-control api-key">
    <input size="44" type="text" class="api-key" name="<?php echo self::APIKEY_OPTION; ?>"
        id="percapi_api_key"
        value="<?php echo get_option(self::APIKEY_OPTION); ?>" />
    </span>
    <?php
	}

	public function settingsDefGrpAuthorDisplay()
	{
		global $wp_version;
		if ($wp_version >= "3.1") {
			$users = get_users();
		} else {
			$users = get_users_of_blog();
		}

		$defgrpauthorId = get_option(self::DEFGRPAUTHORID_OPTION);
?>
		<span class="user-type-grp">
        <select name="<?php echo self::DEFGRPAUTHORID_OPTION; ?>">
            <?php foreach ($users as $user): ?>
            <option <?php echo ($user->ID == $defgrpauthorId) ? ' selected="selected" ' : ''; ?>
                value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
            <?php endforeach; ?>
        </select> When a Wordpress author does not exit for a corresponding group member assign posts to this Wordpress user.
        <?php
	}




	public function settingsAuthorDisplay()
	{

		global $wp_version;
		if ($wp_version >= "3.1") {
			$users = get_users();
		} else {
			$users = get_users_of_blog();
		}


		$authorId = get_option(self::AUTHORID_OPTION);
		//echo "<pre>"; print_r($users); echo "</pre>";
?>
		<span class="user-type-indi">
        <select name="<?php echo self::AUTHORID_OPTION; ?>">
            <?php foreach ($users as $user): ?>
            <option <?php echo ($user->ID == $authorId) ? ' selected="selected" ' : ''; ?>
                value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
            <?php endforeach; ?>
        </select> New posts imported from percolate will be owned by this user.
		</span>
        <?php
	}

	public function settingsGroupAuthorsDisplay(){
		$group_authors = get_option(self::GROUPAUTHORS_OPTION);
?>
		<span class="user-type-grp">
				<input type="hidden" name="<?php echo self::GROUPAUTHORS_OPTION ?>" id="<?php echo self::GROUPAUTHORS_OPTION ?>" value="<?php echo empty($group_authors) ? "" : $group_authors ?>"/>

			<table>
				<tr>
					<th>
						Group Members
				            <input type="button" id="refresh_memberform" value="<?php _e('Refresh') ?>" />

					</th>
				 	<th>
				 		Wordpress Authors
				 	</th>
				 </tr>
		<?php

		$group_id = get_option(self::GROUPID_OPTION);
		$group_authors_array = json_decode($group_authors);

		if($group_id>0){

			$percolate_users = self::getGroupUsers($group_id);

			$objects = $percolate_users['objects'];


			if(is_array($objects)){

				global $wp_version;
				if ($wp_version >= "3.1") {
					$users = get_users();
				} else {
					$users = get_users_of_blog();
				}

				foreach ($objects as $puser) {
					$temp_pid = $puser['id'];
?>
				<tr>
					<td width="100px;">
						<?php echo $puser['username'];?>
					</td>
					<td>
						<select name="<?php echo $temp_pid?>" class="group_user_ids" >
				            <?php foreach ($users as $user):
?>

										<?php if (isset($group_authors_array->$temp_pid)) { ?>
				            <option <?php echo ($user->ID == $group_authors_array->$temp_pid) ? ' selected="selected" ' : ''; ?>
					            value="<?php echo "'".$temp_pid."':'".$user->ID."'"; ?>"><?php echo $user->display_name; ?></option>
					          <?php } else { ?>
				            <option value="<?php echo "'".$temp_pid."':'".$user->ID."'"; ?>"><?php echo $user->display_name; ?></option>
					          <?php } ?>
				            <?php endforeach; ?>
							 <option value="new_author">Create New Author</option>
					    </select>
					</td>
				</tr>
			<?php
				}
			}else{
				echo "Did not find any users with the group id $group_id";
			}

		}else{
			echo "Enter a group id and save to view the group users.";
		}
?>
			</table>
		</span>
		<?php

	}


	public function settingsCategoryDisplay()
	{
		$categoryId = get_option(self::CATEGORY_OPTION);
		wp_dropdown_categories('hide_empty=0&name=' . self::CATEGORY_OPTION . '&selected=' . $categoryId);
		//echo "<pre>"; print_r($users); echo "</pre>";
?>
        New posts imported from percolate will appear in this category.

        <?php
	}

	public function settingsExcludeCategoryDisplay()
	{
		$ex_categoryId = get_option(self::EX_CATEGORY_OPTION);
		wp_dropdown_categories('hide_empty=0&show_option_none=None&name=' . self::EX_CATEGORY_OPTION . '&selected=' . $ex_categoryId);
		//echo "<pre>"; print_r($users); echo "</pre>";
		?>
        Posts assigned to this category will not appear.

        <?php
	}

	public function userIdNotice()
	{
		if (get_option(self::USERID_OPTION) || get_option(self::GROUPID_OPTION)) {
			return;
		}

		echo '<div class="error">';
		echo '<p>';
		echo '<strong>' . __('Notice:') . '</strong> ';
		_e(
			'You haven&rsquo;t set a Percolate User or Group ID for the Percolate
            Import plugin. This plugin will not function correctly
            until the user or group ID is set.'
		);
		echo '</p><p>';
		printf(
			'<a href="%s">' . __('Percolate Settings Page') . '</a>',
			admin_url('options-general.php?page=percolate')
		);
		echo '</p></div>';
	}


	public function postStatusDisplay()
	{
		$options = array('draft'=>'Enter as Drafts', 'publish'=>'Publish Immediately');
		$option = get_option(self::POSTSTATUS_OPTION);
		if (!$option) {
			$option = 'draft';
		}
		echo '<select name="' . self::POSTSTATUS_OPTION . '" >';
		foreach ($options as $key=>$text) {
			echo '<option ' . ($key == $option ? 'selected="selected"' : '') .
				' value="' . $key . '">' . $text . '</option>';
		}
		echo '</select>';
	}

	public function adminMenu()
	{
		add_submenu_page(
			'options-general.php',
			'Percolate Settings Page',
			'Percolate',
			'administrator',
			'percolate',
			array('PercolateImport','settingsPage')
		);
	}

	public function settingsPage()
	{
		if( isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true' && get_option(PercolateImport::IMPORT_OVERRIDE_OPTION) == 1 )
		{
			$last_id = get_option(self::STARTID_OPTION);
?>
        <div id="stories_imported" class="updated settings-error">
        <p>
            <strong>Stories Imported. Last id: <?php echo ($last_id); ?></strong>
        </p>
        </div>
        <?php
			update_option(self::IMPORT_OVERRIDE_OPTION, 0);
		}

		load_template(dirname(__FILE__) . '/percolate-options.php');
	}


	/** IMPORT SCREEN **/
	public function renderImportPage()
	{
		load_template(dirname(__FILE__) . '/admin-import-page.php');
	}


	/** PERCOLATE API **/

	/**
	 * Call the percolate API and try to import stories
	 */
	public function importStories()
	{
    $limit = 30; 
    $offset = 0; //for initial step, this wil update within following loop
    $total = 1; //for initial step, this wil update within following loop
    
    while( intval($offset) < intval($total) ) {
      
  		$posts = self::getPercolatePosts($offset, $limit);		
  		
  		$pagination = $posts['pagination'];
  		$total = $pagination['total']; 
  		$offset = intval($offset) + 30;
  		
  		$objects = $posts['data'];
  		$last_startId = get_option(self::STARTID_OPTION);

  		// Check to see if the last_id coming from the percolate API is larger than that is what is
  		// stored in the wp db, if its smaller than something is wrong and we don't update the start_at_id and don't
  		// do the import.

  			if ($objects) {
  				foreach ($objects as $object) {
  				  $startId = $object['id'];
				  
  				  if(intval($last_startId) < intval($startId)) { 
  				   self::importStory($object);
  				  }
				  
  				}
  				
  				update_option(self::STARTID_OPTION, $startId); 
  				    
  		  } 
  	}
  	
  	update_option(self::LASTIMPORT_OPTION, time());
		
	}

	/**
	 * Add a percolate story to the WP system as a post
	 **/
	public function importStory($object)
	{
		global $wpdb;		
		

		$tags_array =  $object['tags'];
		$body = $object['body'];
		$analytics_array = $object['analytics'];
		$short_url = $object['short_url'];
		$link_array =  $object['link'];
		$url_array = $link_array['url'];
		$media_array = $object['media'];
		$percolate_id = $object['id'];

		$linkId = $object['id'];

		$postName = 'perc_' . $linkId;

		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_name, post_parent
                FROM $wpdb->posts
                WHERE post_name = %s AND (post_type = %s OR post_type = 'attachment')",
				$postName,
				'post'
			)
		);

		if ($posts) {
			return false;
		}

		$post = array();
		$post['post_title']=html_entity_decode($object['title']); //apiV3 feature

		if (!trim($post['post_title'])) {
			$post['post_title']=html_entity_decode($link_array['title']);
		}

		$post['post_content']=$body;

		$post['post_name']=$postName;

		$offset =  get_option('gmt_offset');

		// utc timezone adjustment
		if (0 == $offset){
			$post['post_date']=date('Y-m-d H:i:s', strtotime($object['created_at']));
		}else{
			$post['post_date']=date('Y-m-d H:i:s', strtotime($object['created_at']." ".$offset." hours"));
		}

		$post['post_status']=get_option(self::POSTSTATUS_OPTION);


		if (get_option(self::CATEGORY_OPTION)) {
			$post['post_category']=array(get_option(self::CATEGORY_OPTION));
		}


		//check user type
		$userType = get_option(self::USERTYPE_OPTION);
		if($userType!=1){
			$authorId = get_option(self::AUTHORID_OPTION);
			if ($authorId) {
				$post['post_author']=$authorId;
			}
		}else{
			//story author
			$author_id = $object['user_id'];
			//get group authors
			$group_authors = get_option(self::GROUPAUTHORS_OPTION);
			$group_authors_array = json_decode($group_authors);
			//check if the percolate user is mapped to a wp user.
			$user_id = $group_authors_array->$author_id;
			if ($user_id !=0){
				$post['post_author']=$user_id;
			}else{
				$post['post_author']=get_option(self::DEFGRPAUTHORID_OPTION);
			}


		}

		$postId = wp_insert_post($post);

		if ($tags_array) {
			$tags_s = "";
			foreach($tags_array as $tag){
			  $tags_s = $tags_s.$tag['tag'] .",";
			}
		wp_set_post_tags($postId, $tags_s);
		}

		$ver = floatval(phpversion());

		update_post_meta($postId, self::M_ADDEDON, strtotime($object['created_at']));
		update_post_meta($postId, self::M_LINKID, $link_array['id']);
		update_post_meta($postId, self::M_ORIGINALDESCRIPTION, html_entity_decode($link_array['description']));
		update_post_meta($postId, self::M_ORIGINALTITLE, html_entity_decode($link_array['title'])); 
		update_post_meta($postId, self::M_URL, $url_array);
		update_post_meta($postId, self::M_SHORTURL, $short_url);
		update_post_meta($postId, self::M_USERDESCRIPTION, html_entity_decode($link_array['description']));
		update_post_meta($postId, self::M_USERTITLE, $object['title']);
		update_post_meta($postId, self::M_PERCOLATEID, $percolate_id);
		
		
		//add or update media
		update_post_meta($postId, self::M_MEDIA,$media_array);

		do_action('percolate_import_story', $postId);
		
		//return an array with permalink and perc id to post back to percolate
		$plink = get_permalink($postId);
		update_post_meta($postId, self::M_POSTEDPERMALINK, $plink);
		
		return array("post_id" => $linkId,
			         "permalink" => $plink
			    );
	}

	public static function getUrl($postId)
	{
		$url = get_post_meta($postId, self::M_URL, true);

		return $url;
	}

	/**
	 * Check to see if stories have been imported recently
	 */
	public function checkImport()
	{


		if ( ((time() - get_option(self::LASTIMPORT_OPTION)) > self::IMPORT_INTERVAL) || get_option( self::IMPORT_OVERRIDE_OPTION ) == 1 ) {
			try{
				self::importStories();
			}
			catch (Exception $e)
			{

			}
		}
	}

	public function getPercolatePosts($offset,$limit) {

		$apiKey = get_option(self::APIKEY_OPTION);
		if($apiKey){
			$options['api_key'] = $apiKey;
		}else{
			//no api key return empty array for now
			return array();
		}

		$userType = get_option(self::USERTYPE_OPTION);

		if($userType!=1){
			$user_id = get_option(self::USERID_OPTION);
			$method = "users/".$user_id."/posts/";
		}else{
			$group_id=get_option(self::GROUPID_OPTION);
			$method = "groups/".$group_id."/posts/";
    }
    
    $options['statuses'] = 'published'; //apiV3 feature
    $options['order_direction'] = 'desc'; //apiV3 feature
    $options['limit'] = $limit; //apiV3 feature 
    $options['offset'] = $offset; //apiV3 feature 

   
		return self::callPercolateApi($method , $options);
	}


	//get group users
	public function getGroupUsers($groupId){
		$apiKey = get_option(self::APIKEY_OPTION);
		if($apiKey){
			$options['api_key'] = $apiKey;
		}else{
			//no api key return empty array for now
			return array();
		}

		$method = 'groups/'.$groupId.'/users';
		//echo "<pre>"; print_r(self::callPercolateApi($method , $options)); echo "</pre>";
		try {
		   	return self::callPercolateApi($method , $options);
		} catch (Exception $e) {
		    return $e;
		}


	}
	
	//post to percolate 
	public function postToPercolate($jsonFields){
		$apiKey = get_option(self::APIKEY_OPTION);
		if($apiKey){
			$options['api_key'] = $apiKey;
		}else{
			//no api key return false
			return false;
		}
		
		$method = 'publish/public';
		
		try {
			self::callPercolateApi($method , $options, $jsonFields);
		} catch (Exception $e) {
			//try posting again
			call_user_func(__FUNCTION__,$jsonFields);
		}
		
		return true;
		
	}
	
	// Post the wordpress permalink back to percolate.
	public function permalink_post_back($postId) {
	  
	  	$perc_permalink = get_post_meta($postId, self::M_POSTEDPERMALINK, true);
	  	$percolate_id = get_post_meta($postId, self::M_PERCOLATEID, true);
    	self::postToPercolate( array("post_id" => $percolate_id, "permalink" => $perc_permalink) );
    
	}
	
	protected static function callPercolateApi($method, $fields=array(), $jsonFields=array())

	{

		$url = self::API_BASE . "$method";
		
			if ($fields) {
				$tokens = array();
				foreach ($fields as $key=>$val) {
					$tokens[]="$key=$val";
				}
				$url.="?" . implode('&', $tokens);
			}
		    
			/* call url*/
			$curl_handle = curl_init($url);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_HEADER, 0);
		
			/* json post fields */
			if ($jsonFields) {
				curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS,json_encode($jsonFields));
			}else{
				curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
			}
				
			$buffer = curl_exec($curl_handle);
			
			$status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
			
			curl_close($curl_handle);
			
			$data = json_decode( $buffer, true );
		
			if ($status != 200) {
			
				$message = "An unknown error occurred communicating with Percolate ($status) - $buffer";
			
				if ($data) {
			
					if ($data['error']) {
						$message = $data['error'];
					}
			
					if (array_key_exists('request', $data)) {
						$message .= ' -- Request: '.$data['request'];
					}
				} else {
					$message = "No Data received.";
				}
			
				throw new Exception($message, $status);
			}
		
			
			$data = json_decode( $buffer, true );

		return $data;


	}

	function checkUpdate()
	{
		$message = self::callPercolateApi('check_version', array('version'=>self::IMPORT_VERSION));

		if( $message['message'] ) {
?>
    <script type="text/javascript">
        if( document.getElementById('wpbody-content') ) {
        if( !document.getElementById('percolate_update') ) {


        var newP = document.createElement("div");
        newP.className = 'update-nag';
        newP.id='percolate_update';

        newP.innerHTML = '<?php echo $message['message']?>';
        var p2 = document.getElementsByClassName("wrap")[0];
        p2.parentNode.insertBefore(newP,p2);

        }
        }
    </script>
    <?php
		}
	}

	function activateImport(){
		wp_schedule_event(time(), 'minute', 'percolate_import_event');
	}
	function deactivateImport(){
		wp_clear_scheduled_hook('percolate_import_event');
	}

	function scheduleImport( $schedules ) {
		$schedules['minute'] = array(
			'interval' => 300,
			'display' => __('Once 300 seconds')
		);
	return $schedules;
	}

}



// Add settings link on plugin page
function percolate_plugin_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=percolate">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

// Add Check for Updates on plugin page
function percolate_plugin_check_updates_link($links) {
  $settings_link = "<a id='percolate_update_check' href='#' >Check for Updates</a>";
  array_unshift($links, $settings_link);
  return $links;
}
//load this javascript to admin header
function percolate_plugin_check_updates_js(){
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function(){jQuery('#percolate_update_check').click(function(){jQuery(this).html("Checking...");var data = {action: 'percolate_check_updates_action'};jQuery.post('/wp-admin/admin-ajax.php', data, function(response){window.location.reload();});});});</script>
<?php
}

add_action('admin_head','percolate_plugin_check_updates_js' );


$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'percolate_plugin_check_updates_link' );
add_filter("plugin_action_links_$plugin", 'percolate_plugin_settings_link' );

add_action('wp_ajax_percolate_check_updates_action', 'percolate_check_updates_action_callback');



register_activation_hook(__FILE__, array('PercolateImport', 'install'));

register_activation_hook(__FILE__, array('PercolateImport', 'activateImport'));
register_deactivation_hook(__FILE__, array('PercolateImport', 'deactivateImport'));

add_action('percolate_import_event', array('PercolateImport', 'importStories'));
add_filter('cron_schedules',  array('PercolateImport', 'scheduleImport'));


add_action('plugins_loaded', array('PercolateImport', 'init'));
add_action('admin_init', array('PercolateImport','adminInit'));
add_action('admin_menu', array('PercolateImport', 'adminMenu'));
add_action('save_post', array('PercolateImport', 'updatePost'));
add_action('admin_print_footer_scripts', array('PercolateImport', 'adminScripts'));
add_action('admin_notices', array('PercolateImport','userIdNotice'));
add_action('publish_post', array('PercolateImport','permalink_post_back')); //apiV3 feature

// The plugin github updater
include_once('updater.php');
if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
	$config = array(
		'slug' => plugin_basename(__FILE__),
		'proper_folder_name' => 'WP-Percolate',
		'api_url' => 'https://api.github.com/repos/percolate/WP-Percolate',
		'raw_url' => 'https://raw.github.com/percolate/WP-Percolate/master',
		'github_url' => 'https://github.com/percolate/WP-Percolate',
		'zip_url' => 'https://github.com/percolate/WP-Percolate/zipball/master',
		'sslverify' => false,
		'requires' => "3.1.0",
		'tested' => "3.3.1", //$wp_version
		);
GLOBAL $gitHubUpdater;
$gitHubUpdater = new GitHubUpdater($config);
//reset the transients to allow update checks
function percolate_check_updates_action_callback(){
  global $gitHubUpdater;
  $gitHubUpdater->delete_transients();
}

}

?>