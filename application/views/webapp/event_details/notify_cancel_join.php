
	<div data-role="dialog">
	
		
		<div data-role="header" data-theme="d">
			<h1>RCG Events Planner</h1>

		</div>

		<div data-role="content" data-theme="c" id="dialog-confirm">
			<h1>Cancel Join Event Request?</h1>
			<p>Cancellation of this request will also cancel your companions' event request should you have any. Proceed?</p>
			<a class="dialogActions" data-action="yes-cancel-request"  data-role="button" data-theme="b" href="javascript:void(0);" data-rel="back">Yes, cancel this event request</a>       
			<a class="dialogActions" data-action="no-cancel-request" data-role="button" data-theme="c" href="javascript:void(0);" data-rel="back">No, don't cancel this event request</a>
			<input type="hidden" name="eid" value="<?php echo $eventId ?>" />
			<input type="hidden" name="rtype" value="event" />
			<input type="hidden" name="rid" value="<?php echo $eventId ?>" />
		</div>
	</div>
