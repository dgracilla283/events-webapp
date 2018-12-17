<form id="form-add-edit-breakout" class="form-horizontal frm-breakout" method="post" action="/admin/add_edit_breakout">
<fieldset>
	<table class="table">
	  <tbody>
		<tr>
			<td>Title</td>
			<td class="center" colspan="3"><input class="focused input-xlarge" type="text" name="title" value="<?php echo !empty($breakoutInfo) ? $breakoutInfo['title'] : '';?>" maxlength="100"/></td>
		</tr>
		<tr>
			<td>Location</td>
			<td class="center" colspan="3"><input class="focused input-xlarge" type="text" name="location" value="<?php echo !empty($breakoutInfo) ? $breakoutInfo['location'] : '';?>" maxlength="100" /></td>
		</tr>
		<tr>
			<td>Description</td>
			<td class="center" colspan="3"><textarea name="description" rows="5" class="input-xlarge" maxlength="200"><?php echo !empty($breakoutInfo) ? $breakoutInfo['description'] : '';?></textarea></td>
		</tr>
		<tr>
			<td>Speaker</td>
			<td class="center" colspan="3">
			<div class="speakers-container">
				<?php if (!empty($speakers)): ?>
				<?php 	foreach($speakers as $speaker): ?>
					<div class="control-group">
						<select class="focused input-medium speaker-select" type="text" name="user_id[]">
			  			<option value=""></option>
				  		<?php foreach($users as $user): ?>
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
				<?php 	endforeach; ?>
				<?php else: ?>
				<div class="control-group">
					<select class="focused input-medium speaker-select" type="text" name="user_id[]">
		  			<option value=""></option>
			  		<?php foreach($users as $user): ?>
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
				<?php endif; ?>
			</div>
  			</td>
		</tr>
		<tr>
			<td class="center">Assign Team</td>
			<td class="center" valign="top">
					<div class="pull-left">
						<div><input class="input-medium" id="team-name"  type="text" value="" /></div>
						<div style="margin-top:10px"><a class="btn" href="#" id="btn-add-team">Add Team</a></div>
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
			</td>
		</tr>
	  </tbody>
 	</table>

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
 			<td class="wordBreak"><?php echo $value['team']; ?></td>
 			<td>
 				<a class="icon-black icon-trash btn-delete-team-member" title="Delete Team Member" data-option-id="<?php echo $key;?>" href="#"></a>
 				<input type="hidden" value="<?php echo $value['user_id']; ?>'" name="team_members[]" />
 				<input type="hidden" value="<?php echo $value['team'];?>" name="team_name[]" />
 			</td>
 		</tr>
 		<?php
 					endif;
 				endforeach;
 			endif; ?>
 	</table>

	<input type="hidden" value="<?php echo $itineraryId;?>" name="itinerary_id" />
	<input type="hidden" name="event_id" value="<?php echo $eventId;?>"/>
	<input type="hidden" value="<?php echo !empty($breakoutInfo) ? $breakoutInfo['breakoutID'] : ''; ?>" name="breakoutID" />
	<input type="hidden" name="reference_type" value="activity" />
	</fieldset>
  </form>