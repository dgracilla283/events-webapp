<form id="form-edit-itinerary" class="form-horizontal form-itinerary" method="post" action="/admin/save_itinerary">
	<fieldset>
	  <div class="control-group">
		<label class="control-label">Title</label>
		<div class="controls">
		  <input class="input-xlarge" type="text" name="title" value="<?php echo htmlentities($itinerary['title']);  ?>" maxlength="50"/>
		</div>
	  </div>
	  <div class="control-group">
		<label class="control-label">Description</label>
		<div class="controls">
		  <textarea name="description" rows="5" class="input-xlarge"><?php echo htmlentities($itinerary['description']);  ?></textarea>
		</div>
	  </div>
	  <div class="control-group">
		<label class="control-label" for="disabledInput">Location</label>
		<div class="controls">
		  <input class="input-xlarge" type="text" name="location" value="<?php echo htmlentities($itinerary['location']);  ?>"/>
		</div>
	  </div>

	  <div class="control-group">
		<label class="control-label" for="disabledInput">Location Map Photo</label>
		<div class="controls">
		  <!-- <input class="input-xlarge" type="text" name="location" value="" maxlength="50"/> -->
		  <?php //dump($mapReference); exit; ?>
		  <select name="map_photo_id">
		  <option value=""></option>
		  <?php if(!empty($mapPhotos)): ?>
		  <?php foreach($mapPhotos as $mapPhoto): ?>
		  <option<?php echo isset($mapReference['map_photo_id']) && ($mapPhoto['mapPhotoID'] == $mapReference['map_photo_id']) ? ' selected' : '' ?> value="<?php echo $mapPhoto['mapPhotoID'] ?>"><?php echo $mapPhoto['title'] ?></option>
		  <?php endforeach; ?>
		  <?php endif; ?>
		  </select>
		  <input type="hidden" name="mapReferenceID" value="<?php echo isset($mapReference['mapReferenceID']) ? $mapReference['mapReferenceID'] : '' ?>" />
		</div>
	  </div>
	<div class="speakers-container">
		<?php if (!empty($speakers)): ?>
		<?php 	foreach($speakers as $speaker): ?>
		<div class="control-group">
			<label class="control-label" for="disabledInput">Speaker</label>
			<div class="controls">
			  <select class="focused input-medium speaker-select" type="text" name="user_id[]">
	  			<option value=""></option>
		  		<?php foreach($attendees as $user): ?>
	  				<option value="<?php echo $user['userID']?>"<?php echo $speaker['user_id'] == $user['userID'] ? 'selected' : ''?>><?php echo $user['last_name'].', '.$user['first_name']?></option>
	  			<?php endforeach; ?>
	  		  </select>
	  		  <a class="btn btn-add-more-speaker" href="javascript:;" title="Add more speaker" >
				<i class="icon-user icon-black"></i>
			  </a>
			  <a class="btn btn-remove-speaker" href="javascript:;" title="Delete speaker">
				<i class="icon-trash icon-black"></i>
			  </a>
			</div>
		</div>
		<?php 	endforeach; ?>
		<?php else: ?>
		<div class="control-group">
		<label class="control-label" for="disabledInput">Speaker</label>
		<div class="controls">
		  <select class="focused input-medium speaker-select" type="text" name="user_id[]">
  			<option value=""></option>
	  		<?php foreach($attendees as $user): ?>
  				<option value="<?php echo $user['userID']?>"><?php echo $user['last_name'].', '.$user['first_name']?></option>
  			<?php endforeach; ?>
  		  </select>
  		  <a class="btn btn-add-more-speaker" href="javascript:;" title="Add more speaker" >
			<i class="icon-user icon-black"></i>
		  </a>
		  <a class="btn btn-remove-speaker" href="javascript:;" title="Delete speaker">
			<i class="icon-trash icon-black"></i>
		  </a>
		</div>
	  	</div>
		 <?php endif; ?>
		 <input type="hidden" name="itinerary_id" value="<?php echo isset($itinerary['itineraryID']) ? $itinerary['itineraryID'] : '' ?>" />
		 <input type="hidden" name="reference_type" value="agenda" />
	</div>
 	<div class="control-group">
		<label class="control-label" for="disabledInput">Date Start</label>
		<div class="controls">
		  <input class="input-small datepicker" type="text" name="start_date_time" value="<?php echo date('m-d-Y',strtotime(str_replace('-', '/',$itinerary['start_date_time'])));  ?>" />
		  <input class="input-mini timepicker" type="text" name="start_time" value="<?php echo date('h:i A',strtotime($itinerary['start_date_time']));  ?>" readonly />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="disabledInput">Date End</label>
		<div class="controls">
		  <input class="input-small datepicker" type="text" name="end_date_time" value="<?php echo date('m-d-Y',strtotime(str_replace('-', '/',$itinerary['end_date_time'])));  ?>" />
		  <input class="input-mini timepicker" type="text" name="end_time" value="<?php echo date('h:i A',strtotime($itinerary['end_date_time']));  ?>" readonly />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="disabledInput">Breakout Session?</label>
		<div class="controls">
		  <input class="input-mini" type="checkbox" name="breakout_status" value="1" <?php if( 1 == $itinerary['breakout_status']){ echo 'checked="checked"';}?> /><span class="hint" title="Check if this itinerary is a breakout session">[?]</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="disabledInput">Attendees Limit <span class="hint" title="Leave it blank for unlimited attendees.">[?]</span></label>
		<div class="controls">
		   <input class="input-xlarge" type="text" name="attendees_limit" value="<?php echo $itinerary['attendees_limit']?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="disabledInput">Assign Team</label>
		<div class="controls">
				<div class="pull-left">
					<div><input class="input-medium" id="team-name"  type="text" value="" /></div>
					<div style="margin-top:10px; margin-bottom:10px;"><a class="btn" href="#" id="btn-add-team-agenda">Add Team</a></div>
				</div>
				<div class="pull-left" style="margin-left:10px">
					<?php
						$teams = array();
						$unAssignedGuests = array();
						if(!empty($users)) :
							foreach($users as $guest):
								if(!empty($breakoutInfo['guests'][$guest['user_id']]) &&
										!empty($breakoutInfo['guests'][$guest['user_id']]['team'])){
									$teams[] = $guest;
								}else {
									$unAssignedGuests[] = $guest;
								}
							endforeach;
						endif;
					?>
					<select class="multi input-medium" type="text" multiple id="team-members">
			  		<?php foreach($unAssignedGuests as $user): ?>
		  				<option value="<?php echo $user['userID']?>"><?php echo $user['last_name'].', '.$user['first_name']?></option>
		  			<?php endforeach; ?>
		  			</select>
		  		</div>
		</div>
 	</div>

 	<table class="table" id="table-assigned-team">
 		<tr>
 			<th>Member</th>
 			<th>Team</th>
 			<th>Actions</th>
 		</tr>
 		<?php
 		if(!empty($breakoutInfo['guests'])) :
 				foreach($breakoutInfo['guests'] as $key => $value):
 					if(!empty($value['team'])): ?>
 		<tr>
 			<td><?php echo $value['last_name'].', '.$value['first_name']; ?></td>
 			<td><?php echo $value['team']; ?></td>
 			<td>
 				<a class="icon-black icon-trash btn-delete-team-member" title="Delete Team Member" data-option-id="<?php echo $key;?>" href="#"></a>
 				<input type="hidden" value="<?php echo $key; ?>'" name="team_members[]" />
 				<input type="hidden" value="<?php echo $value['team'];?>" name="team_name[]" />
 			</td>
 		</tr>
 		<?php
 					endif;
 				endforeach;
 			endif; ?>
 	</table>
	<input type="hidden" value="<?php echo $itinerary['event_id'];?>" name="event_id" />
	<input type="hidden" value="<?php echo $itinerary['itineraryID']; ?>" name="itineraryID" />
	</fieldset>
  </form>
  <?php exit; ?>