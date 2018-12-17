<!-- <a href="/webapp/notify_cancel_join?eid=<?php echo $eventId ?>" data-inline="true" data-rel="dialog" data-transition="pop">Cancel Event Request</a> -->

<div id="action-button">
	<a href="<?php echo $urlCancelEventRequest; ?>" class="cancel_join_button join_button" id="btnEventDetailsCancel">Cancel Event Request</a>
</div>

<div data-role="popup" id="popupCancelJoin">
	<form id="form-cancel-join-event" data-redirect="/webapp/event_details?id=<?php echo $eventId ?>">
       	<div data-role="fieldcontain">
    	   	<div class="popup_header fontSize14px">Please select the names you want to cancel the request:</div>
        	<fieldset data-role="controlgroup">
        		<div class="companion-container" style="height: 15px; margin-top:10px">
	        		<input id="primary-user-checkbox" style="margin-top:1px;" type="checkbox" name="primary_user" value="<?php echo $primaryUser['userID'] ?>" class="custom" />
					<label  style="margin-left: 45px;">Me</label>
				</div>
        		<?php foreach($companions as $key => $companion): ?>
        		<div class="companion-container" style="height: 15px; margin-top:10px">
					<input style="margin-top:1px;" type="checkbox" name="companions[]" value="<?php echo $companion['userID'] ?>" class="custom" />
					<label style="margin-left: 45px;"><?php echo $companion['first_name'] . ' ' . $companion['last_name'] ?></label>
				</div>
				<?php endforeach ?>
				<input type="hidden" name="eid" value="<?php echo $eventId ?>" />
				<input type="hidden" name="rtype" value="event" />
				<input type="hidden" name="rid" value="<?php echo $eventId ?>" />
				<div class="form-actions popup_form_btn">
				<br/>
				<a href="javascript:void(0);" data-role="button" data-inline="true" id="btn-form-cancel-join-event" >Yes, Cancel Request</a>
				<a href="javascript:void(0);" data-role="button" data-inline="true" data-rel="back" data-transition="flow">No, Don't Cancel Request</a>
				</div>
	    	</fieldset>
		</div>
	</form>
</div>