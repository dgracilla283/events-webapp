<?php include('includes/header.php'); ?>
	<div id="content" class="span10">
		<div class="row-fluid sortable">
			<div class="box span12">
				<div class="box-header well">
					<h2><i class="icon-edit"></i>Duplicate Event</h2>
					<div class="box-icon">
						<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
					</div>
				</div>
				<div class="box-content">
					<?php
						$transaction        = $this->session->flashdata('isSuccess');
						$transactionMessage = $this->session->flashdata('message');
						if (empty($transaction)) {
							$transaction        = $isSuccess;
							$transactionMessage = $message;
						}
					?>
					<?php if (!empty($transaction)) { ?>
						<?php $alertClass = 'alert-' . $transaction; ?>
						<div class="alert <?php echo $alertClass; ?>">
							<strong><?php echo $transactionMessage; ?></strong>
						</div>
					<?php } ?>

					<form name="duplicate_event_form" id="duplicate_event_form" enctype="multipart/form-data" method="post" class="form-horizontal">
						<input type="hidden" value="<?php echo $event['eventID']?>" name="eventID" />
						<input type="hidden" value="<?php echo $event['eventID']?>" name="currentEventID" />
						<fieldset>
							<div class="control-group">
								<label class="control-label"> Include Attendees?</label>
								<div class="controls">
									<?php
										$value = $this->input->post('include_attendees');
										if (empty($value)) {
											$value = '';
										} else {
											$value = 'checked="checked"';
										}
									?>
									<input type="checkbox" name="include_attendees" <?php echo $value; ?> />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="focusedInput">Event Title</label>
								<div class="controls">
									<?php
										$value = set_value('title');
										$value = (empty($value)) ? $event['title'] : $value;
									?>
									<input class="input-xlarge txtTitle" type="text" name="title" value="<?php echo $value; ?>">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">Description</label>
								<div class="controls">
									<?php
										$value = set_value('description');
										$value = (empty($value)) ? $event['description'] : $value;
									?>
									<textarea name="description" class="input-xlarge" rows="5"><?php echo $value; ?></textarea>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="focusedInput">Event Location</label>
								<div class="controls">
									<?php
										$value = set_value('location');
										$value = (empty($value)) ? $event['location'] : $value;
									?>
									<input name="location" class="input-xlarge focused" id="location" type="text" value="<?php echo $value; ?>" maxlength="150">
								</div>
							</div>
							<div class="row-fluid">
								<div class="span4">
									<div class="control-group control-date-group">
										<label class="control-label" for="disabledInput">Date Start</label>
										<div class="controls">
											<?php
												$value = set_value('start_date_time');
												$value = (empty($value)) ? date('m-d-Y',strtotime($event['start_date_time'])) : $value;
											?>
											<input class="input-small" type="text" name="start_date_time" id="date-start" value="<?php echo $value; ?>" />
											<span class="hint">mm/dd/yyyy</span>
										</div>
									</div>
								</div>
								<div class="span8">
									<div class="control-group control-date-group">
										<label class="control-label" for="optionsCheckbox2">Time Start</label>
										<div class="controls">
											<?php
												$value = set_value('start_time');
												$value = (empty($value)) ? date('h:i A',strtotime($event['start_date_time'])) : $value;
											?>
											<input class="input-mini" type="text" name="start_time" id="time-start" value="<?php echo $value; ?>" />
											<span class="hint">24h format (hh:mm)</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span4">
									<div class="control-group control-date-group">
										<label class="control-label" for="optionsCheckbox2">Date End</label>
										<div class="controls">
											<?php
												$value = set_value('end_date_time');
												$value = (empty($value)) ? date('m-d-Y',strtotime($event['end_date_time'])) : $value;
											?>
											<input class="input-small" type="text" name="end_date_time" id="date-end" value="<?php echo $value; ?>" />
											<span class="hint">mm/dd/yyyy</span>
										</div>
									</div>
								</div>
								<div class="span8">
									<div class="control-group control-date-group">
										<label class="control-label" for="optionsCheckbox2">Time End</label>
										<div class="controls">
											<?php
												$value = set_value('end_time');
												$value = (empty($value)) ? date('h:i A',strtotime($event['end_date_time'])) : $value;
											?>
											<input class="input-mini" type="text" name="end_time" id="time-end" value="<?php echo $value; ?>"/>
											<span class="hint">24h format (hh:mm)</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row-fluid">
								<div class="control-group">
									<label class="control-label" for="optionsCheckbox2">Publish Event</label>
									<div class="controls">
										<?php
											$value = $this->input->post('status');
											if (empty($value)) {
												if ($event['status'] === '1') {
													$value = 'checked="checked"';
												}
											} else {
												$value = 'checked="checked"';
											}
										?>
										<input type="checkbox" name="status" id="s-status" <?php echo $value; ?> />
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="optionsCheckbox2">Attendees Limit</label>
									<div class="controls">
										<?php
											$value = set_value('attendees_limit');
											$value = (empty($value)) ? $event['attendees_limit'] : $value;
										?>
										<input class="input-large" type="text" name="attendees_limit" id="attendees-limit" value="<?php echo $value; ?>" />
									</div>
								</div>

								<div class="control-group">
									<label class="control-label">Additonal Info (dress code, logistics, requirements, etc.)</label>
									<div class="controls">
										<?php
											$value = set_value('additional_info');
											$value = (empty($value)) ? $event['additional_info'] : $value;
										?>
										<textarea name="additional_info" id="additional-info" rows="5" class="input-xlarge"><?php echo $value; ?></textarea>
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
							</div>
							<div class="control-group">
								<label class="control-label">Itinerary Dates</label>
								<?php
									$eventDates = array();
									$newDate = true;
									$ctr = 0;
									foreach ($itineraries as $itinerary) {
										$programStartDate = date('m-d-Y', strtotime(str_replace('-', '/',$itinerary['start_date_time'])));
										$programStartTime = date('h:i A', strtotime($itinerary['start_date_time']));
										$programEndDate = date('m-d-Y', strtotime(str_replace('-', '/',$itinerary['end_date_time'])));
										$programEndTime =	date('h:i A', strtotime($itinerary['end_date_time']));
										if(!in_array($programStartDate, $eventDates)){
											$eventDates[] = $programStartDate;
											$newDate = true;
											$ctr++;
										} else {
											$newDate = false;
										}

										if ($newDate) {
									 ?>
											<div class="controls">
												<?php $itinerary_date_arr = explode(" ", $itinerary['start_date_time']); ?>
												<label class="control-label text-left">Event Day <?php echo $ctr;?> </label>
												<?php
													$itineraryDateLabel = 'itinerary_date_' . $itinerary_date_arr[0];
													$value = set_value($itineraryDateLabel);
													$value = (empty($value)) ? date('m-d-Y',strtotime($itinerary['start_date_time'])) : $value;
												?>
												<input class="input-small itinerary_date" type="text" name="itinerary_date_<?php echo $itinerary_date_arr[0];?>" value="<?php echo $value; ?>" />
												<span class="hint">mm/dd/yyyy</span>
											</div>
											<br />
								<?php
										}
									}
								?>
							</div>
						</fieldset>
						<div class="form-actions">
							<button type="submit" id="btn-confirm-event-duplicate" class="btn btn-primary">Save changes</button>
							<a style="margin-right:10px" href="/admin/dashboard" class="btn">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php include('includes/footer.php'); ?>




