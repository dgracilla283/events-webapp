<form id="form-edit-itinerary" class="form-horizontal form-itinerary" method="post" action="/admin/add_edit_breakout_attendees">
	<fieldset>			
	  <div class="control-group">
		<label class="control-label">Select Attendee:</label>
		<div class="controls">
		  <select class="focused input-medium speaker-select" type="text">
  			<option value=""></option>
	  		<?php foreach($eventAttendees as $eventAttendee): 
	  				if(empty($breakoutAttendees[$eventAttendee['userID']])): 
	  		?>	  						  				  		
  				<option value="<?php echo $eventAttendee['userID']?>"><?php echo $eventAttendee['last_name'].', '.$eventAttendee['first_name']?></option>
  			<?php 
  					endif; 
  				endforeach; 
  			?> 
  		  </select>
  		  <a class="btn btn-add-breakout-attendee" href="javascript:;" title="Add Attendee" >
			<i class="icon-user icon-black"></i> Add                     
		  </a>
		</div>
	  </div>
	  <?php foreach($activityPreferences as $pref) { ?>
	  <div class="control-group">
		<label class="control-label"><?php echo htmlentities($pref['title']);?></label>
		<div class="controls">  		  
			<?php 
				$displayType = trim($pref['optionDisplayType']);
				$htmlOptions = ''; 
				
				switch($displayType){
					case 'selectbox':						 
						$htmlOptions = '<select name="option_'.$pref['activityPreferenceID'].'">';	 
						foreach($pref['options'] as $option){
							$htmlOptions .= '<option value="'.$option['activityPreferenceOptionID'].'">'.$option['title'].'</option>'; 	
						}
						$htmlOptions .= '</select>';
					break;				
				}				
				echo  $htmlOptions; 
				
			?>
			
		</div>
	  </div>
	  <?php } ?>
	  
	  <table class="table" id="table-breakout-attendees">
 		<tr>
 			<th width="60%">Name</th>			
 			<th width="40%">Actions</th>
 		</tr> 	
 		<?php 
 			if(!empty($breakoutAttendees)):
 				foreach($breakoutAttendees as $key => $breakoutAttendee):
 					if(3 == $breakoutAttendee['role_id']) : 	
 		?>	
 		<tr> 			
 			<td><?php echo $eventAttendees[$key]['last_name']. ', '.$eventAttendees[$key]['first_name'];?></td>
 			<td>
 				<a class="icon-black icon-trash btn-delete-breakout-attendee" title="Delete Activity Attendee" data-option-id="<?php echo $key;?>" href="#"></a>
 				<input type="hidden" value="<?php echo $key; ?>'" name="user_id[]" /> 				
 			</td> 			
 		</tr>
 		<?php 
 					endif; 
 				endforeach; 		
 			endif; 
 		?>	
 	</table>	  	
	<input type="hidden" value="<?php echo $eventId; ?>" name="eid" />			 		
	<input type="hidden" value="<?php echo $breakoutId; ?>" name="bid" />
	<input type="hidden" value="<?php echo $itineraryId; ?>" name="iid" />
	</fieldset>
 </form>
	