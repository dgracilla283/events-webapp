<?php if(!empty($breakouts)):?>
<div data-role="collapsible-set">
<?php foreach($breakouts as $breakout) : ?>
	<div id="schedule-list-header" data-role="collapsible"
		data-collapsed="<?php echo ($breakout['breakoutID'] == $selectedBreakoutId) ? 'false': 'true'?>">
		<h3 class="title">
			<div class="schedule-header-date">
				<div class="left-schedule-head"></div>
				<?php echo $breakout['title']; ?>
			</div>
		</h3>
		<div class="hide multi-activity">
			<div>
				<div>
					<p class="subtitle">
						Date:
						<?php echo $selectedBreakoutId. date('l, F d, Y', strtotime($breakout['start_date_time']));?>
					</p>
					<p class="subtitle">
						Time:
						<?php echo date('h:i A', strtotime($breakout['start_date_time']));?>
						-
						<?php echo date('h:i A', strtotime($breakout['end_date_time']));?>
					</p>
					<?php if($breakout['location']):?>
					<p class="subtitle">
						Location:
						<?php echo $breakout['location']?>
					</p>
					<?php endif; ?>
					<?php
					if(!empty($breakout['speakers'])):?>
					<p class="subtitle">
					<?php echo (1 < count($breakout['speakers'])) ? 'Speakers' : 'Speaker' ?>
						:
						<?php
						$speakerNames = array();
						$userIsSpeaker;
						foreach($breakout['speakers'] as $spr):
						$speakerNames[] = '<a href="/webapp/view_user?uid='.$spr['user_id'].'&eid='.$eventId.'&spr=1">'.$spr['first_name'] . ' ' . $spr['last_name'].'</a>';
						if ($userID == $spr['user_id'])
							$userIsSpeaker = true;
						endforeach;
						echo implode(', ',$speakerNames);
						?>
					</p>
					<?php
					endif;
					?>
				</div>
				<?php if(!array_key_exists($userID, $breakout['activityAttendees']) && !$userIsSpeaker): ?>
				<div class="join-activity primary-user">			
					<?php if(array_key_exists($userID, $breakout['pendingRequests'])): ?>
					<div class="cancel-join">
						<a class="btn-cancel-join join_button"
							data-id="<?php echo $breakout['pendingRequests'][$userID]['eventAttendeeID'];?>"
							href="javascript:;">Cancel My Join Request </a>
					</div>
					<?php else: ?>
					<a class="btn-join-activity join_button"
						data-id="<?php echo $breakout['breakoutID']?>" 
						data-reftype="activity" 
						data-userid="<?php echo $userID;?>" 
						href="javascript:;">
						Join  
					</a>
					<?php endif; ?>		
				</div>	
				<?php endif; ?>	
				<?php if(!empty($eventCompanions)): ?>
				<?php foreach($eventCompanions as $cmp): ?> 
				<div class="join-activity event-companions">
					<?php if(array_key_exists($cmp['userID'], $breakout['pendingRequests']) && !$userIsSpeaker): ?>
					<div class="cancel-join">
						<a class="btn-cancel-join join_button"
							data-id="<?php echo $breakout['pendingRequests'][$cmp['userID']]['eventAttendeeID'];?>"
							href="javascript:;">Cancel <?php echo $cmp['first_name'].' '.$cmp['last_name'] ?> Join Request</a>
					</div>
					<?php elseif(!array_key_exists($cmp['userID'], $breakout['activityAttendees'])): ?>
					<a class="btn-join-activity join_button"
						data-id="<?php echo $breakout['breakoutID']?>"
						data-userid="<?php echo $cmp['userID']?>"   
						href="javascript:;"  
						data-reftype="activity">
						Join for <?php echo $cmp['first_name'].' '.$cmp['last_name'] ?>
					</a>
					<?php endif; ?>	 
				</div>	
				<?php endforeach; ?>
				<?php endif;?>
				
			</div>
			<?php if($breakout['description']):?>
			<div class="session-details-summary">
				<p>
					<span class="lead">Summary:</span>
					<?php echo $breakout['description']?>
				</p>
			</div>
			<?php endif; ?>
			<?php if(!empty($breakout['teams'])):?>
			<div class="speaker-session-header">Teams</div>
			<ul data-inset="false" data-role="listview" data-divider-theme="a">
			<?php	foreach($breakout['teams'] as $guest):	?>
				<li data-theme="c"><a
					href="/webapp/view_user?uid=<?php echo $guest['user_id'].'&eid='.$eventId;?>"
					data-transition="slide">
						<div class="team-list-attendees">
							<div class="block-a title">
							<?php echo $mapAttendees[$guest['user_id']]['last_name'] .', '.$mapAttendees[$guest['user_id']]['first_name']?>
							</div>
							<div class="block-b">
							<?php echo $guest['team']; ?>
							</div>
						</div> </a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
			<?php if(!empty($breakout['activityAttendees'])):?>
			<div class="speaker-session-header">Activity Attendees</div>
			<ul data-inset="false" data-role="listview" data-divider-theme="a">
			<?php foreach($breakout['activityAttendees'] as $guest):?>
				<li data-theme="c"><a class="title"
					href="/webapp/view_user?uid=<?php echo $guest['user_id'].'&eid='.$eventId;?>"
					data-transition="slide"> <?php echo $guest['first_name'] . ' ' . $guest['last_name']?>
				</a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php endforeach; ?>
</div>
	<?php else: ?>
	No breakout sessions.
	<?php endif; ?>
