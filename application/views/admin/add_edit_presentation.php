<form id="form-presentation" class="form-horizontal frm-presentation" method="post" action="/admin/add_edit_presentation">
	<fieldset>
		<div class="control-group">
			<label class="control-label text-left">Title</label>
			<div class="controls">
				<input class="focused input-xlarge" type="text" name="title" value="<?php echo !empty($presentation) ? $presentation['title'] : '';?>" maxlength="100"/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label text-left">Link</label>
			<div class="controls">
				<input class="focused input-xlarge" type="text" name="url" value="<?php echo !empty($presentation) ? $presentation['url'] : '';?>" maxlength="100"/>
			</div>
		</div>
		<input type="hidden" name="presentation_category_id" value="<?php echo $getVar['presentation_category_id'];?>" />
		<input type="hidden" name="presentationID" value="<?php echo $getVar['pid'];?>" />
	</fieldset>
</form>