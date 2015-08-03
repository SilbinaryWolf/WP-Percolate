<?php
/**
 * @package Percolate_Import
 */
/*
Plugin Name: WP Percolate
Plugin URI: http://percolate.com
Description: This plugin turns Percolate posts into Wordpress entries.
Author: Percolate Industries, Inc.
Version: 3.3.5
Author URI: http://wp.percolate.com

*/

class PercolateImport
{
  const SETTINGS_SECTION='percolate_api_settings';
  const SETTINGS_PAGE='percolate';

  const IMPORT_INTERVAL=300;
  const MIN_IMPORT_INTERVAL=180;
  const IMPORT_INTERVAL_OPTION='percolateimport_interval';

  const USERTYPE_OPTION='percolateimport_usertype';
  const GROUPID_OPTION='percolateimport_groupid';
  const DEFGRPAUTHORID_OPTION='percolateimport_defgrpauthorid';
  const GROUPAUTHORS_OPTION='percolateimport_groupauthorids';
  const USERID_OPTION='percolateimport_userid';
  const AUTHORID_OPTION='percolateimport_authorid';
  const IMPORT_LOAD_IMAGES_OPTION='percolateimport_load_images';

  const LASTIMPORT_OPTION='percolateimport_lastimported';
  // const LASTID_OPTION='percolateimport_lastid';

  const STARTID_OPTION='percolateimport_startid';
  const APIKEY_OPTION='percolateimport_apikey';

  const POSTSTATUS_OPTION='percolateimport_poststatus';
  const CATEGORY_OPTION='percolate_category';
  const EX_CATEGORY_OPTION='ex_percolate_category';
  const ALLSOURCES_OPTION='percolate_allsources';

  const IMPORT_OVERRIDE_OPTION='percolateimport_override';

  const POSTTYPE_OPTION='percolateimport_posttype';

  //Used in callPercolateApi function. PERCOLATE_API_BASE, defined in wp-config.php
  //shall take precedence over API_BASE constant.
  const API_BASE='https://percolate.com/api/v3/';

  const M_LINKID='percolate_link_id';
  const M_ADDEDON='percolate_added_on';
  const M_ORIGINALTITLE='percolate_original_title';
  const M_USERTITLE='percolate_user_title';
  const M_DOMAIN='percolate_domain';
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

  const CHANNEL_ID_OPTION = 'percolateimport_channel_id';

  /** INSTALL AND INIT CODE **/

  public function install()
  {
    // Check to see if the plugin options were already installed, if not then set the default values.
    if (get_option(self::USERID_OPTION) == FALSE ) update_option(self::USERID_OPTION, '0');
    if (get_option(self::USERTYPE_OPTION) == FALSE ) update_option(self::USERTYPE_OPTION, '0');
    //if (get_option(self::GROUPID_OPTION) == FALSE ) update_option(self::GROUPID_OPTION, '0');
    if (get_option(self::LASTIMPORT_OPTION) == FALSE ) update_option(self::LASTIMPORT_OPTION, '0');
    if (get_option(self::STARTID_OPTION) == FALSE ) update_option(self::STARTID_OPTION, '0');
    if (get_option(self::POSTSTATUS_OPTION) == FALSE ) update_option(self::POSTSTATUS_OPTION, 'draft');
    if (get_option(self::POSTTYPE_OPTION) == FALSE ) update_option(self::POSTTYPE_OPTION, 'post');
    if (get_option(self::CHANNEL_ID_OPTION) == FALSE ) update_option(self::CHANNEL_ID_OPTION, '0');
    if (get_option(self::IMPORT_INTERVAL_OPTION) == FALSE ) update_option(self::IMPORT_INTERVAL_OPTION, self::IMPORT_INTERVAL);
    if (get_option(self::IMPORT_LOAD_IMAGES_OPTION) == FALSE ) update_option(self::IMPORT_LOAD_IMAGES_OPTION, 0);

    // $recentOption = get_option(self::IMPORT_MOSTRECENT_OPTION);
    // if (!isset($recentOption) || !$recentOption || $recentOption == '') update_option(self::IMPORT_MOSTRECENT_OPTION,0);
    if (get_option(self::ALLSOURCES_OPTION) == FALSE ) update_option(self::ALLSOURCES_OPTION, '0');
  }




  public static function init()
  {

  }

  public function addRewrite($wpRewrite)
  {

  }

  public function queryVars($pubicQueryVars)
  {

  }

  public static function adminScripts()
  {
    echo '<script type="text/javascript" src="' . plugin_dir_url(__File__) . 'percimport.js"></script>';
  }


  //=========================================
  // Admin
  //=========================================
  public static function adminInit() {

    // Get the post type set in the options.
    $postTypeSlug = get_option(self::POSTTYPE_OPTION);

    //----------------------
    // Meta boxes for percolate posts
    //----------------------
    add_meta_box(
      'percolate-info',
      'Original Description',
      array('PercolateImport','infoMetaBox'),
      $postTypeSlug,
      'normal',
      'high'
    );

    add_meta_box(
      'percolate-url',
      'Percolate URL',
      array('PercolateImport','urlMetaBox'),
      $postTypeSlug,
      'normal',
      'high'
    );

    add_meta_box(
      'percolate-media',
      'Percolate Media',
      array('PercolateImport','mediaMetaBox'),
      $postTypeSlug,
      'normal',
      'high'
    );

    add_meta_box(
      'percolate-short-url',
      'Percolate Short Url',
      array('PercolateImport','shortUrl'),
      $postTypeSlug,
      'normal',
      'high'
    );

    add_meta_box(
      'percolate_id',
      'Percolate ID',
      array('PercolateImport','percolateId'),
      $postTypeSlug,
      'normal',
      'high'
    );

    add_meta_box(
      'posted_permalink',
      'Posted Permalink to Percolate',
      array('PercolateImport','postedPermalink'),
      $postTypeSlug,
      'normal',
      'high'
    );


    //----------------------
    // Plugin Settings
    //----------------------

    // Setup the settings section
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

    // Only show the API Key first, then once
    // that is set, show the rest of the settings.
    if (get_option(self::APIKEY_OPTION)) {

      add_settings_field(
        self::CHANNEL_ID_OPTION,
        "Website",
        array('PercolateImport', 'settingsChannelIdDisplay'),
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
        "Default License Author",
        array('PercolateImport', 'settingsDefGrpAuthorDisplay'),
        self::SETTINGS_PAGE,
        self::SETTINGS_SECTION
      );

      add_settings_field(
        self::GROUPAUTHORS_OPTION,
        "License Authors",
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

      add_settings_field(
        self::POSTTYPE_OPTION,
        "Post Type",
        array('PercolateImport', 'settingsPostTypeDisplay'),
        self::SETTINGS_PAGE,
        self::SETTINGS_SECTION
      );

      add_settings_field(
        self::IMPORT_INTERVAL_OPTION,
        "Import Interval",
        array('PercolateImport', 'settingsImportInterval'),
        self::SETTINGS_PAGE,
        self::SETTINGS_SECTION
      );

      add_settings_field(
          self::IMPORT_LOAD_IMAGES_OPTION,
          "Load images",
          array('PercolateImport', 'settingsLoadImages'),
          self::SETTINGS_PAGE,
          self::SETTINGS_SECTION
      );


    } // End if APIKEY_OPTION


    // note the use of is_admin() to double check that this is happening in the admin
    if (is_admin() && current_user_can('manage_options') ) {
      $config = array(
        'slug' => plugin_basename(__FILE__),
        'proper_folder_name' => 'WP-Percolate',
        'api_url' => 'https://api.github.com/repos/percolate/WP-Percolate',
        'raw_url' => 'https://raw.github.com/percolate/WP-Percolate/master',
        'github_url' => 'https://github.com/percolate/WP-Percolate',
        'zip_url' => 'https://github.com/percolate/WP-Percolate/zipball/master',
        'sslverify' => false,
        'requires' => "3.2.0",
        'tested' => "3.8.1", //$wp_version
      );
      GLOBAL $gitHubUpdater;
      $gitHubUpdater = new GitHubUpdater($config);
      //reset the transients to allow update checks
      function percolate_check_updates_action_callback(){
        global $gitHubUpdater;
        $gitHubUpdater->delete_transients();
      }
    }

    register_setting(self::SETTINGS_PAGE, self::USERTYPE_OPTION);
    register_setting(self::SETTINGS_PAGE, self::GROUPID_OPTION);
    register_setting(self::SETTINGS_PAGE, self::APIKEY_OPTION);
    register_setting(self::SETTINGS_PAGE, self::DEFGRPAUTHORID_OPTION);
    register_setting(self::SETTINGS_PAGE, self::GROUPAUTHORS_OPTION);
    register_setting(self::SETTINGS_PAGE, self::POSTSTATUS_OPTION);
    register_setting(self::SETTINGS_PAGE, self::AUTHORID_OPTION);
    register_setting(self::SETTINGS_PAGE, self::CATEGORY_OPTION);
    register_setting(self::SETTINGS_PAGE, self::ALLSOURCES_OPTION);
    register_setting(self::SETTINGS_PAGE, self::IMPORT_OVERRIDE_OPTION);
    register_setting(self::SETTINGS_PAGE, self::POSTTYPE_OPTION);
    register_setting(self::SETTINGS_PAGE, self::CHANNEL_ID_OPTION);
    register_setting(self::SETTINGS_PAGE, self::IMPORT_INTERVAL_OPTION, array('PercolateImport', 'sanitizeImportInterval'));
    register_setting(self::SETTINGS_PAGE, self::IMPORT_LOAD_IMAGES_OPTION);
    //Import process
    self::checkImport();

  }

  //----------------------
  // Meta Boxes for Percoalte Posts
  //----------------------

  public function urlMetaBox($post) {
    $url = get_post_meta($post->ID, self::M_URL, true);
    echo $url;
  }


  public function shortUrl($post) {
    $url = get_post_meta($post->ID, self::M_SHORTURL, true);
    echo $url;
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
    if (!empty($mediaMeta[0])) {
    foreach($mediaMeta as $media_js){
      // Get the media type
      //$medias= json_decode($media_js);
      $media=$media_js;
      $mediaType = $media['type'];
      echo "<h4>Type: " . $mediaType . "</h4>";
      echo "<input type='hidden' value='" . $mediaType . "' id='media_type' />";
        if ($mediaType === "image") {
          foreach(array_keys($media['images']) as $size){
            $url=$media['images'][$size]['url'];
            $width=$media['images'][$size]['width'];
            $height=$media['images'][$size]['height'];
            if ($size == 'original') {
              $original_url = $url;
              $original_width = $width;
              $original_height = $height;
            } 
            else {
              echo "<input type='radio' name='image-size' value='$size' style='display:none;'>";
              echo "<img src='$url' id='m_media_{$size}' width='$width' height='$height' class='media_image' size='$size'/>";
              echo "</input><br />";
            }
          }
          //Removing hard-coded image size references
          /*
          $original_url = $media['images']['original']['url'];
          $original_width = $media['images']['original']['width'];
          $original_height = $media['images']['original']['height'];


          // $p_img = $media['src']; //$p_img = $media['p_img']; //apiV3 feature
          $p_img =  $media['images']['large']['url'];
          $p_img_width = $media['images']['large']['width'];//apiV3 feature
          $p_img_height = $media['images']['large']['height']; //apiV3 feature

          echo "<input type='radio' name='image-size' value='large' style='display:none;'>";
          echo "<img src='$p_img' id='m_media' width='$p_img_width' height='$p_img_height' class='media_image' size='large'/>"; //echo "<img src='$p_img' id='m_media' />"; narada
          echo "</input>";
          $p_img_medium_url = $media['images']['medium']['url'];//apiV3 feature
          $p_img_medium_width = $media['images']['medium']['width'];//apiV3 feature
          $p_img_medium_height = $media['images']['medium']['height']; //apiV3 feature

          echo "<input type='radio' name='image-size' value='medium' style='display:none;'>";
          echo "<img src='$p_img_medium_url' id='m_media_m' width='$p_img_medium_width' height='$p_img_medium_height' class='media_image' size='medium' />";
          echo "</input>";
          $p_img_small_url = $media['images']['small']['url'];//apiV3 feature
          $p_img_small_width = $media['images']['small']['width'];//apiV3 feature
          $p_img_small_height = $media['images']['small']['height']; //apiV3 feature

          echo "<input type='radio' name='image-size' value='small' style='display:none;'>";
          echo "<img src='$p_img_small_url' id='m_media_s' width='$p_img_small_width' height='$p_img_small_height' class='media_image' size='small'/>";
          echo "</input>";
          */ 
          if (!empty($original_url)) {
            echo "<br /><input type='radio' id='original-radio' name='image-size' value='original'> Or insert the original image size. ($original_width x $original_height)</input>";
            echo "<img src='$original_url' id='m_media_original' class='media_image' size='original' style='display:none;'/>";
          }
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
          echo '<blockquote>' . $quote_text . '</blockquote><textarea style="width:90%;color:#CCC;" id="m_media_quote"><blockquote>' . $quote_text . '</blockquote></textarea>';
        }
      }

  ?>

  <style>
    img.media_image {
      margin-right:5px;
      border:solid 3px #f9f9f9;
    }
  </style>

  <script type="text/javascript">
      jQuery(function () {
          (function($){

              if(!$("#media_type").val()){
                $('.select-media-button').hide();
              }

              $('.select-media-button').click(function () {

                mType = $("#media_type").val();

                if (mType == 'image' ) {
                  var image_size = $("input[name='image-size']:checked").val();
                  p_img = $("#m_media_"+image_size).attr('src');
                  embedContent = '<img src="'+ p_img +'" alt="" />';
                } else if (mType == 'video') {
                  embedContent = $("textarea#m_media_video").val();
                } else if (mType == 'quote') {
                  embedContent = $("textarea#m_media_quote").val();
                }

                  switchEditors.go('content', 'html');
                  //edInsertContent(edCanvas, embedContent); // This stopped working in 3.3.1
                  send_to_editor(embedContent);
                  switchEditors.go('content', 'tinymce');
              });
          })(jQuery);
      });

      jQuery(document).ready(function () {
        jQuery(".media_image").click(function(){
          jQuery("input[name='image-size']").prop('checked', false);
          jQuery(".media_image").each(function(){
            jQuery(this).css('border', 'solid 3px #f9f9f9');
          });
          jQuery(this).css('border', 'solid 3px #00a6ee');
          var image_size = jQuery(this).attr('size');
          var check_radio = "input[value='" + image_size + "']";
          jQuery(check_radio).prop('checked', true);
          jQuery("input[value='original']").prop('checked', false);
        });
        jQuery("#original-radio").click(function(){
          jQuery(".media_image").each(function(){
            jQuery(this).css('border', 'solid 3px #f9f9f9');
          });
        });
      });

      </script>
     <div class="add-source-input">
      <br /><br />
      <input type="button" class="select-media-button" value="Insert <?php echo $mediaType ?> into post body." />
     </div>
  <?php
  } else {
      echo "<p>There wasn't any media imported from Percolate.</p>";
    }


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
              Single Percolate User


        <br />
        <input type="radio" name="<?php echo self::USERTYPE_OPTION; ?>"
              id="percapi-user-type-group" value="1"
           <?php
    echo $userType == 1 ?  "checked=\"checked\"" : "" ;
?>
              />
              Multiple Percolate Users


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
        </select> When a Wordpress author does not exist for a corresponding license author, assign posts to this Wordpress user.
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
            License Authors
                    <a href="#" id="refresh_memberform">refresh</a>

          </th>
          <th>
            Wordpress Authors
          </th>
         </tr>
    <?php

    $group_id = get_option(self::GROUPID_OPTION);
    $group_authors_array = json_decode($group_authors);

    //if($group_id>0){

      $percolate_users = self::getGroupUsers($group_id);

      $objects = $percolate_users['users'];

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

    // }else{
    //  echo "Enter a group id and save to view the group users.";
    // }
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

    public function settingsPostTypeDisplay()
    {
        $postTypeID = get_option(self::POSTTYPE_OPTION);
        ?>
        <select name="percolateimport_posttype">
            <?php
            $args=array(
                'public'   => true,
            );
            $output = 'names'; // names or objects, note names is the default
            $operator = 'and'; // 'and' or 'or'
            $post_types=get_post_types($args,$output,$operator);
            foreach ($post_types as $post_type ) {
                echo '<option '. ($post_type == $postTypeID ? 'selected="selected"' : '') .
                     'value="'. $post_type. '">'. $post_type. '</option>';
            }

            ?>
        </select> New posts imported from percolate will be set to this post type
        <?php
    }

    public function settingsImportInterval()
    {
    ?>
      <span>
        <input class="small-text" type="number" min="<?php echo self::MIN_IMPORT_INTERVAL; ?>" step="1" name="<?php echo self::IMPORT_INTERVAL_OPTION; ?>" value="<?php echo get_option(self::IMPORT_INTERVAL_OPTION); ?>" >
      </span> Minimum <?php echo self::MIN_IMPORT_INTERVAL; ?> seconds.
    <?php
    }

    public function sanitizeImportInterval($value)
    {
      if ($value < self::MIN_IMPORT_INTERVAL) {
          return self::MIN_IMPORT_INTERVAL;
      }
      return $value;
    }


    public function settingsLoadImages()
    {
      $loadImages = get_option(self::IMPORT_LOAD_IMAGES_OPTION);
      ?>
      <span class="percapi-load-images">
    <input type="checkbox" name="<?php echo self::IMPORT_LOAD_IMAGES_OPTION; ?>"
           id="percapi-load-images"  value="<?php echo (int)$loadImages; ?>"
        <?php
        // percolateimport_load_images
        echo (int)$loadImages ?  "checked=\"checked\"" :  "" ;
        ?> />
          Download Percolate images to Media Library

  <input type="hidden" name="<?php echo self::IMPORT_LOAD_IMAGES_OPTION; ?>" id="init_load_images" value="<?php echo $loadImages;?>">
</span>
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

  public static function apiKeyNotice()
  {
    if (get_option(self::APIKEY_OPTION)) {
      return;
    }

    echo '<div class="error">';
    echo '<p>';
    echo '<strong>' . __('Notice:') . '</strong> ';
    _e(
      "You haven't entered your Percolate API Key yet. "
    );
    printf(
      '<a href="%s">' . __('Percolate Settings Page') . '</a>',
      admin_url('options-general.php?page=percolate')
    );
    echo '</p></div>';
  }


  public static function channelIdNotice()
  {
    if (!get_option(self::CHANNEL_ID_OPTION) && get_option(self::APIKEY_OPTION)) {

      echo '<div class="error">';
      echo '<p>';
      echo '<strong>' . __('Notice:') . '</strong> ';
      _e(
        "Please set which site are you publishing to. The Percolate plugin will not work until you set this."
      );
      printf(' <a href="%s">' . __('Go to the Percolate settings page') . '</a>', admin_url('options-general.php?page=percolate'));
      echo '</p></div>';

    }
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

    public function settingsChannelIdDisplay()
    {

        $result = self::getUserProfile();
        $default_license_id = $result['default_license_id'];
        if ($default_license_id){
            $result = self::getChannels($default_license_id);
            if(!$result){
                ?>
                <span class="percapi-channel-id-control channel-id">

                <?php
                echo '<select name="' . self::CHANNEL_ID_OPTION . '">';
                    echo '<option value="0">Choose a Channel</option>';

                    ?>
                    </select>
                </span>
                <?php

            }else{
                $channels = array();
                foreach ($result['data'] as $channel){

                    // Only return "public" sites, these are our .com sites. We don't want
                    // to show tumblr or other channels like that.
                    if ($channel['type'] == "public") {
                      array_push($channels, array('id' => $channel['id'], 'name' =>$channel['name']));
                    }
                }
                if($channels){
                    //error_log(serialize($channels));
                    //error_log(get_option(self::CHANNEL_ID_OPTION));
                    ?>
                    <span class="percapi-channel-id-control channel-id">

                      <div class="error fn-channel-error" style="display:none;">
                        <p>Please select which website you're publishing to.</p>
                      </div>

                        <?php
                            echo '<select class="fn-channel-id" name="' . self::CHANNEL_ID_OPTION . '">';
                            if(get_option(self::CHANNEL_ID_OPTION)=='0'){
                                echo '<option disabled="true" selected="selected">Choose a Channel...</option>';
                            }else{
                                echo '<option disabled="true" selected="selected">Choose a Channel...</option>';

                            }

                            foreach($channels as $channel_option){
                                if (get_option(self::CHANNEL_ID_OPTION) ==  $channel_option['id']){
                                    echo '<option selected="selected" value="' . $channel_option['id'] . '">' . $channel_option['name'] . "</option>";

                                }else{
                                    echo '<option value="' . $channel_option['id'] . '">' . $channel_option['name'] . "</option>";
                                }
                            }
                        ?>
                        </select>
                        <?php
                          if (get_option(self::CHANNEL_ID_OPTION)) {
                            echo '<span class="fn-channel-error-help">Which site are you publishing to.</span>';
                          } else {
                            echo '<span class="fn-channel-error-help inline-error">Which site are you publishing to.</span>';
                          }
                        ?>
                    </span>
                    <?php

                }else{

                }
            }
        }else{

        }
    }

  public static function adminMenu()
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
      $last_id = get_option(self::STARTID_OPTION);?>
        <div id="stories_imported" class="updated settings-error">
        <p>
            <strong>Stories Imported. Most Recent Percolate ID: <?php echo ($last_id); ?></strong>
        </p>
        </div>
        <?php
      update_option(self::IMPORT_OVERRIDE_OPTION, 0);

    // If we are updating the settings, lets get any updated info from the api for this user.
    } else if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true'){

        $result = self::getUserProfile();
        $user_profile_id = $result['id'];
        update_option(self::USERID_OPTION, $user_profile_id);
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
  public static function importStories()
  {

    $limit = 30;
    $offset = 0; //for initial step, this wil update within following loop
    $total = 1; //for initial step, this wil update within following loop

    // Ed.C 10-19-2012 Commenting out loop so users import most 30 recent posts
    // TODO Add condition check to import all posts
    //while( intval($offset) < intval($total) ) {

      $posts = self::getPercolatePosts($offset, $limit);

      $pagination = $posts['pagination'];
      $total = $pagination['total'];
      $offset = intval($offset) + 30;

      $objects = $posts['data'];
      //$last_startId = get_option(self::STARTID_OPTION);

      // Check to see if the last_id coming from the percolate API is larger than that is what is
      // stored in the wp db, if its smaller than something is wrong and we don't update the start_at_id and don't
      // do the import.

        if ($objects) {
          // update the startID with the last id that was imported.
          $startId = $objects[0]['id'];
          update_option(self::STARTID_OPTION, $startId);

          foreach ($objects as $object) {

            if ($object['external'] == false) {
              $startId = $object['id'];

              //if(intval($last_startId) < intval($startId)) {
               self::importStory($object);

              //}
            }

          }

        }
    //}

  }

  /**
   * Add a percolate story to the WP system as a post
   **/
  public static function importStory($object)
  {
    global $wpdb;

    $custom_title = NULL;
    $custom_body = NULL;
    $publish_date = NULL;

    // get custom attributes for post matching channel ID
    foreach($object['schedules'] as $post){

      if ($post['channel_id'] == get_option(self::CHANNEL_ID_OPTION)){
        $custom_title = $post['title'];
        $custom_body = $post['body'];
        $publish_date = $post['published_at'];
      }
    }

    $tags_array =  $object['tags'];
    $body = trim($custom_body) ? $custom_body : $object['body'];
    $post_title = trim($custom_title) ? $custom_title : $object['title'];
    $analytics_array = $object['analytics'];
    $short_url = $object['short_url'];
    $link_array =  $object['link'];
    $url_array = $link_array['url'];
    //$media_array = $object['media'];
    $percolate_id = $object['id'];
    $linkId = $object['id'];

    // We use this to check for posts that are already imported.
    // TODO: Find a better way to do this
    $posts = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT meta_id, post_id, meta_key, meta_value
                FROM $wpdb->postmeta
                WHERE meta_value = %s AND (meta_key='percolate_id')",
        $percolate_id
      )
    );

    if ($posts) {
      return false;
    }

    $post = array();
    $post['post_title']=html_entity_decode($post_title);

    if (!trim($post['post_title'])) {
      $post['post_title']=html_entity_decode($link_array['title']);
    }

    // if need load images from remote percolate server into local media library
    $img = '';
    $newObject = $object;
    $loadImages = get_option(self::IMPORT_LOAD_IMAGES_OPTION);
    if((int)$loadImages) {
      $newObject = self::insertImageIntoMediaLib(0, $object); // $postId
      foreach($newObject['media']['images'] as $key => $image)
      {
          if(!is_numeric($key) && $key == 'original')
          {
             $img .= "<p><img src=\"{$image['url']}\"/></p>";
          } else {
            if(isset($image['oldSrc']))
              $body = str_replace($image['oldSrc'], $image['url'], $body);
          }
      }
    }

    $media_array = $newObject['media'];
    $img = (empty($img)) ? '' : $img;
    $post['post_content'] = $body.$img;
    $postName = sanitize_title($post['post_title']);
    $post['post_name']=$postName;
    $offset =  get_option('gmt_offset');
    // utc timezone adjustment if there is an offset set in wordpress.

    // Trying to fix 1970 bug
    if ($publish_date == NULL){
      foreach($object['schedules'] as $schedule){
        if ($schedule['published_at'] != NULL){
          $publish_date = $schedule['published_at'];
        }
      }
    }

    // Still trying to fix 1970 bug
    if ($publish_date == NULL){
      $date = $object['created_at']." ".$offset." hours";
    }
    else{
      $date = $publish_date;
    }

    $date = strtotime($date);
    $post['post_date'] = date('Y-m-d H:i:s', $date);
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

    $post['post_type'] = get_option(self::POSTTYPE_OPTION);
    $postId = wp_insert_post($post);
    if ($tags_array) {
      $tags_s = "";
      foreach($tags_array as $tag){
        $tags_s = $tags_s.$tag['tag'] .",";
      }
      wp_set_post_tags($postId, $tags_s);
    }

    $ver = floatval(phpversion());


    // updates post meta

    update_post_meta($postId, self::M_DOMAIN, parse_url($url_array, PHP_URL_HOST));

    update_post_meta($postId, self::M_ADDEDON, strtotime($object['created_at']));
    update_post_meta($postId, self::M_LINKID, $link_array['id']);
    update_post_meta($postId, self::M_ORIGINALDESCRIPTION, html_entity_decode($link_array['description']));
    update_post_meta($postId, self::M_ORIGINALTITLE, html_entity_decode($link_array['title']));
    update_post_meta($postId, self::M_URL, $url_array);
    update_post_meta($postId, self::M_SHORTURL, $short_url);
    update_post_meta($postId, self::M_USERDESCRIPTION, html_entity_decode($link_array['description']));
    update_post_meta($postId, self::M_USERTITLE, $post_title);
    update_post_meta($postId, self::M_PERCOLATEID, $percolate_id);

    if (isset($media_array['type']))
    {
      // v2 Compatibility, copy the large image to p_img at the root of the the media_array.
      if ($media_array['type'] == 'image') {
        $media_array['p_img'] = $media_array['images']['large']['url'];
        $media_array['height'] = $media_array['images']['large']['height'];
        $media_array['width'] = $media_array['images']['large']['width'];
      } else if ($media_array['type'] == 'video') {
        $media_array['t_img'] = $media_array['images'][1]['url'];
      }
    }


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

  /**
   *  Insert image into media library
   */
  protected static function insertImageIntoMediaLib($post_id, $object)
  {
    $body = $object['body'];
    $media = $object['media'];
    $images = $media['images'];
    $metadata = $media['metadata'];

    // initializing an empty imagesSizes array
    // so we don't try to process sizes that are only there for attachments
    $imageSizes = array();
    if (isset($metadata['original_filename']) || (isset($metadata['is_photo']) && ($metadata['is_photo'] == 1) && isset($images['original']) && isset($images['original']['url']))){
      if (isset($metadata['original_filename'])){
        $file = $metadata['original_filename'];
      } else {
        $file = $images['original']['url'];
      }

      // overwrite the initial empty imageSizes array with built in sizes used by attachments
      $imageSizes = array_keys($images);
    } else {
      // we have no attachments
      // no longer throwing an exception here
      // so we can handle posts with body images but no attachments
      //throw new Exception("No original filename");
    }

    $bodyImages = self::extractBodyImagesSrc($body);
    $cnt = count($bodyImages);
    foreach($bodyImages as $key => $bodyImage) {
        $imageSizes[count($imageSizes)] = $key;
        $object['media']['images'][$key]['url'] = $bodyImage['src'];
        $object['media']['images'][$key]['oldSrc'] = $bodyImage['oldSrc'];
        //$images[$key]['width']  = $bodyImage['width'];
        //$images[$key]['height'] = $bodyImage['height'];
    }

    foreach($imageSizes as $image)
    {
      if (isset($object['media']['images'][$image])){
        $src = $object['media']['images'][$image]['url'];
      } else {
        continue;
      }

      // Get uploads dir
      if (!(($uploads = wp_upload_dir()) && false === $uploads['error']))
        return new WP_Error('upload_error', $uploads['error']);

      if(preg_match("/^(.*)\.(\w+)$/", $file, $matches) && count($matches))
         $sizeFilename = $matches[1].'_'.$image.'.'.$matches[2];
      else
         $sizeFilename = $file;

      // get unique file name
      $filename = wp_unique_filename($uploads['path'], basename($sizeFilename));
      $filepath = $uploads['path'] . '/' . $filename;

      $url = '';

      if (self::getImageFromServer($src, $filepath)) {
        // Compute the URL
        $url = $uploads['url'] . '/' . $filename;
        $object['media']['images'][$image]['url'] = $url;

        $type = mime_content_type($filepath);

        $title = preg_replace('!\.[^.]+$!', '', basename($file));
        $content = $title; //'';

        // use image exif/iptc data for title and caption defaults if possible
        if ($image_meta = @wp_read_image_metadata($filepath)) {
          if ('' != trim($image_meta['title']))
            $title = trim($image_meta['title']);
          if ('' != trim($image_meta['caption']))
            $content = trim($image_meta['caption']);
        }

        $time = gmdate('Y-m-d H:i:s', @filemtime($filepath));

        if ($time) {
          $post_date_gmt = $time;
          $post_date = $time;
        } else {
          $post_date = current_time('mysql');
          $post_date_gmt = current_time('mysql', 1);
        }

        // Construct the attachment array
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => 0, //$post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
        );

        $filepath = $uploads['path'] .'/'. $filename;
        //Win32 fix:
        $filepath = str_replace(strtolower(str_replace('\\', '/', $uploads['path'])), $uploads['path'], $filepath);
        // Save the data
        $id = wp_insert_attachment($attachment, $filepath, $post_id);
        if (!is_wp_error($id)) {
           $data = wp_generate_attachment_metadata($id, $filepath);
           wp_update_attachment_metadata($id, $data);
        }
      } else {
        //throw new Exception('Sorry, cannot upload file '.$filepath);
        //return false;
        new WP_Error('upload_error', 'Sorry, cannot upload file ' . $filepath);
      }
    }

    return $object;
  }

  /*
   * Extract body images src
   */
  public static function extractBodyImagesSrc($body)
  {
      $img = array();

      $doc = new DOMDocument();
      @$doc->loadHTML($body);

      $tags = $doc->getElementsByTagName('img');

      foreach ($tags as $key => $tag) {
          $img[$key]['src'] = $tag->getAttribute('src');
          $img[$key]['oldSrc'] = $img[$key]['src'];
          //$img[]['width'] = $tag->getAttribute('width');
          //$img[]['height'] = $tag->getAttribute('height');
      }

      unset($tag);
      unset($doc);

      return $img;
  }

  public static function getUrl($postId)
  {
    $url = get_post_meta($postId, self::M_URL, true);

    return $url;
  }

  /**
   * Check to see if stories have been imported recently
   */
  public static function checkImport()
  {

    if ( ((time() - get_option(self::LASTIMPORT_OPTION)) > self::IMPORT_INTERVAL) || get_option( self::IMPORT_OVERRIDE_OPTION ) == 1 ) {
      try{
        // update the lastimport here so we reset it even if posts aren't imported.
        update_option(self::LASTIMPORT_OPTION, time());

        // And try the import
        self::importStories();
      }

      catch (Exception $e)
      {

      }
    }
  }

  public static function getPercolatePosts($offset,$limit) {

    $apiKey = get_option(self::APIKEY_OPTION);

    if($apiKey){
        }else{
      //no api key return empty array for now
            error_log("no api key");
      return array();
    }

        $result = self::getUserProfile();
        $default_license_id = $result['default_license_id'];
        if ($default_license_id){
        }else{
            error_log("no default license id");
            return array();
        }

        $options['api_key'] =  $apiKey;


        $channel_id = get_option(self::CHANNEL_ID_OPTION);
        if($channel_id){
        }else{
            error_log("no channel id");
            return array();
        }
        $method = "licenses/" . $default_license_id . "/posts";



        $options['channel_id'] = $channel_id;
        $options['statuses'] = 'published'; //apiV3 feature
        //$options['service_ids'] = '12'; // .com service - apiV3 feature
        $options['order_direction'] = 'desc'; //apiV3 feature
        $options['limit'] = $limit; //apiV3 feature
        $options['offset'] = $offset; //apiV3 feature


    return self::callPercolateApi($method , $options);
  }


    public static function getUserProfile() {

        $apiKey = get_option(self::APIKEY_OPTION);

        if($apiKey){
            $options['api_key'] = $apiKey;
            $method = "me";
        }else{
            error_log('no api key');
            return array();
        }
        try {
            return self::callPercolateApi($method , $options);
        } catch (Exception $e) {
            return array();
        }
    }



    public function getChannels($default_license_id) {

        $apiKey = get_option(self::APIKEY_OPTION);

        if($apiKey){
            $options['api_key'] = $apiKey;
        }else{
            //no api key return empty array for now
            return array();
        }





        $method = "licenses/" . $default_license_id . "/publishing/channels";

        try {
            return self::callPercolateApi($method , $options);
        } catch (Exception $e) {
            return array();
        }
    }

  //get group users
  public function getGroupUsers($groupId){
        $result = self::getUserProfile();
        $default_license_id = $result['default_license_id'];
        if ($default_license_id){

        }
        else{
            error_log('no default license');
            return array();
        }

        $apiKey = get_option(self::APIKEY_OPTION);

        if($apiKey){
            $options['api_key'] = $apiKey;
        }else{
            error_log('no api key');
            return array();
        }

        $user_id = get_option(self::USERID_OPTION);

        if($user_id){

        }else{
            error_log('no user id');
            return array();
        }

        if ($user_id and $apiKey){
            $options['api_key'] =  $apiKey;
        }
        else{
            return array();
        }



    $method = 'licenses/'.$default_license_id;
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

    // Add the channel id to the published public endpoint
    $method = "publish/public";

    try {
      self::callPercolateApi($method, $options, $jsonFields);
    } catch (Exception $e) {
      // error
    }

    return true;

  }

  // Post the wordpress permalink back to percolate.
  public function permalink_post_back($postId) {

      $perc_permalink = get_post_meta($postId, self::M_POSTEDPERMALINK, true);
      $percolate_id = get_post_meta($postId, self::M_PERCOLATEID, true);
      $channel_id = get_option(self::CHANNEL_ID_OPTION);

      self::postToPercolate( array("channel_id" => $channel_id, "post_id" => $percolate_id, "permalink" => urlencode($perc_permalink)) );

  }

  protected static function callPercolateApi($method, $fields=array(), $jsonFields=array())
  {
    // If PERCOLATE_API_BASE is defined in wp-config.php, use it instead of API_BASE
    if (defined('PERCOLATE_API_BASE')) {
      $url =  constant('PERCOLATE_API_BASE') . "$method";
    }
    else{
      $url = self::API_BASE . "$method";
    }



      if ($fields) {
        $tokens = array();
        foreach ($fields as $key=>$val) {
          $tokens[]="$key=$val";
        }
        $url.="?" . implode('&', $tokens);
      }


      //if (WP_DEBUG) {
        //error_log("------ Calling API ------ \nURL: ".$url."\n", 0);
        //}


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

  // get image from percolate server
  protected static function getImageFromServer($src, $filename)
  {
    /* get image by url */
    $curl_handle = curl_init($src);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_HEADER, 0);

    $f = fopen($filename, 'w');

    if($f !== false)
    {
      curl_setopt($curl_handle, CURLOPT_FILE, $f);
      curl_exec($curl_handle);
      fclose($f);

      // Set correct file permissions
      $stat = stat(dirname($filename));
      $perms = $stat['mode'] & 0000666;
      @ chmod( $filename, $perms );

      $res = true;

    } else {
      throw new Exception("fopen error for filename {$filename}");
    }

    $status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

    curl_close($curl_handle);

    if ($status != 200) {

      $message = "An unknown error occurred communicating with Percolate ($status)";

      $res = false;

      throw new Exception($message, $status);
    }

    return $res;

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
    $timestamp = wp_next_scheduled( 'percolate_import_event');
    wp_clear_scheduled_hook($timestamp, 'minute', 'percolate_import_event');
  }

  static function scheduleImport( $schedules ) {
    $interval = get_option( self::IMPORT_INTERVAL_OPTION);
    $schedules['minute'] = array(
      'interval' => $interval,
      'display' => __('Once every '. $interval . ' seconds')
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
//add_action('admin_notices', array('PercolateImport','userIdNotice'));
add_action('admin_notices', array('PercolateImport','apiKeyNotice'));
add_action('admin_notices', array('PercolateImport','channelIdNotice'));
add_action('publish_post', array('PercolateImport','permalink_post_back')); //apiV3 feature


// The plugin github updater
include_once('updater.php');

?>
