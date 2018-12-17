<div class="session-details">
	<div class="f-left span-left-10">
		<p class="title">
		<?php echo $itinerary['title']?>
		</p>
		<p class="subtitle">
			Date:
			<?php echo date('l, F d, Y', strtotime($itinerary['start_date_time']));?>
		</p>
		<p class="subtitle">
			Time:
			<?php echo date('h:i A', strtotime($itinerary['start_date_time']));?>
			-
			<?php echo date('h:i A', strtotime($itinerary['end_date_time']));?>
		</p>
		<?php if(!empty($itinerary['location'])): ?>
		<p class="subtitle">
			Location:
			<?php echo $itinerary['location']?>
			<?php if(isset($mapReference['map_photo_id'])): ?>
				<a href="/webapp/map_details?eid=<?php echo $eventId ?>&mid=<?php echo $mapReference['map_photo_id'] ?>">View Map Photo</a>
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<?php if(!empty($itinerary['speakers'])): ?>
		<p class="subtitle">
		<?php echo (count($itinerary['speakers']) > 1) ? 'Speakers:' : 'Speaker' ?>
			:
			<?php
			$speakerNames = array();
			$userIsSpeaker	= false;
			foreach($itinerary['speakers'] as $speaker) {
				$speakerNames[] = '<a href="/webapp/view_user?uid=' . $speaker['user_id'] . '&eid=' . $eventId	. '&spr=1">' .
				$speaker['first_name'] . ' ' . $speaker['last_name'] . '</a>';
				if ($userID == $speaker['user_id'])
					$userIsSpeaker = true;
			}
			echo implode(', ', $speakerNames);
			?>
		</p>
		<?php endif; ?>
	</div>
	<?php
	$attendeesCount = count($itinerary['activityAttendees']);
	if(!$itinerary['breakout_status']): ?>
		<?php if(!array_key_exists($userID, $itinerary['activityAttendees']) && !$userIsSpeaker): ; ?>
			<div class="join-activity primary-user">
				<?php if(array_key_exists($userID, $itinerary['pendingRequests'])): ?>
				<div class="cancel-join">
					<a class="btn-cancel-join join_button"
						data-id="<?php echo $itinerary['pendingRequests'][$userID]['eventAttendeeID'];?>"
						href="javascript:;">Cancel My Join Request </a>
				</div>
				<?php else:
				$limit = !empty($itinerary['attendees_limit']) ? $itinerary['attendees_limit'] : 1000000;
				$slotsRemaining = ($limit - $attendeesCount); ?>

				<?php if($slotsRemaining > 0) : ?>
				<a class="btn-join-activity join_button"
					data-id="<?php echo $itinerary['itineraryID']?>"
					data-reftype="agenda"
					data-userid="<?php echo $userID;?>"
					href="javascript:;">
					Join
				</a>
				<?php endif;?>
				<?php if(!empty($itinerary['attendees_limit'])): ?>
				<p class="join_button">
					<?php if($slotsRemaining > 0): ?>
					This activity is limited to only <?php echo $itinerary['attendees_limit'];?> participants. There <?php if ($attendeesCount > 1): ?>are<?php else: ?>is<?php endif;?> <?php echo $attendeesCount;?> who already joined, there <?php if ($slotsRemaining > 1): ?>are<?php else: ?>is<?php endif;?> <span class="red"><?php echo $slotsRemaining?></span> slots remaining.
					<?php else: ?>
					This activity is limited to only <?php echo $itinerary['attendees_limit'];?> participants. There <?php if ($attendeesCount > 1): ?>are<?php else: ?>is<?php endif;?> <?php echo $attendeesCount;?> who already joined. There are no more slots available. Please contact the event facilitator for inquiry.
					<?php endif; ?>
				</p>
				<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if(!empty($eventCompanions) && !$userIsSpeaker): ?>
		<?php foreach($eventCompanions as $cmp): ?>
		<div class="join-activity event-companions">
			<?php if(array_key_exists($cmp['userID'], $itinerary['pendingRequests'])): ?>
			<div class="cancel-join">
				<a class="btn-cancel-join join_button"
					data-id="<?php echo $itinerary['pendingRequests'][$cmp['userID']]['eventAttendeeID'];?>"
					href="javascript:;">Cancel <?php echo $cmp['first_name'].' '.$cmp['last_name'] ?> Join Request</a>
			</div>
			<?php elseif(!array_key_exists($cmp['userID'], $itinerary['activityAttendees'])): ?>
			<a class="btn-join-activity join_button"
				data-id="<?php echo $itinerary['itineraryID']?>"
				data-userid="<?php echo $cmp['userID']?>"
				href="javascript:;"
				data-reftype="agenda">
				Join for <?php echo $cmp['first_name'].' '.$cmp['last_name'] ?>
			</a>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php if(!empty($itinerary['description'])) : ?>
<div class="session-details-summary">
	<p>
		<span class="lead">Summary:</span>
		<?php echo $itinerary['description']?>
	</p>
</div>
<?php endif; ?>

<?php if(!empty($itinerary['activityAttendees'])): ?>
<div class="speaker-session-header">Attendees <?php if($itinerary['attendees_limit'] > 0):?>(<?php echo count($itinerary['activityAttendees'])?> out of <?php echo $itinerary['attendees_limit']?>)<?php else: ?> <?php echo count($itinerary['activityAttendees'])?> <?php endif;?></div>
<ul data-inset="false" data-role="listview" data-divider-theme="a">
<?php foreach($itinerary['activityAttendees'] as $guest):?>
	<li data-theme="c"><a class="title"
		href="/webapp/view_user?uid=<?php echo $guest['user_id'] ?>&eid=<?php echo $eventId ?>"
		data-transition="slide"> <?php echo $guest['first_name'] .' '.$guest['last_name'];?>
	</a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if(!empty($itinerary['teams'])):?>
<div class="speaker-session-header">Teams</div>
<ul data-inset="false" data-role="listview" data-divider-theme="a">
<?php foreach($itinerary['teams'] as $guest):?>
	<li data-theme="c">
		<a href="/webapp/view_user?uid=<?php echo $guest['user_id'].'&eid='.$eventId;?>" data-transition="slide">
			<div class="team-list-attendees">
				<div class="block-a title">
				<?php echo $guest['first_name'] .' '.$guest['last_name']?>
				</div>
				<div class="block-b">
				<?php echo $guest['team']; ?>
				</div>
			</div>
		</a>
	</li>
	<?php
	endforeach;
	?>
</ul>
<?php endif; ?>
