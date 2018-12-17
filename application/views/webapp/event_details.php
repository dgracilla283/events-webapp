<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">
	    	<a href="/" id="btn-home" data-ajax="false">HOME</a>
	        <a id="btn-back" href="/webapp/dashboard?id=<?php echo $event['eventID'];?>" data-ajax="false">BACK</a>
	        <h3>Event Details</h3>
	    </div>
     </div>
     <div data-role="content">
     	<div class="box-white">

			<img class="event-details-image" src="/img/Logo.jpg" width="80" height="100" align="left" />
			<div class="right-panel">
				<p class="title"><?php echo $event['title']?></p>
				<p><?php echo date('l, F d, Y h:i A', strtotime($event['start_date_time'])) .' to '. date('l, F d, Y h:i A', strtotime($event['end_date_time'])); ?></p>
				<p>Location: <?php echo $event['location']?></p>
			</div>
		</div>

		<?php //TODO: Add guard, only include this JOIN button if not yet part of event
		//dump($guestStatus[$eventId]['status']); exit;
		if( isset($guestStatus[$eventId]['status']) && 'rejected' !== $guestStatus[$eventId]['status'] ) {
			$this->load->view('webapp/event_details/' . $guestStatus[$eventId]['status'] . '_status.php', $statusData);
		} else {
			$attendeesCount = count($attendees);
			if($attendeesCount < $event['attendees_limit'] || $event['attendees_limit'] == 0) {
				$this->load->view('webapp/event_details/no_status.php', $statusData);
			}else { ?>
				<p class="join_button">
			 		This event is limited to only <?php echo $event['attendees_limit'];?> participants. There <?php if ($attendeesCount > 1): ?>are<?php else: ?>is<?php endif;?> <?php echo $attendeesCount;?> who already joined. There are no more slots available. Please contact the event facilitator for inquiry.
				</p>
		<?php
			}
		}
		?>

		<div class="box-white-event-details">
				<p>Description: <?php echo !empty($event['description']) ? $event['description'] : '<i>No description</i>'?></p>
				<div id="additional_info">
				<?php if(!empty($event['additional_info'])): ?>
				<p>Additional Info:</p>
					<?php echo $event['additional_info']?>
				</div>
				<?php endif; ?>

		</div>
    </div>


 <?php include('includes/footer.php'); ?>