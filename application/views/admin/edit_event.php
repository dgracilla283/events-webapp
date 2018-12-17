<?php include('includes/header.php'); ?>

<div id="content" class="span10">

	<div class="row-fluid sortable">

		<div class="box span12">

			<div class="box-header well" data-original-title>

				<h2><i class="icon-edit"></i>

					<?php if(!empty($event)):

							echo $event['title']. '- Edit Event';

						else:

							echo 'Add Event';

						endif;

					?>

				</h2>

				<div class="box-icon">

					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>

				</div>

			</div>

			<div class="box-content">

				<?php if(!empty($edited)): ?>

				<div class="alert alert-success">

					<strong>Event successfully saved! <a href="/admin/view_event?id=<?php echo $event['eventID'];?>">Click here</a> to view itineraries.</strong>

				</div>

				<?php endif; ?>

				<form class="form-horizontal" method="post" action="" id="form-event" enctype="multipart/form-data">

					<fieldset>

					  <div class="control-group">

						<label class="control-label" for="focusedInput">Event Title</label>

						<div class="controls">

						  <input name="title" class="input-xlarge focused" id="title" type="text" value="<?php if(!empty($event)) { echo htmlentities($event['title']); }?>" maxlength="100">

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label" for="focusedInput">Event Location</label>

						<div class="controls">

						  <input name="location" class="input-xlarge focused" id="location" type="text" value="<?php if(!empty($event)) { echo htmlentities($event['location']); }?>" maxlength="150">

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label">Description</label>

						<div class="controls">

						  <textarea name="description" id="description" rows="5" class="input-xlarge" maxlength="800"><?php if(!empty($event)) {echo htmlentities($event['description']);}?></textarea>

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label" for="disabledInput">Date Start</label>

						<div class="controls">

						  <input class="input-medium" type="text" name="start_date_time" id="date-start" value="<?php if(!empty($event)) { echo date('m-d-Y',strtotime($event['start_date_time'])); }?>" />

						  <input class="input-mini" type="text" name="start_time" id="time-start" value="<?php if(!empty($event)) { echo date('h:i A',strtotime($event['start_date_time'])); }?>" readonly />

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label" for="optionsCheckbox2">Date End</label>

						<div class="controls">

							<input class="input-medium" type="text" name="end_date_time" id="date-end" value="<?php if(!empty($event)) { echo date('m-d-Y',strtotime($event['end_date_time'])); }?>"/>

							<input class="input-mini" type="text" name="end_time" id="time-end" value="<?php if(!empty($event)) { echo date('h:i A',strtotime($event['end_date_time'])); }?>" readonly />

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label" for="optionsCheckbox2">Publish Event</label>

						<div class="controls">

							<input type="checkbox" name="status" id="s-status" <?php if(!empty($event)) { echo ($event['status'] === '1') ? 'checked' : ''; }?> />

						</div>

					  </div>

					  <div class="control-group">

						<label class="control-label" for="optionsCheckbox2">Attendees Limit</label>

						<div class="controls">

							<input class="input-large" type="textbox" name="attendees_limit" id="attendees-limit" value="<?php if(!empty($event)) { echo $event['attendees_limit']; }?>" />

						</div>

					  </div>


					  <div class="control-group">

						<label class="control-label">Additonal Info (dress code, logistics, requirements, etc.)</label>

						<div class="controls">

						  <textarea name="additional_info" id="additional-info" rows="5" class="input-xlarge"><?php if(!empty($event)) { echo $event['additional_info']; }?></textarea>

						</div>

					  </div>

					  <div class="control-group">
							<label class="control-label">Map Facility Photo</label>
							<div class="controls">
								<?php if(!empty($mapPhotos)): ?>
								<?php foreach($mapPhotos as $mapPhoto): ?>
								<div class="map-photo-item">
									<input type="hidden" name="map_photo_item_fname[]" value="<?php echo $mapPhoto['s_fname'] ?>" />
									<input type="hidden" name="map_photo_item_id[]" value="<?php echo $mapPhoto['mapPhotoID'] ?>" />
									<div class="map-photo-item-title"><?php echo $mapPhoto['title'] ?></div>
									<img src="/img/upload/map/<?php echo $mapPhoto['s_fname']?>" width="150" />
									<div class="map-photo-item-action">
										<input type="checkbox" name="map_photo_item_remove[]" value="<?php echo $mapPhoto['mapPhotoID'] ?>" /> Remove photo
									</div>
								</div>
								<?php endforeach; ?>
								<?php endif; ?>
								<div id="map-photo-item-new"></div>
								<a href="javascript:void(0);" id="add-more-maps">[+] Add More Map Photos</a>
							</div>

					  </div>

					  <div class="form-actions">

						<button type="submit" class="btn btn-primary">Save changes</button>

						<a style="margin-right:10px" href="/admin/dashboard" class="btn">Cancel</a>

					  </div>

					</fieldset>

					<input type="hidden" name="eventID" value="<?php if(!empty($event)) { echo $event['eventID']; }?>" />

				  </form>



			</div>

		</div><!--/span-->

		</div><!--/row-->

	</div>

<?php include('includes/footer.php'); ?>