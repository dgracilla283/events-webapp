<?php $this->load->view('webapp/includes/header.php') ?>
  <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home" data-ajax="false">HOME</a> 
	        <a id="btn-back" href="/webapp/dashboard?id=<?php echo $event['eventID'];?>" data-ajax="false">BACK</a>
	        <h3>Event Details Confirmation</h3>                
	    </div>
     </div>      
   
<div id="add-companions" class="form-join-event-container">
 
	<form id="form-join-event" data-redirect="/webapp/event_details?id=<?php echo $eventId ?>">
	       	<div>
	    	   	<div class="fontSize14px">Please select the companions you want to include in <b><?php echo $event['title'] ?></b>:</div>
	        	<fieldset data-role="controlgroup">
	        	 
	        	<?php foreach($companions as $key => $companion): ?>
				<div class="companion-container" style="height: 15px; margin-top:10px">
				<input style="margin-top:3px;" type="checkbox" name="companions[]" value="<?php echo $companion['userID'] ?>" />
				<label style="margin-left: 45px;" class="fontSize14px" ><?php echo $companion['first_name'] . ' ' . $companion['last_name'] ?></label>
				</div>
	        	<?php endforeach ?>
				
				<input type="hidden" name="eid" value="<?php echo $eventId ?>" />
				<input type="hidden" name="rtype" value="event" />  
				<input type="hidden" name="rid" value="<?php echo $eventId ?>" />  
				<br/>
				<a href="#popupAddCompanion" class="join_button" data-inline="true" id="add-more-companion" data-rel="popup" data-position-to="window" title="[+] Add More Companion?">Add New Companion?</a>
				<br/><br/>
						<a href="javascript:void(0);" class="aBtn" id="btn-form-join-event" title="Join Event">Join Event</a>
						<br />
						<a href="javascript:void(0);" class="aBtn" data-rel="back" data-transition="flow" title="Cancel">Go Back</a> 
	    	</fieldset>
		</div>
	</form>
</div>

<div data-role="popup" id="popupAddCompanion">
	<form id="form-add-companion" data-redirect="/webapp/join_companion?id=<?php echo $eventId ?>" data-ajax="false">
    	   	<div class="popup_header fontSize14px">Add new companion:</div>
        	<fieldset data-role="controlgroup">
        		<label>First Name:</label>
        		<input type="text" name="first_name" placeholder="First Name" />
        		<label>Last Name:</label>
        		<input type="text" name="last_name" placeholder="Last Name" />
        		 <select name="type">
        			<option selected="selected" value="adult">Adult</option>
        			<option value="child">Child</option>
        		</select>
        		<div class="error"></div>
        		<div class="form-actions popup_form_btn">
	        		<a href="javascript:void(0);" data-role="button" data-inline="true" id="btn-form-add-companion" >Add Companion</a>
					<a href="javascript:void(0);" data-role="button" data-inline="true" data-rel="back" data-transition="flow">Cancel</a>
				</div> 
	    	</fieldset>
	</form>
</div>

<?php $this->load->view('webapp/includes/footer.php') ?>