<?php
/**
 * @package Percolate_Import
 * @version 1.0
 */
/*
Plugin Name: WP Percolate
Plugin URI: http://percolate.org
Description: This plugin turns Percolate posts into Wordpress entries.
Author: Percolate Industries, Inc.
Version: 1.0
Author URI: http://percolate.org
*/

class PercolateImport
{
    const SETTINGS_SECTION='percolate_api_settings';
    const SETTINGS_PAGE='percolate';
    
    const IMPORT_INTERVAL=300;
    
    const USERID_OPTION='percolateimport_userid';
    const AUTHORID_OPTION='percolateimport_authorid';
    const LASTIMPORT_OPTION='percolateimport_lastimported';
    const LASTID_OPTION='percolateimport_lastid';
    const POSTSTATUS_OPTION='percolateimport_poststatus';
    const CATEGORY_OPTION='percolate_category';
	const EX_CATEGORY_OPTION='ex_percolate_category';
    const ALLSOURCES_OPTION='percolate_allsources';
    
    const IMPORT_OVERRIDE_OPTION='percolateimport_override';
    
    const API_BASE='http://percolate.com/api/v1/';
    
    const M_LINKID='percolate_link_id';
    const M_ADDEDON='percolate_added_on';
    const M_ORIGINALTITLE='percolate_original_title';
    const M_USERTITLE='percolate_user_title';
    const M_DOMAIN='percolate_domain';
    const M_ORIGINALDESCRIPTION='percolate_original_description';
    const M_USERDESCRIPTION='percolate_user_description';
    const M_URL='percolate_url';
    const M_SOURCES='percolate_sources';
    const M_FEATUREDSOURCE='percolate_featured_source';
    const M_SOURCETITLES='percolate_source_titles';
    const M_USE='percolate_use';
    const IMPORT_VERSION = '1.0';
    
    /** INSTALL AND INIT CODE **/
    
    public function install()
    {
        update_option(self::USERID_OPTION, '0');
        update_option(self::LASTIMPORT_OPTION, 0);
        update_option(self::POSTSTATUS_OPTION, 'publish');
        update_option(self::ALLSOURCES_OPTION, 0);
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
        echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') .
            '/wp-content/plugins/WP-Percolate/percimport.js"></script>';
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
            'percolate-domain',
            'Percolate Via',
            array('PercolateImport','domainMetaBox'),
            'post',
            'normal',
            'high'
        );        
        add_meta_box(
            'percolate-sources',
            'Percolate Sources',
            array('PercolateImport','sourcesMetaBox'),
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
            self::USERID_OPTION,
            "Percolate User ID",
            array('PercolateImport', 'settingsUserIdDisplay'),
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
            self::CATEGORY_OPTION,
            "Category",
            array('PercolateImport', 'settingsCategoryDisplay'),
            self::SETTINGS_PAGE,
            self::SETTINGS_SECTION
        );
        

		add_settings_field(
            self::EX_CATEGORY_OPTION,
            "Exclude Category",
            array('PercolateImport', 'settingsExcludeCategoryDisplay'),
            self::SETTINGS_PAGE,
            self::SETTINGS_SECTION
        );

        add_settings_field(
            self::ALLSOURCES_OPTION,
            "Import All Sources?",
            array('PercolateImport', 'settingsAllSourcesDisplay'),
            self::SETTINGS_PAGE,
            self::SETTINGS_SECTION
        );        
        
        register_setting(self::SETTINGS_PAGE, self::USERID_OPTION);
        register_setting(self::SETTINGS_PAGE, self::POSTSTATUS_OPTION);
        register_setting(self::SETTINGS_PAGE, self::AUTHORID_OPTION);
        register_setting(self::SETTINGS_PAGE, self::CATEGORY_OPTION);
		register_setting(self::SETTINGS_PAGE, self::EX_CATEGORY_OPTION);
        register_setting(self::SETTINGS_PAGE, self::ALLSOURCES_OPTION);
    		register_setting(self::SETTINGS_PAGE, self::IMPORT_OVERRIDE_OPTION);
        
        //Import process
        self::checkImport();
    
    // self::checkUpdate();
    }
    
    /** POST META BOXES **/
    public function urlMetaBox($post)
    {
        $url = get_post_meta($post->ID, self::M_URL, true);
        ?>
        <input type="text" style="width:99%;" name="<?php echo self::M_URL; ?>" value="<?php echo $url; ?>" />
        <?php
    }
    
    public function domainMetaBox($post)
    {
        $domain = get_post_meta($post->ID, self::M_DOMAIN, true);
        $url = get_post_meta($post->ID, self::M_URL, true);
        ?>
        <input type="text" style="width:99%;" name="<?php echo self::M_DOMAIN; ?>" value="<?php echo $domain; ?>" />
        <?php
    }
    
    public function infoMetaBox($post)
    {
        $userTitle = get_post_meta($post->ID, self::M_USERTITLE, true);
        $originalTitle = get_post_meta($post->ID, self::M_ORIGINALTITLE, true);
        $userDescription = get_post_meta($post->ID, self::M_USERDESCRIPTION, true);
        $originalDescription = get_post_meta($post->ID, self::M_ORIGINALDESCRIPTION, true);
        $linkId = get_post_meta($post->ID, self::M_LINKID, true);
        if (!$linkId) {
            echo '<p class="nonessential">Not a Percolate post.</p>';
            return;
        }
        ?>
        <?php echo $originalDescription; ?>
        <?php
    }
    
    public function sourcesMetaBox($post)
    {
        $sourceMeta = get_post_meta($post->ID, self::M_SOURCES);
        
        $sources = array();
        
        if ($sourceMeta) {
            $sources = json_decode($sourceMeta[0], true);
        }
        
        $featuredSource = get_post_meta($post->ID, self::M_FEATUREDSOURCE, true);
        $featuredSource = $featuredSource ? $featuredSource : 0;
        $useSources = json_decode(get_post_meta($post->ID, self::M_USE, true), true);
        $sourceTitles = json_decode(get_post_meta($post->ID, self::M_SOURCETITLES, true), true);
        
        if (!is_array($sourceTitles)) {
            $sourceTitles = array();
        }
        // echo "<pre>X:"; print_r(get_post_meta($post->ID, self::M_SOURCETITLES, true)); echo "</pre>";
        // echo "<pre>use:"; print_r($useSources); echo "</pre>";
        // echo "<pre>title:"; print_r($sourceTitles); echo "</pre>";
        // echo "<pre>"; print_r($sources); echo "</pre>";
        ?>
        <script type="text/javascript">
        jQuery(function () {
            (function($){
                    
                $('.add-source-button').click(function () {
                    var table = $('.sources-table',$(this).closest('.postbox')),
                        rowCount = $('tr', table).length,
                        row = $('<tr>' +
                        '<td><input type="radio" ' +
                        'name="<?php echo self::M_FEATUREDSOURCE; ?>" value="' + (rowCount - 1) + '" /></td>' + 
                        '<td><input type="checkbox" checked="checked" ' +
                        'name="<?php echo self::M_USE;?>[]" value="new_' +
                        rowCount + '" /></td>' + 
                        '<td style="width:99%">Title: <input type="text" ' +
                        'class="title-input percolate-required" ' +
                        'name="<?php echo self::M_SOURCETITLES; ?>[new_' +
                        rowCount + ']" value="" style="width:80%" />' +
                        '<br />URL:<input type="text" class="percolate-required" name="percolate_sourceurl[new_' +
                        rowCount + ']" style="width:80%" /><br /><span class="percolate-error"></span></td>' +
                        '</tr>');
                    
                    $(table).append(row);
                    $('.title-input', row).focus();
                    
                    $('.percolate-required', row).change(function () {
                        var valid = true;
                        jQuery('.percolate-required', jQuery(this).closest('td')).each(function () {
                            if(!jQuery(this).val())
                            {
                                valid = false;
                            }                            
                        });
                        
                        if (valid) {
                            jQuery('.percolate-error',$(this).closest('td'))
                                .removeClass('error')
                                .html('');
                        } else {  
                            jQuery('.percolate-error',$(this).closest('td'))
                                .addClass('error')
                                .html('<small>Please enter both a URL and a title.</small>');
                        }
                    });
                    
                });
            })(jQuery);
        });
        </script>
        <input type="hidden" name="percolate_postid" value="<?php echo $post->ID; ?>" />
        <table class="form-table sources-table">
            <tr>
                <th>Featured</th>
                <th>Use</th>
                <th>Source</th>
            </tr>
            <?php if(is_array($sources)): ?>
            <?php foreach ($sources as $idx=>$source):
                $subid = $source['source_subscription_id'];
						// echo print_r($source);
            ?>
            <tr>
                <td>
                    <input <?php if($idx == $featuredSource): echo 'checked="checked"'; endif; ?>
                        type="radio"
                        name="<?php echo self::M_FEATUREDSOURCE; ?>"
                        value="<?php echo $idx; ?>" />
                </td>
                <td>
                    <input type="checkbox"
                        <?php if(in_array($subid, $useSources)): echo 'checked="checked"'; endif; ?>
                        name="<?php echo self::M_USE; ?>[]"
                        value="<?php echo $subid; ?>" />
                </td>
                <td style="width: 99%;">
                    <input type="text"
                       name="<?php echo self::M_SOURCETITLES; ?>[<?php echo $subid; ?>]"
                       value="<?php
                            echo array_key_exists($subid, $sourceTitles) ? empty($sourceTitles[$subid]) ? htmlentities($source['source_entry_title']) : htmlentities($sourceTitles[$subid]) : htmlentities($source['source_entry_title']) ;
                        ?>"
                       style="width: 99%;" />
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
        <div class="add-source-input">
            <input type="button" class="add-source-button" value="Add Source" />
        </div>
        <?php
        //echo "<pre>"; print_r($sources); echo "</pre>";
    }
    
    public function updatePost($postId)
    {
        
        if (wp_is_post_revision($postId)) {
            return;
        }
        
        if (!empty($_POST['percolate_sourceurl'])) {
            $sourceMeta = get_post_meta($postId, self::M_SOURCES, true);

            $sources = json_decode($sourceMeta, true);
            
            if (!is_array($sources)) {
                $sources = array(); 
            }
            
            $newSources = $_POST['percolate_sourceurl'];
            
            $titles = $_POST[self::M_SOURCETITLES];
            
            foreach ($newSources as $id=>$url) {
                if (empty($url)) {
                    continue;
                }
                
                if (empty($titles[$id])) {
                    $titles[$id]='Source';
                }
                
                $newSource = array(
                    'source_subscription_id'=>$id,
                    'source_url'=>$url,
                    'source_entry_title'=>$titles[$id],
                    'is_twitter'=>(strpos($url, 'twitter.com') > -1) ? 1 : 0
                );
                
                $sources[]=$newSource;
            }

            update_post_meta($postId, self::M_SOURCES, json_encode($sources));
        }
        if (!empty($_POST[self::M_DOMAIN])) {
            update_post_meta($postId, self::M_DOMAIN, $_POST[self::M_DOMAIN]);
        }
        if (!empty($_POST[self::M_FEATUREDSOURCE])) {
            update_post_meta($postId, self::M_FEATUREDSOURCE, intval($_POST[self::M_FEATUREDSOURCE]));
        }
        if (!empty($_POST[self::M_URL])) {
            update_post_meta($postId, self::M_URL, $_POST[self::M_URL]);
        }
        if (!empty($_POST[self::M_USE])) {
            update_post_meta($postId, self::M_USE, json_encode($_POST[self::M_USE]));
        }

        if (!empty($_POST[self::M_SOURCETITLES])) {
            update_post_meta($postId, self::M_SOURCETITLES, json_encode($_POST[self::M_SOURCETITLES]));
        }
    }
    
    public function suppressCustomMeta($postType, $post = null)
    {
        remove_meta_box('postcustom', 'post', 'advanced');
    }
    
    /** SETTINGS **/
    public function settingsSectionHeader()
    {
        echo "<p>Settings for Percolate API Integration</p>";
    }
    
    public function settingsUserIdDisplay()
    {
        ?>
        <script type="text/javascript">
        jQuery(function() {
            (function ($){
                $('.percapi-userid-control').append(
                    '<br />Find User ID by user name: ' +
                    '<input type="text" id="percapi_username" size="10" />' +
                    '<input type="button" id="percapi_submit" value="Find by username" />'
                );
                
                $('#percapi_submit').click(function () {
                    var uname=$('#percapi_username').val();
                    $.getJSON(
                        'http://percolate.com/api/v1/user_id?callback=?',
                        {username: uname},
                        function (result){
                            $('#percapi_user_id').val(result.user_id);
                            $('#percapi_username').val('');
                        });
                    return false;
                });
            
            })(jQuery);
        });            
        </script>
    
        <span class="percapi-userid-control">
        <input size="5" type="text" name="<?php echo self::USERID_OPTION; ?>"
            id="percapi_user_id"
            value="<?php echo get_option(self::USERID_OPTION != '0' ? self::USERID_OPTION : ''); ?>" />
            User ID for the Percolate API.
        </span>
        <?php
    }
    
    
    public function settingsAuthorDisplay()
    {
        $users = get_users();
        $authorId = get_option(self::AUTHORID_OPTION);
        //echo "<pre>"; print_r($users); echo "</pre>";
        ?>
        <select name="<?php echo self::AUTHORID_OPTION; ?>">
            <option></option>
            <?php foreach ($users as $user): ?>
            <option <?php echo ($user->ID == $authorId) ? ' selected="selected" ' : ''; ?>
                value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
            <?php endforeach; ?>
        </select> New posts imported from percolate will be owned by this user.
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

    public function settingsAllSourcesDisplay()
    {
        // Get the All Sources Value
    		$allSources = get_option(self::ALLSOURCES_OPTION);
        ?>
     
        <span class="percapi-allsources">
        <input type="checkbox" name="<?php echo self::ALLSOURCES_OPTION; ?>"
            id="percapi_allsources"
            value="1" 
            <?php if ($allSources == 1) { echo("checked='checked'");} ?> />
            Yes
        </span>    
    
    
        <?php
    }    
    
    
    
    public function userIdNotice()
    {
        if (get_option(self::USERID_OPTION)) {
            return;
        }
        
        echo '<div class="error">';
        echo '<p>';
        echo '<strong>' . __('Notice:') . '</strong> ';
        _e(
            'You haven&rsquo;t set a Percolate User ID for the Percolate
            Import plugin. This plugin will not function correctly
            until the user ID is set.'
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
    if( $_REQUEST['settings-updated'] == 'true' && get_option(PercolateImport::IMPORT_OVERRIDE_OPTION) == 1 )
    {
        $last_id = get_option(self::LASTID_OPTION);
        ?>
        <div id="stories_imported" class="updated settings-error">
        <p>
            <strong>Stories Imported. Last id: <?php echo ($last_id); ?></strong>
        </p>
        </div>
        <?php
        update_option(self::IMPORT_OVERRIDE_OPTION, 0);
    }
    
        load_template(ABSPATH . 'wp-content/plugins/WP-Percolate/percolate-options.php');
    }
    
    
    /** IMPORT SCREEN **/
    public function renderImportPage()
    {
        load_template(ABSPATH . 'wp-content/plugins/WP-Percolate/admin-import-page.php');
    }
    
    /** PERCOLATE API **/
    
    /**
     * Call the percolate API and try to import stories
     */
    public function importStories()
    {
        $stories = self::getPercolateStories();
        //echo "<pre>|stories:"; print_r($stories); echo "</pre>";
        $lastId = get_option(self::LASTID_OPTION);
        if ($stories) {
            foreach ($stories as $story) {
                self::importStory($story);
                if ($story['entry_id'] > $lastId) {
                    $lastId = $story['entry_id'];
                }
            }            
        }
        //echo "import complete: " . time();
        update_option(self::LASTIMPORT_OPTION, time());
        update_option(self::LASTID_OPTION, $lastId);
    }
    
    /**
     * Add a percolate story to the WP system as a post
     **/
    public function importStory($story)
    {
        global $wpdb;
        
        //echo "<pre>"; print_r($story); echo "</pre>";
        
        $linkId = $story['link_id'];
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
        $post['post_title']=html_entity_decode($story['user_title']);
        if (!$post['post_title']) {
            $post['post_title']=html_entity_decode($story['original_title']);
        }
        
        if (!trim($post['post_title'])) {
            $post['post_title']='[no title]';
        }
        
        $post['post_content']=html_entity_decode($story['user_description']);
        
        // if (!$post['post_content']) {
        //     $post['post_content']=html_entity_decode($story['original_description']);
        // }
        
        $post['post_name']='perc_' . $story['link_id'];
        
        $post['post_date']=date('Y-m-d H:i:s', strtotime($story['added_on']));
        
        $post['post_status']=get_option(self::POSTSTATUS_OPTION);
        
        
        if (get_option(self::CATEGORY_OPTION)) {
            $post['post_category']=array(get_option(self::CATEGORY_OPTION));
        }
        
        $authorId = get_option(self::AUTHORID_OPTION);
        
        if ($authorId) {
            $post['post_author']=$authorId;
        }
        
        $postId = wp_insert_post($post);
        
        if ($story['tag']) {
            wp_set_post_tags($postId, array($story['tag']));
        }

        $sourceTitles=array();
        $useSources=array();
        
        foreach ($story['sources'] as $source) {
            $sourceTitles[$source['source_subscription_id']] = htmlentities(
                $source['source_subscription_title'] . ': ' . $source['source_entry_title'],
                ENT_QUOTES | ENT_IGNORE, "UTF-8"
            );
            $useSources[] = $source['source_subscription_id'];
            $source['source_subscription_title']=htmlentities($source['source_subscription_title']);
            $source['source_entry_title']=htmlentities($source['source_entry_title']);
            if (!$sourceTitles[$source['source_subscription_id']]) {
                $sourceTitles[$source['source_subscription_id']] = '[no title]';
            }
        }
        //echo "<pre>"; print_r($sourceTitles); echo "</pre>";
        
        $ver = floatval(phpversion());
        update_post_meta($postId, self::M_DOMAIN, $story['domain']);
        update_post_meta($postId, self::M_ADDEDON, strtotime($story['added_on']));
        update_post_meta($postId, self::M_FEATUREDSOURCE, 0);
        update_post_meta($postId, self::M_LINKID, $story['link_id']);
        update_post_meta($postId, self::M_ORIGINALDESCRIPTION, $story['original_description']);
        update_post_meta($postId, self::M_ORIGINALTITLE, $story['original_title']);
        if( $ver > 5.2 )
            update_post_meta($postId, self::M_SOURCES, json_encode($story['sources'], JSON_HEX_QUOT));
        else
            update_post_meta($postId, self::M_SOURCES, json_encode($story['sources']));

        update_post_meta($postId, self::M_URL, $story['url']);
        update_post_meta($postId, self::M_USERDESCRIPTION, $story['user_description']);
        update_post_meta($postId, self::M_USERTITLE, $story['user_title']);
        if( $ver > 5.2 )
            update_post_meta($postId, self::M_USE, json_encode($useSources, JSON_HEX_QUOT));
        else
            update_post_meta($postId, self::M_USE, json_encode($useSources));

        if( $ver > 5.2 )
            update_post_meta($postId, self::M_SOURCETITLES, json_encode($sourceTitles, JSON_HEX_QUOT));
        else
            update_post_meta($postId, self::M_SOURCETITLES, json_encode($sourceTitles));

        do_action('percolate_import_story', $postId);
        
        return $postId;
    }
    
    public static function getUrl($postId)
    {
        $url = get_post_meta($postId, self::M_URL, true);
        
        return $url;
    }
    
    public static function getFeaturedSource($postId)
    {
        $sources = json_decode(get_post_meta($postId, self::M_SOURCES, true), true);
        $useIds = json_decode(get_post_meta($postId, self::M_USE, true), true);
        $titles = json_decode(get_post_meta($postId, self::M_SOURCETITLES, true), true);
        $featuredId = get_post_meta($postId, self::M_FEATUREDSOURCE, true);
        $result = array();

        if (!$sources) {
            return array();
        }
        
        if (!array_key_exists($featuredId, $sources)) {
            return array();
        }
        
        $source = $sources[$featuredId];

        $id = $source['source_subscription_id'];
        if (!array_key_exists('source_url', $source)) {
            if (!array_key_exists('source_entry_url', $source)) {
                $source['source_url']=$source['source_entry_url'];
            } else {
                $source['source_url']='';
            }
        }
        
        $source['display_title']=$titles[$id];
        
        if (!$source['display_title']) {
            $source['display_title']=$source['source_entry_title'];
        }        
        
        return $source;
        
    }
    
    public static function getSources($postId)
    {
        $sources = json_decode(get_post_meta($postId, self::M_SOURCES, true), true);
        $useIds = json_decode(get_post_meta($postId, self::M_USE, true), true);
        $titles = json_decode(get_post_meta($postId, self::M_SOURCETITLES, true), true);
        $featuredId = get_post_meta($postId, self::M_FEATUREDSOURCE, true);
        
        $result = array();
        //echo "<pre>"; print_r($sources); echo "</pre>";
        //return;
        if (!$sources) {
            return $result;
        }
        
        foreach ($sources as $source) {
            $id = $source['source_subscription_id'];
            
            if (in_array($id, $useIds)) {
                
                if (!array_key_exists('source_url', $source)) {
                    
                    if (!array_key_exists('source_entry_url', $source)) {
                        
                        $source['source_url']=$source['source_entry_url'];
                        
                    } else {
                        
                        $source['source_url']='';
                        
                    }
                    
                }
                
                $source['display_title']=$titles[$id];
                
                if (!$source['display_title']) {
                    
                    $source['display_title']=$source['source_entry_title'];
                    
                }
                
                
                $source['featured']=($id == $featuredId);
                
                $result[]=$source;
            }
            
        }
        return $result;
    }
    
    /**
     * Check to see if stories have been imported recently
     */
    public function checkImport()
    {
        /*
        echo time() . "-" . get_option(self::LASTIMPORT_OPTION) . "=" .
            (time() - get_option(self::LASTIMPORT_OPTION)) . ">" .
            self::IMPORT_INTERVAL;
        */
//  echo get_option( self::IMPORT_OVERRIDE_OPTION ) . '<br>';
//  echo get_option( self::LASTIMPORT_OPTION ) . '<br>';
        if ( ((time() - get_option(self::LASTIMPORT_OPTION)) > self::IMPORT_INTERVAL) || get_option( self::IMPORT_OVERRIDE_OPTION ) == 1 ) {
            try{
                self::importStories();
                
            }
            catch (Exception $e)
            {
                
            }
        }
    }
    
    public function getPercolateStories()
    {
        $options['id'] = get_option(self::USERID_OPTION);
        $lastId = get_option(self::LASTID_OPTION);
        $allSources = get_option(self::ALLSOURCES_OPTION);
        if($lastId){
            $options['last_id'] = $lastId;
        }
        if($allSources){
        		$options['allsources'] = 1;
        }
        if($options['id'] != 0){
            return self::callPercolateApi('entries', $options);
        }
    }
    
    protected static function callPercolateApi($method, $fields=array())
    {
        $url = self::API_BASE . "$method";

        if ($fields) {
            $tokens = array();
            foreach ($fields as $key=>$val) {
                $tokens[]="$key=$val";
            }
            $url.="?" . implode('&', $tokens);
        }
        // echo $url;
        $curl_handle = curl_init($url);
        /* */
        //curl_setopt($curl_handle, CURLOPT_PROXY, '127.0.0.1:8888');
        /* */
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);                
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_HEADER, 0);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));

        $buffer = curl_exec($curl_handle);
        
        $status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
     
        curl_close($curl_handle);

        if ($status != 200) {
            $data = json_decode($buffer, true);
            
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
        
        return json_decode($buffer, true);    
        
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

        newP.innerHTML = '<?=$message['message']?>';
        var p2 = document.getElementsByClassName("wrap")[0];
        p2.parentNode.insertBefore(newP,p2);

        }
        }
    </script>
    <?php
    }
    }
}

register_activation_hook(__FILE__, array('PercolateImport', 'install'));

add_action('plugins_loaded', array('PercolateImport', 'init'));
add_action('admin_init', array('PercolateImport','adminInit'));
add_action('admin_menu', array('PercolateImport', 'adminMenu'));
add_action('save_post', array('PercolateImport', 'updatePost'));
add_action('add_meta_boxes', array('PercolateImport', 'suppressCustomMeta'));
add_action('admin_print_footer_scripts', array('PercolateImport', 'adminScripts'));
add_action('admin_notices', array('PercolateImport','userIdNotice'));
?>