(function($) {
    $(document).ready(function () {
        var ANIMATE_SPEED=800;
        
        $('.perc-debug-toggle').click(function () {
          $('.perc-debug').toggle();
          return false;
        });

        $('.perc-import-button').click(function () {
            var summary=$(this).closest('.story-text'),
                container=$(this).closest('.story'),
                link=$('.story-link', container).attr('name'),
                data=percStories[link],
                form = $(tmpl('story_form_tmpl', data)),
                firstTitle = $('input[name$="title"][value!=""]:first', form),
                descriptions = $('textarea[name$="description"]', form),
                foundDescription = false;
                
            container.css({position:'relative', height: container.height()});
            summary.css({position: 'absolute', top: 0, left: 0, width: container.width()});
            form.css({position: 'absolute', top: 0, left: container.width()});
            form.appendTo(container);
            
            $('input:radio[value="' + firstTitle.attr('name') + '"]', form).attr('checked','checked');
            
            descriptions.each(function () {
                if(foundDescription)
                {
                    return;
                }
                if($(this).val().trim() !== "")
                {
                    $('input:radio[value="' + $(this).attr('name') + '"]', form).attr('checked','checked');                    
                    foundDescription = true;
                }
            });
            
            $('input[name$="title"], textarea[name$="description"]', form).click(function () {
                $('input:radio[value="' + $(this).attr('name') + '"]', form).attr('checked','checked');
            });
            
            $('input:radio[name="featured_source"]:first').attr('checked','checked');
            
            summary.animate({left: container.width() * -1}, ANIMATE_SPEED, function () {
                container.data('summary', summary.detach());
            });
            
            form.animate({left: 0}, ANIMATE_SPEED);
            container.animate({height: form.outerHeight()}, ANIMATE_SPEED);
            
            
            $('.cancel-button', form).click(function () {
                summary.prependTo(container);

                summary.animate({left: 0}, ANIMATE_SPEED, function () {
                    summary.css({position: 'inherit'});
                });
                
                form.animate({left: container.width()}, ANIMATE_SPEED, function () {
                    form.remove();
                });
                
                container.animate({height: summary.height()}, ANIMATE_SPEED, function () {
                    container.css({height: null, position:'inherit'});                    
                });

                
                return false;
            });
            
            return false;
        });
        
        $('.sources ul').hide();
        $('.source-toggle').click(function () {
            var container=$(this).closest('.sources');
            if($('ul:visible', container).length > 0)
            {
                $('ul', container).slideUp();
            }
            else
            {
                $('ul', container).slideDown();
            }
            return false;
        });

	 //user type selection:
	 $('#percapi-user-type-individual').click(function (){
		toggleUserType($('#percapi-user-type-individual').val());
	});
	
	$('#percapi-user-type-group').click(function (){
		toggleUserType($('#percapi-user-type-group').val());
	});

	//toggle user type onload
   	toggleUserType($('#init_user_type').val());

	//search user id by name
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

	//set group author ids before submit
	$('#percolate_options').submit(function() {
		setupGroupAuthorIdsValue();
		required_complete=checkRequiredOptions();
		return required_complete;
	});
	
	//refresh group users
	$("#refresh_memberform").click(function(){
		setupGroupAuthorIdsValue();
		required_complete = checkRequiredOptions();
		if (required_complete==true){
		 $('#percolate_options').submit();
		}
	});
	
	//create new author 
	$(".group_user_ids").change(function(){
		$new_author = $(this).val();
		if ($new_author=='new_author'){
			window.location ='user-new.php';
		}
	});
	//import stories now
	$("#import_stories_now").click(function(){
		$('#override_import').val('1');
		setupGroupAuthorIdsValue();
		required_complete = checkRequiredOptions();
		if (required_complete==true){
		 $('#percolate_options').submit();
		}
	});


  });
  function toggleUserType(type){
	if(type==0){
		$(".user-type-indi").parent().parent().show();
		$(".user-type-grp").parent().parent().hide();
	}else{
		$(".user-type-indi").parent().parent().hide();
		$(".user-type-grp").parent().parent().show();
	}
  }
   
  function setupGroupAuthorIdsValue(){
		//get all the user selected values 
	var values = '{"":""';
  	$(".group_user_ids option:selected").each(function () {
			selected_value = $(this).val();
			selected_value = selected_value.replace(/'/g, '\"');
    		if(selected_value){
				values += ","+selected_value;
			}
  	});
	values += "}";
	$("#percolateimport_groupauthorids").val(values);	
  }
	
  function checkRequiredOptions(){
	type = $('input:radio[name=percolateimport_usertype]:checked').val();
	user_group_id = $(".user-group-id").val();
  api_key = $("#percapi_api_key").val();
	user_id = $(".user-id").val();
	



  if ($(".fn-channel-id").length ) {
    channel_id = $(".fn-channel-id").val();

    if (!channel_id) {
      $(".fn-channel-error").show();
      $(".fn-channel-error-help").css("color", "#cc0000");
      return false;
    }
  }


  return true;
	// if(type==0){
	// 	if(user_id==''){
	// 		alert("User ID is required!");
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
		
	// }else{
	// 	if(user_group_id==''){
	// 		alert("Group ID is required!");
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
	// }
  
  }


})(jQuery);



/*** FORMATTING ***/
// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){
  var cache = {};
  
  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :
      
      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +
        
        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +
        
        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");
    
    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();