<form id="form-add-presentation-category" class="form-horizontal form-presentation-category" method="post" action="/admin/save_presentation_category">
	<fieldset>
		<div class="control-group">
			<label class="control-label text-left">Name</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="name" value="" maxlength="50"/>
			</div>
		</div>
		<input type="hidden" value="<?php echo $eventId;?>" name="event_id" />
		<input type="hidden" value="" name="presentationCategoryID" />
	</fieldset>
</form>