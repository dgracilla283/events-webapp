<form id="form-add-itinerary" class="form-horizontal form-itinerary" method="post" action="/admin/save_itinerary">
			<fieldset>			
			  <div class="control-group">
				<label class="control-label">Title</label>
				<div class="controls">
				  <input class="input-xlarge" type="text" name="title" value="" maxlength="50"/>
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label">Description</label>
				<div class="controls">
				  <textarea name="description" rows="5" class="input-xlarge" maxlength="200"></textarea>
				</div>
			  </div>			  		  
			  <div class="control-group">
				<label class="control-label" for="disabledInput">Location</label>
				<div class="controls">
				  <input class="input-xlarge" type="text" name="location" value="" maxlength="50"/>
				</div>
			  </div>
			   <div class="control-group">
				<label class="control-label" for="disabledInput">Location Map Photo</label>
				<div class="controls">
				  <!-- <input class="input-xlarge" type="text" name="location" value="" maxlength="50"/> -->
				  <select name="map_photo_id">
				  <option value=""></option>
				  <?php if(!empty($mapPhotos)): ?>
				  <?php foreach($mapPhotos as $mapPhoto): ?>
				  <option value="<?php echo $mapPhoto['mapPhotoID'] ?>"><?php echo $mapPhoto['title'] ?></option>
				  <?php endforeach; ?>
				  <?php endif; ?>
				  </select>
				</div>
			  </div>
			  <div class="speakers-container">
				  <div class="control-group">
					<label class="control-label" for="disabledInput">Speaker</label>
					<div class="controls">						
					  <select class="focused input-medium speaker-select" type="text" name="user_id[]">
			  			<option value=""></option>
				  		<?php foreach($attendees as $attendee): ?>			  				  		
			  				<option value="<?php echo $attendee['userID']?>"><?php echo $attendee['last_name'].', '.$attendee['first_name']?></option>
			  			<?php endforeach; ?> 
			  		  </select>
			  		  <a class="btn btn-add-more-speaker" href="javascript:;" title="Add more speaker" >
						<i class="icon-user icon-black"></i>                     
					  </a>
					  <a class="btn btn-remove-speaker" href="javascript:;" title="Delete speaker">
						<i class="icon-trash icon-black"></i>                     
					  </a>  
					</div>
				 </div>
			 </div>		
			<div class="control-group">
				<label class="control-label" for="disabledInput">Date Start</label>
				<div class="controls">
				  <input class="input-small datepicker date-start" type="text" name="start_date_time" value="" />
				  <input class="input-mini timepicker" type="text" name="start_time" value="" readonly />
				</div>
			</div>			
			<div class="control-group">
				<label class="control-label" for="disabledInput">Date End</label>
				<div class="controls">
				  <input class="input-small datepicker date-end" id="date-end" type="text" name="end_date_time" value="" />
				  <input class="input-mini timepicker" type="text" name="end_time" value="" readonly />
				</div>
			</div>		
			<div class="control-group">
				<label class="control-label" for="disabledInput">Breakout Session?</label>
				<div class="controls">
				  <input class="input-mini" type="checkbox" name="breakout_status" value="1" /><span class="hint" title="Check if this itinerary is a breakout session">[?]</span>
				</div>
			</div>	
			<div class="control-group">
				<label class="control-label" for="disabledInput">Attendees Limit <span class="hint" title="Leave it blank for unlimited attendees.">[?]</span></label>
				<div class="controls">
				   <input class="input-xlarge" type="text" name="attendees_limit" value="<?php echo $itinerary['attendees_limit']?>"/>
				</div>
			</div>	
			<input type="hidden" value="<?php echo $eventId;?>" name="event_id" />			 		
			<input type="hidden" value="" name="itineraryID" />
			</fieldset>
		  </form>