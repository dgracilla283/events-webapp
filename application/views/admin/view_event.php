<?php include('includes/header.php'); ?>
<div id="content" class="span10">	
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-th"></i> <?php echo $event['title']?></h2>	
				<div class="box-icon"> 
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>			
			</div>
			<div class="box-content">
				<div class="event-image pull-left"> 
					<img src="/img/Logo.jpg" width="100" height="100" /> 
				</div>
				<div class='event-info pull-left'> 
					<ul class="nav nav-tabs" id="myTab">						
						<li class ="active"><a href="#custom">Agenda</a></li>
						<li><a href="#presentation_category">Presentation</a></li>
						<li><a href="#attendees">Attendees</a></li>
						<li><a href="#event_owners">Event Owner</a></li>
						<li><a href="#info">Summary</a></li>
					</ul>					 
					<div id="myTabContent" class="tab-content">						
						<div class="tab-pane active" id="custom">
							<div class="clearfix">
								<a id="add-itinerary" class="btn btn-right" href="#"><i class="icon icon-calendar icon-black"></i> Add Agenda</a>							
							</div>
							<div id="itineraries" class="clearfix"> 
								<span class="loader">&nbsp;</span> <br /> 
							</div>															
						</div>	
						<div class="tab-pane" id="presentation_category">
							<div class="clearfix">
								<a class="btn  btn-right" href="#event-presentation-category-add" id="add-presentation-category" style="margin-right:10px">
									<i class="icon-calendar icon-black"></i>  
									Manage Category                                     
							 	</a>							 										
							</div>
						 	 <div id="presentation_categories"><span class="loader">&nbsp;</span> <br /> </div>
						 <br /> 
						 <br />						 
						</div>						
						<div class="tab-pane" id="attendees">
							<div class="clearfix">
								<a class="btn btn-info btn-right" href="#guests-email" id="email-guests">
									<i class="icon-envelope icon-white"></i>  
									Email Attendees                                           
							 	</a>
								<a class="btn  btn-right" href="#guest-add" id="add-guest" style="margin-right:10px">
									<i class="icon-user icon-black"></i>  
									Manage Event Attendees                                       
							 	</a>							 										
							</div>
						 	 <div id="guests"><span class="loader">&nbsp;</span> <br /> </div>
						 <br /> 
						 <br />						 
						</div>
						<div class="tab-pane" id="event_owners">
							<div class="clearfix">
								<a class="btn  btn-right" href="#event-owner-add" id="add-event-owner" style="margin-right:10px">
									<i class="icon-user icon-black"></i>  
									Manage Event Owner                                       
							 	</a>							 										
							</div>
						 	 <div id="owners"><span class="loader">&nbsp;</span> <br /> </div>
						 <br /> 
						 <br />						 
						</div>
						<div class="tab-pane" id="info">							
							<div class="clearfix">
								
								<a class="btn btn-right" href="/admin/edit_event?id=<?php echo $event['eventID']?>">
									<i class="icon-edit icon-black"></i>  
									Edit                                            
								</a>

								<a class="btn btn-right btn-primary" href="/export/event_attendees/?eid=<?php echo $event['eventID']?>&export=1" style="margin-right:10px">
									<i class="icon-black icon-hdd"></i>   
									Export
								</a>

								
							</div>
							<h3>Event Information:</h3>
							<p><strong>Description:</strong> <?php echo $event['description']?></p>							
							<p><strong>Start:</strong> <?php echo date('l, F d, Y h:i A', strtotime($event['start_date_time']));?></p>
							<p><strong>End:</strong> <?php echo date('l, F d, Y h:i A', strtotime($event['end_date_time']))?> </p> 
							<p><strong>Location: </strong> <?php echo $event['location']?></p>
							<h3>Attendees: </h3>
							<p><strong>Adult: </strong> <?php echo count($users) - $childrenCount;?></p>
							<p><strong>Children: </strong> <?php echo $childrenCount;?></p>							
							<br /> 
						 	<br />
						</div>
						
					</div>
					
				</div>	
					
			</div>
			
	</div><!--/span--> 

</div>
<?php // add program popup ?>
<div class="modal hide fade" id="program-add" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Agenda</h3>
	</div>
	<div class="modal-body">
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="save-program">Save changes</a>
	</div> 
</div>
<?php // edit program popup ?>
<div class="modal hide fade" id="program-edit" data-backdrop="static" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Edit Agenda</h3>
	</div>
	<div class="modal-body">
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn" data-dismiss="modal">Close</a>
		<a href="javascript:;" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // delete program popup ?>
<div class="modal hide fade" id="program-delete" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Agenda</h3>
	</div>
	<div class="modal-body">	
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn" data-dismiss="modal">No</a>
		<a href="javascript:;" class="btn btn-primary" id="confirm-program-delete">Yes</a>
	</div>
</div>
<?php // add  presentation category ?>
<div class="modal hide fade" id="presentation-category-add" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Category</h3>
	</div>
	<div class="modal-body">
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="save-presentation-category">Save changes</a>
	</div> 
</div>
<?php // edit presentation category popup ?>
<div class="modal hide fade" id="presentation-category-edit" data-backdrop="static" data-presentation-category-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Edit Category</h3>
	</div>
	<div class="modal-body">
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn" data-dismiss="modal">Close</a>
		<a href="javascript:;" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // delete presentation category ?>
<div class="modal hide fade" id="presentation-category-delete" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Category</h3>
	</div>
	<div class="modal-body">	
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn" data-dismiss="modal">No</a>
		<a href="javascript:;" class="btn btn-primary" id="confirm-presentation-category-delete">Yes</a>
	</div>
</div>

<?php // Presentation ?>
<div class="modal hide fade" id="presentation-popup" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Presentations</h3>
	</div>
	<div class="modal-body">	
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-save-presentation">Manage Presentations</a>
	</div>
</div>
<?php // add guest popup ?>
<div class="modal hide fade" id="guest-add" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Manage Event Attendees</h3>
	</div>
	<div class="modal-body">
			<span class="loader">&nbsp;</span> <br />  	 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // add event owner popup ?>
<div class="modal hide fade" id="event-owner-add" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Manage Event Owners</h3>
	</div>
	<div class="modal-body">
			<span class="loader">&nbsp;</span> <br />  	 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // add attendees on agenda ?>
<div class="modal hide fade" id="agenda-attendees" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Agenda Attendees</h3>
	</div>
	<div class="modal-body">
			<span class="loader">&nbsp;</span> <br />  	 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // delete user pop-up ?>		
<div class="modal hide fade" id="guest-delete" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Attendee</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-confirm-delete-guest">Yes</a>
	</div>
</div>
<?php // delete owner pop-up ?>		
<div class="modal hide fade" id="event-owner-delete" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Owner</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-confirm-delete-event-owner">Yes</a>
	</div>
</div>
<?php // Add/Edit Breakout ?>		
<div class="modal hide fade" id="breakout-add-edit-popup" data-backdrop="static" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Breakout</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save">Save changes</a>
	</div>
</div>
<?php // delete breakout ?>		
<div class="modal hide fade" id="breakout-delete-popup" data-backdrop="static" role="dialog" data-breakout-id="0" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Activity</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-confirm-delete-breakout">Yes</a>
	</div>
</div>
<?php // Add/Edit activity Attendees with preferences ?>		
<div class="modal hide fade" id="breakout-attendees-popup" data-backdrop="static" role="dialog" data-breakout-id="0" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Activity Attendees</h3>
		<input type="hidden" class="startDateTime" />
		<input type="hidden" class="endDateTime" />
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-add-activity-attendees">Save changes</a>		
	</div>
</div>

<?php // email attendee notification ?>
<div class="modal hide fade" id="guests-email-popup" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Email Attendees of Event Invite</h3>
	</div>
	<div class="modal-body">	
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn" data-dismiss="modal">No</a>
		<a href="javascript:;" class="btn btn-primary" id="btn-confirm-event-email-invite">Yes</a>
	</div>
</div>

<?php // Activity Preference ?>
<div class="modal hide fade" id="activity-preference" data-backdrop="static">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Activity Preferences</h3>
	</div>
	<div class="modal-body">	
		<span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-save-activity-preference">Manage Activity Preferences</a>
	</div>
</div>

<?php include('includes/footer.php'); ?>