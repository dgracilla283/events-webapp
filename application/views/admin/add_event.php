<?php include('includes/header.php'); ?>
<div id="content" class="span10">	
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well" data-original-title>
				<h2><i class="icon-edit"></i> Add Event</h2>
				<div class="box-icon"> 
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class="icon-remove"></i></a>
				</div>			
			</div> 
			<div class="box-content">
				
				<form class="form-horizontal" method="post" action="/admin/add_event">
					<fieldset>
					  <div class="control-group">
						<label class="control-label" for="focusedInput">Event Title</label>
						<div class="controls">
						  <input class="input-xlarge focused" id="focusedInput" type="text" name="title" value="">
						</div>
					  </div>
					   <div class="control-group">
						<label class="control-label" for="focusedInput">Location</label>
						<div class="controls">
						  <input class="input-xlarge focused" id="focusedInput" type="text" name="location" value="">
						</div>
					  </div>
					  <div class="control-group">
						<label class="control-label">Description</label>
						<div class="controls">
						  <textarea name="description" row="100" column="100"></textarea>
						</div>
					  </div>
					  
					  <div class="control-group">
						<label class="control-label" for="disabledInput">Date Start</label>
						<div class="controls">
						  <input class="input-small" type="text" name="date_start" /><span class="hint">mm/dd/yyyy</span>
						</div>
					  </div>
					  <div class="control-group">
						<label class="control-label" for="optionsCheckbox2">Date End</label>
						<div class="controls">						 
							<input class="input-small" type="text" name="date_end" /><span class="hint">mm/dd/yyyy</span>
						</div>
					  </div>					 
					   <div class="control-group">
						<label class="control-label" for="optionsCheckbox2">Time Start</label>
						<div class="controls">						 
							<input class="input-mini" type="text" name="time_start" /><span class="hint">24h format (hh:mm)</span>
						</div>
					  </div>					 
					   <div class="control-group">
						<label class="control-label" for="optionsCheckbox2">Time End</label>
						<div class="controls">						 
							<input class="input-mini" type="text" name="time_end" /><span class="hint">24h format (hh:mm)</span>
						</div>
					  </div>					 
					  <div class="form-actions">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button class="btn" id="cancel-add">Cancel</button>
					  </div>
					</fieldset>
				  </form>
			
			</div>
		</div><!--/span-->
		</div><!--/row-->
	</div> 	
<?php include('includes/footer.php'); ?>