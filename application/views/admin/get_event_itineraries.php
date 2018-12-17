<?php if (!empty($itineraries)) : ?>
<table class="table table-striped" id="table-itineraries">
	  <thead>
		  <tr>
			  <th width="20%">Time</th>
			  <th class="center" align="center">Program</th>
			  <th width="20%">Location</th>
			  <th width="25%">Actions</th>
		  </tr>
	  </thead>
	  <tbody>
	  <?php
  		$eventDates = array();
  		$newDate = true;
  		foreach ($itineraries as $itinerary):
  			$programStartDate = date('m-d-Y', strtotime(str_replace('-', '/',$itinerary['start_date_time'])));
  			$programStartTime = date('h:i A', strtotime($itinerary['start_date_time']));
  			$programEndDate = date('m-d-Y', strtotime(str_replace('-', '/',$itinerary['end_date_time'])));
  			$programEndTime =	date('h:i A', strtotime($itinerary['end_date_time']));
  		 	if(!in_array($programStartDate, $eventDates)){
  		 		$eventDates[] = $programStartDate;
  		 		$newDate = true;
  		 	}else{
  		 		$newDate = false;
  		 	}
	  	if ($newDate) : ?>
	  	<tr>
			<td colspan="4" class="date center"><?php echo date('l, F d, Y', strtotime(str_replace('-', '/',$programStartDate)))?></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td>
				<?php echo $programStartTime. ' - ' .$programEndTime; ?>
				<input type="hidden" class="startDateTime" value="<?php echo $itinerary['start_date_time'];?>" />
				<input type="hidden" class="endDateTime" value="<?php echo $itinerary['end_date_time'];?>" />
			</td>
			<td class="center wordBreak"><?php echo $itinerary['title']; ?></td>
			<td class="center wordBreak"><?php echo $itinerary['location']; ?></td>
			<td class="center">
					<a class="btn edit-program" href="#" data-program-id="<?php echo $itinerary['itineraryID']; ?>" title="Edit Program"><i class="icon-black icon-edit"></i></a>
					<a class="btn delete-program" href="#" data-program-id="<?php echo $itinerary['itineraryID']; ?>" title="Delete Program"><i class="icon-black icon-trash"></i></a>
					<?php if(1 == $itinerary['breakout_status']): ?>
					<a class="btn btn-add-breakout" href="#" title="Add breakout" data-program-id="<?php echo $itinerary['itineraryID']; ?>"><i class="icon-black icon-plus-sign"></i></a>
					<?php else: ?>
					<a class="btn btn-activity-preferences" href="#" data-program-id="<?php echo $itinerary['itineraryID']; ?>" title="Activity Preferences" data-program-type="agenda"><i class="icon icon-darkgray icon-gear"></i></a>
					<a class="btn btn-add-agenda-attendees" href="#" title="Add Agenda Attendees" data-activity-id="<?php echo $itinerary['itineraryID']; ?>" data-activity-type="agenda" data-haspreferences="<?php echo ($itinerary['hasPreferences']) ? '1' : '0'; ?>"><i class="icon-black icon-user"></i></a>
					<?php endif; ?>
			</td>
		</tr>
		<?php if(!empty($itinerary['breakouts'])) : ?>
		<tr>
			<td colspan="4" class="breakout"><a class="btn-show-breakout" href="#" title="Show Activity Charts" data-bgroup-id="<?php echo $itinerary['itineraryID']; ?>"><i class="<?php if ($itinerary['itineraryID'] != $lastExpandID): echo 'icon-circle-arrow-down '; else: echo 'icon-circle-arrow-up '; endif;?>icon-white"></i> <?php echo $itinerary['title']; ?> Charts</a></td>
		</tr>
		<tr class="<?php if ($itinerary['itineraryID'] != $lastExpandID) echo 'hide ';?>breakout-<?php echo $itinerary['itineraryID']; ?>">
			<td class="center bgbreakout" colspan="4">
				<table class="table">
					 <thead>
					  <tr>
						  <th width="30%">Activity Title</th>
						  <th width="15%">Location</th>
						  <th width="30%">Teams</th>
						  <th>Actions</th>
					  </tr>
				 	 </thead>
					<?php foreach($itinerary['breakouts'] as $breakout): ?>
					<tr>
						<td class="center wordBreak"><?php echo $breakout['title']; ?></td>
						<td class="center wordBreak"><?php echo $breakout['location']; ?></td>
						<td class="center wordBreak"><?php if(!empty($breakout['teams'])): echo implode(',', $breakout['teams']); endif; ?></td>
						<td class="center">
							<a class="btn btn-edit-breakout" href="#" data-program-id="<?php echo $itinerary['itineraryID']; ?>" data-breakout-id="<?php echo $breakout['breakoutID']; ?>" title="Edit Breakout"><i class="icon-black icon-edit"></i></a>
							<a class="btn btn-delete-breakout" href="#" data-breakout-id="<?php echo $breakout['breakoutID']; ?>" title="Delete Breakout"><i class="icon-black icon-trash"></i></a>
							<a class="btn btn-activity-preferences" href="#" data-program-id="<?php echo $breakout['breakoutID']; ?>" data-program-type="activity" title="Breakout Preferences"><i class="icon icon-darkgray icon-gear"></i></a>
							<a class="btn btn-breakout-attendees" href="#" data-activity-id="<?php echo $breakout['breakoutID']; ?>" data-activity-type="activity" title="Activity Attendees" data-haspreferences="<?php echo ($breakout['hasPreferences']) ? '1' : '0'; ?>"><i class="icon-black icon-user"></i></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
			</td>
		</tr>
		<tr class="hide breakout-<?php echo $itinerary['itineraryID']; ?>">
			<td colspan="4" class="breakout">&nbsp;</td>
		</tr>
		<?php 	endif; 	?>
		</tbody>
		<?php endforeach; ?>
 </table>
 <?php else: ?>
 <div class="alert alert-error">
	<strong>No event itineraries!</strong>
</div>
 <?php endif; ?>
