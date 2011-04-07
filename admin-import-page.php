<style type="text/css">

.story { border-bottom: 1px solid #eee; }

.sources h5 { background: #ddd; text-shadow: 0 1px 0 white; line-height: 1em; padding: 4px; }

.import-submit { text-align: right; }

.form-field input.perc-field-select { float: left; width: 20px; margin-top: 7px; }

.perc-add-story-form input[type="text"], .perc-add-story-form textarea { width: 80%;}
.perc-column-small, .form-table th.perc-column-small { width: 40px; }

.story-form { margin: 0; padding: 10px 0; }
</style>

<div class="wrap">
    <div id="icon-edit" class="icon32"><br></div>
    <h2>Import Posts from Percolate</h2>

    <h3>Your Percolate Stories</h3>
    
    <?php
    $stories=PercolateImport::getPercolateStories();
   // echo "<pre>"; print_r($stories); echo "</pre>";
   
        function displayFirst($arr)
        {
            foreach($arr as $item)
            {
                if($item)
                {
                    echo $item;
                    return;
                }
            }
        }

        function percBaseUrl($url)
        {
            $matches = array();
            preg_match('|^(http[s]?://)([0-9a-z.-]*)/(.*)$|i', $url, $matches);
            
            $protocol = $matches[1];
            $domain = $matches[2];
            
            $matches = array();
            preg_match('|([0-9a-z-]*\.)*([0-9a-z-]*\.[0-9a-z-]*)$|i', $domain, $matches);
            
            $host = $matches[2];
            
            return $protocol . $host;
            
        }
        
        function urlify($s) {
            return preg_replace('/https?:\/\/[\w\-\.!~?&+\*\'"(),\/]+/','<a target="_blank" href="$0">$0</a>',$s);
        }
    
    ?>
    <script type="text/javascript">
        var percStories={};
    </script>
    
    <div id="poststuff">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <div class="postbox">
                <h3 class="hndle"><span>Your Percolate Stories</span></h3>
                <div class="inside">
    <?php foreach( $stories as $i=>$story ): ?>
    <?php echo "<pre>"; print_r($story); echo "</pre>"; ?>
                    <div class="story">
                        <a class="story-link" name="<?php echo $story['link_id']; ?>" ></a>
                        <script type="text/javascript">
                        percStories['<?php echo $story['link_id']; ?>']=<?php echo json_encode($story); ?>;
                        </script>
                        <div class="story-text">
                            <h3><?php displayFirst(array($story['original_title'],$story['user_title'], '[No Title]')); ?></h3>
                            <?php if($story['user_description']): ?>
                            <p class="user-description"><?php echo $story['user_description']; ?></p>
                            <?php endif; ?>
                            <p><blockquote><?php echo $story['original_description']; ?></blockquote></p>
                            <?php //echo "<pre>"; print_r($story); echo "</pre>"; ?>
                            <p class="import-submit">
                            <input type="button" class="button-primary perc-import-button" value="Import This Story &raquo;" />
                            </p>
                            <div class="sources">
                                <h5><a href="#" class="source-toggle"><span>Sources</span></a></h5>
                                <ul class="sources-list">
                                <?php foreach($story['sources'] as $source): ?>
                                    <?php $baseUrl = percBaseUrl($source['source_url']); ?>
                                    <li><a target="_blank" href="<?php echo $source['source_url']; ?>" ><img src="<?php echo $baseUrl . '/favicon.ico'; ?>" title="<?php echo $source['source_subscription_title']; ?>" alt="<?php echo $source['source_subscription_title']; ?>" /></a> <?php echo urlify($source['source_entry_title']); ?></li>
                                <?php endforeach;?>
                                </ul>
                            </div>
                        </div>
                    </div>
    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/html" id="story_form_tmpl">
<div class="story-form form-wrap">
	<h3>Add Story</h3>
	<form class="perc-add-story-form" method="post">
	<div class="form-field">
		<label for="perc-user-title">User Title</label>
		<input type="radio" name="title_source" class="perc-field-select" id="title_source_user" value="user_title" /> <input id="perc-user-title" class="perc-input" type="text" name="user_title" value="<%=user_title%>"  />
	</div>
	<div class="form-field">
		<label for="perc-original-title">Original Title</label>
		<input type="radio" name="title_source" class="perc-field-select" id="title_source_original" value="original_title" /> <input id="perc-original-title" class="perc-input" type="text" name="original_title" value="<%=original_title%>" />
	</div>
	<div class="form-field">
		<label for="perc-user-body">User Description</label>
		<input type="radio" name="description_source" class="perc-field-select" id="description_source_user" value="user_description" />
		<textarea name="user_description" class="perc-input" ><%=user_description%></textarea>
	</div>
	<div class="form-field">
		<label for="perc-original-title">Original Description</label>
		<input type="radio" name="description_source" class="perc-field-select" id="description_source_original" value="original_description" />
		<textarea name="original_description" class="perc-input" ><%=original_description%></textarea>
	</div>
    <div class="sources">
        <h4 class="title">Sources</h4>
        <table class="form-table">
            <tr>
                <th class="perc-column-small" width="40">Featured</th>
                <th class="perc-column-small" width="40">Use</th>
                <th>&nbsp;</th>
            </tr>
        <% for(var i in sources) { var source=sources[i]; %>
            <tr>
                <td class="perc-column-small" width="40">
                    <input type="radio" name="featured_source" value="<%=source.subscription_id%>" class="perc-field-select">
                </td>
                <td class="perc-column-small" width="40">
                    <input type="checkbox" name="display_sources[]" value="<%=source.subscription_id%>" checked="checked"/>
                </td>
                <td width="100%">
                    <input type="text" name="source_titles[<%=source.subscription_id%>]" value="<%=source.source_subscription_title%> | <%=source.source_entry_title%>" />
                    <input type="hidden" name="source_urls[<%=source.subscription_id%>]" value="<%=source.source_entry_url%>" />
                </td>
            </tr>
        <%}%>
        </table>
    </div>
	<p class="submit">
		<input type="submit" class="button-primary submit-button" id="submit" value="Import Story" name="submit" />
        <input type="reset" class="button cancel-button" id="cancel" value="Cancel" name="cancel" />
	</p>
	</form>
</div>
    </script>
   
</div>