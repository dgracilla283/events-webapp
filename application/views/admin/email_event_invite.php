<div class="modal-header">				
	<button type="button" class="close" data-dismiss="modal">x</button>
	<h3>Email Attendees of Event Invite</h3>
</div>
<div class="modal-body">
	<p>
	<?php if( !isset($result['error']) ):?>
		Emails were succesfully sent
	<?php else: ?>
		<?php echo $result['error'] ?>
	<?php endif; ?>
	</p>
</div>
<div class="modal-footer">
	<a href="javascript:;" class="btn btn-primary" data-dismiss="modal">Ok</a>
</div>

