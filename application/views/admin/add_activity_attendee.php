<form id="form-add-activity-attendee" class="form-horizontal form-itinerary" method="post" action="/admin/add_edit_activity_attendee">
	<fieldset>			
	  <div class="control-group">
		<label class="control-label">Attendee</label>
		<div class="controls">
		<?php if($getVar['uid']): 
			echo $eventAttendees[$getVar['uid']]['last_name'].', '. $eventAttendees[$getVar['uid']]['first_name']; 
			echo '<input type="hidden" name="user_id" value="'.$getVar['uid'].'"/>'; 
		else: ?> 	
		  <select class="focused input-medium speaker-select" type="text" name="user_id">
  			<option value=""></option>
	  		<?php foreach($eventAttendees as $eventAttendee): 
	  				if(empty($activityAttendees[$eventAttendee['userID']])): ?>	  						  				  		
  						<option value="<?php echo $eventAttendee['userID']?>"><?php echo $eventAttendee['last_name'].', '.$eventAttendee['first_name']?></option>  				
  			<?php 
  					endif; 
  				endforeach; 
  			?> 
  		  </select> 
  		  <?php endif; ?> 
		</div>
	  </div>
	  <?php foreach($activityPreferences as $pref) { ?>
	  <div class="control-group">
		<label class="control-label"><?php echo htmlentities($pref['title']);?></label>
		<div class="controls">  		  
			<?php 
				// move this helper 
				$displayType = trim($pref['optionDisplayType']);
				$htmlOptions = ''; 
				
				switch($displayType){
					case 'selectbox':						 
						$htmlOptions = '<select name="options['.$pref['activityPreferenceID'].'][]">';												
						$htmlOptions .= '<option value=""></option>'; 	 
						foreach($pref['options'] as $option){
							$selected = ''; 
							if(!empty($attendeeActivityPreferences[$option['activityPreferenceID']])) :									
									if($option['activityPreferenceOptionID'] == $attendeeActivityPreferences[$option['activityPreferenceID']][0])
										$selected = ' selected'; 
							endif;  
														
							$htmlOptions .= '<option value="'.$option['activityPreferenceOptionID'].'"'.$selected.'>'.$option['title'].'</option>'; 	
						}
						$htmlOptions .= '</select>';
					break;	
					case 'radio':
						foreach($pref['options'] as $option){
							$selected = ''; 
							if(!empty($attendeeActivityPreferences[$option['activityPreferenceID']])) :									
									if($option['activityPreferenceOptionID'] == $attendeeActivityPreferences[$option['activityPreferenceID']][0])
										$selected = ' checked'; 
							endif;  	
							$htmlOptions .= '<p><input type="radio" value="'.$option['activityPreferenceOptionID'].'" name="options['.$pref['activityPreferenceID'].'][]"'.$selected.' />&nbsp;<span>'.$option['title'].'</span></p>'; 	
						}
					break;
					case 'checkbox':						 							 
						foreach($pref['options'] as $option){
							$selected = ''; 
							if(!empty($attendeeActivityPreferences[$option['activityPreferenceID']])) :									
									if(in_array($option['activityPreferenceOptionID'],$attendeeActivityPreferences[$option['activityPreferenceID']]))
										$selected = ' checked'; 
							endif;  
							$htmlOptions .= '<p><input type="checkbox" value="'.$option['activityPreferenceOptionID'].'" name="options['.$pref['activityPreferenceID'].'][]"'.$selected.' />&nbsp;<span>'.$option['title'].'</span></p>'; 	
						}
					break;	
					case 'textbox':		
						$i = 0; 				 							 
						foreach($pref['options'] as $option){
							$value = !empty($attendeeActivityPreferences[$option['activityPreferenceID']][$i]) ? $attendeeActivityPreferences[$option['activityPreferenceID']][$i] : ''; 
							$htmlOptions .= '<p><span>'.$option['title'].':</span></p><p><input type="textbox" value="'.htmlentities($value).'" name="options['.$pref['activityPreferenceID'].'][]" /></p>';
							$i++;  	
						}
					break;
					case 'textarea':	
						$i = 0; 							 							 
						foreach($pref['options'] as $option){
							$value = !empty($attendeeActivityPreferences[$option['activityPreferenceID']][$i]) ? $attendeeActivityPreferences[$option['activityPreferenceID']][$i] : '';	
							$htmlOptions .= '<p><span>'.$option['title'].':</span></p><p><textarea name="options['.$pref['activityPreferenceID'].'][]">'.htmlentities($value).'</textarea></p>'; 	
						}
					break;	
								
				}				
				echo  $htmlOptions; 
				
			?>
			
		</div>
	  </div>
	  <?php } ?>    	
	<input type="hidden" value="<?php echo $getVar['eid']; ?>" name="eventID" />			 		
	<input type="hidden" value="<?php echo $getVar['id']; ?>" name="activityID" />
	<input type="hidden" value="<?php echo $getVar['rtype'];; ?>" name="referenceType" />
	</fieldset>
 </form>
	