<?php $this->load->view('admin/includes/header') ?>



<div id="content" class="span10">	
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-th"></i> <?php echo 'Upload Map\'s Photo' ?></h2>	
				<div class="box-icon"> 
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>			
			</div>
			<div class="box-content clearfix">
				<div class="clearfix">										
					<a id="btn-add-back" class="btn btn-right" href="/admin/view_event?id=<?php echo $eventId ?>" style="margin-right: 10px">
						<i class="icon icon-undo icon-black"></i>
						Back to event
					</a>
				</div>
				
				
<form enctype="multipart/form-data" method="post" action="/admin/upload_map">
<fieldset>
	<div class="control-group">
		<label class="control-label">Upload Map Photo</label>
		<div class="controls">
			<input type="file" name="map_agenda_photo" id="map_agenda_photo"  class="input-medium focused" />
		</div>
	</div>

	<div class="form-actions">
		<input type="hidden" name="event_id" id="event-id" value="<?php echo $eventId ?>" />
		<input type="hidden" name="reference_type" id="reference-type" value="<?php echo 'itinerary' ?>" />
		<input type="hidden" name="reference_id" id="reference-id" value="<?php echo $itineraryId ?>" />
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button class="btn" id="cancel-add">Cancel</button>
	</div>

</fieldset>
</form>
						
			</div>			
	</div><!--/span--> 

</div>
</div>

<?php $this->load->view('admin/includes/footer') ?>