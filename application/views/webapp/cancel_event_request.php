<?php $this->load->view('webapp/includes/header.php') ?>
  <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
		<div class="header-inner-content">
			<a href="/" id="btn-home" data-ajax="false">HOME</a>
			<a id="btn-back" href="/webapp/dashboard?id=<?php echo $event['eventID'];?>" data-ajax="false">BACK</a>
			<h3>Cancel Event Confirmation</h3>
		</div>
	 </div>

<div id="add-companions" class="form-join-event-container">

	<form id="form-cancel-join-event" data-redirect="/webapp/event_details?id=<?php echo $eventId ?>">
		<div>
			<div class="fontSize14px">Please select the attendees you want to cancel the request:</div>
			<fieldset data-role="controlgroup" id="primary-companion-container">
				<div class="companion-container is-primary" style="height: 15px; margin-top:10px">
					<input id="primary-user-checkbox" style="margin-top:1px;" type="checkbox" name="primary_user" value="<?php echo $primaryUser['userID'] ?>" class="custom" />
					<label  style="margin-left: 45px;">Me</label>
				</div>
				<?php foreach($statusData['companions'] as $key => $companion): ?>
					<div class="companion-container is-companion" style="height: 15px; margin-top:10px">
						<input style="margin-top:3px;" type="checkbox" class="cbxCompanions" name="companions[]" value="<?php echo $companion['userID'] ?>" />
						<label style="margin-left: 45px;" class="fontSize14px" ><?php echo $companion['first_name'] . ' ' . $companion['last_name'] ?></label>
					</div>
				<?php endforeach ?>
				<br />
				<input type="hidden" name="eid" value="<?php echo $eventId ?>" />
				<input type="hidden" name="rtype" value="event" />
				<input type="hidden" name="rid" value="<?php echo $eventId ?>" />
				<a href="javascript:void(0);" class="aBtn" id="btn-form-cancel-join-event" title="Yes, Cancel Request">Yes, Cancel Request</a>
				<br />
				<a href="javascript:void(0);" class="aBtn" data-rel="back" data-transition="flow" title="Cancel">No, Don't Cancel Request</a>
			</fieldset>
		</div>
	</form>
</div>

<?php $this->load->view('webapp/includes/footer.php') ?>