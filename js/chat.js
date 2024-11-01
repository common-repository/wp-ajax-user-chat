jQuery(document).ready(function(){
	  var idList = new Array();
      var broadcastMessageCallback = function(from, msg, met) {
		  var idi = "box" + from;
		  var met = "submit";
          for(var i = 0; i < idList.length; i ++) {
		  	  if(idList[i] == idi){
			  	chatboxManager.addBox(idList[i]);
              	jQuery("#" + idList[i]).chatbox("option", "boxManager").addMsg(from, msg, met);	
			  }
          }
      }
      // chatboxManager is excerpt from the original project
      // the code is not very clean, I just want to reuse it to manage multiple chatboxes
	  	var tuid = jQuery('.currentuser').attr("id");
		var name = jQuery('.currentuser').attr("rel"); 
     	chatboxManager.init({messageSent : broadcastMessageCallback});
		function get_users(){
			jQuery.post(the_ajax_script.ajaxurl, { method:"getusers", action: "the_ajax_hook" }, function(response){
				//alert(response);
				jQuery("#allusers").html(response);
			});
		}
		function get_chat(name){ 
			jQuery.post(the_ajax_script.ajaxurl, { method:"getchat", action: "the_ajax_hook", uid: name }, function(response_from_the_action_functions){
					//jQuery("#response_area").html(response_from_the_action_functions);
				if(response_from_the_action_functions){
					var obj = response_from_the_action_functions;//jQuery.parseJSON(response_from_the_action_functions);
					if(obj){
						var nuser = obj.fromuser;
						var mmsg = obj.text;
						var meti = "get";
						if(nuser && mmsg){
							var ids = "box" + nuser;
					          if(idList){
							  	var isidi = jQuery.inArray( ids, idList );
								if(isidi == -1){
									idList.push(ids);	
								}
							  }else{
							  	idList.push(ids); // adding member to the list
							  }
	    					chatboxManager.addBox(ids, 
					                            {
													dest:"dest" + nuser, // not used in demo
					                               	title:"box" + nuser,
					                               	first_name: nuser, // + uid
					                               	last_name: ' '
					                           	}
											);
					          jQuery("#" + ids).chatbox("option", "boxManager").addMsg(nuser, mmsg, meti);
							}
						}
					}
				}, "json");
			} 
      jQuery("#wpchat").on("click",".chat", function(event, ui) {
		  var uid = jQuery(this).attr('id');
		  var uname = jQuery(this).attr('rel');
		  var id = "box" + uname;
		  if(idList){
		  	var isid = jQuery.inArray( id, idList );
			if(isid == -1){
				idList.push(id);	
			}
		  }else{
		  	idList.push(id); // adding member to the list
		  }
          chatboxManager.addBox(id, 
                                  {dest:"dest" + uname,
                                   title:"box" + uname,
                                   first_name: uname,
                                   last_name: ' '
                                  });
          event.preventDefault();
		  return false;
      });
	if(name){
		setInterval(function(){ get_chat(name); }, 3000);
		setInterval(function(){ get_users(); }, 10000);
	}
	jQuery(".mini").click(function(){
		jQuery("#allusers").toggle();
		jQuery(this).toggleClass("activewpac");
	});
});