function submit_chat(from, msg, peer){
			jQuery.post(the_ajax_script.ajaxurl, { method:'submitchat', action: 'the_ajax_hook', user: from, chat: msg, uid: peer }
			,
			function(response_from_the_action_functions){
				jQuery("#response_area").html(response_from_the_action_functions);
			}
			);
		}