<?php include('includes/header.php'); ?>
<div id="content" class="span10">	
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-th"></i> <?php echo htmlentities($activityInfo['title'])?> - Attendees</h2>	
				<div class="box-icon"> 
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>			
			</div>
			<div class="box-content clearfix">
				<div class="clearfix">										
					<a id="btn-add-attendee" class="btn btn-right" href="javascript:;">
						<i class="icon icon-user icon-black"></i>
						Add Attendee  
					</a>
					<a id="btn-add-back" class="btn btn-right" href="/admin/view_event?id=<?php echo $getVar['eid']?>" style="margin-right: 10px">
						<i class="icon icon-undo icon-black"></i>
						Back to event
					</a>
				</div>
				<?php if(!empty($activityAttendees)): ?>
					<table class="table table-striped">
						  <thead>
							  <tr>
								  <th>Name</th>
								  <th>Position/Title</th>
								  <th>Company</th>
								  <th>Industry</th>
								  <th>Actions</th>                              
							  </tr>
						  </thead>   
						  <tbody>
						  	<?php foreach($activityAttendees as $guest): ?>  
							<tr>
								<td width="20%"><?php echo $guest['last_name'].', ' .$guest['first_name']?></td>			
								<td width="30%"><?php echo $guest['title'];?></td>
								<td width="20%"><?php echo $guest['affiliation'];?></td>
								<td width="15%"><?php echo $guest['industry'];?></td>
								<td class="center"> 				 										
									<a class="btn btn-edit-attendee" href="#" data-user-id="<?php echo $guest['userID'];?>"" title="Edit Attendee"><i class="icon-black icon-edit"></i></a>
									<a class="btn btn-delete-attendee" href="#" data-user-id="<?php echo $guest['userID'];?>"" title="Delete Attendee"><i class="icon-black icon-trash"></i></a>
								</td>                                       
							</tr>
							<?php endforeach; ?>								
						  </tbody>
					 </table>  
					 <?php else: ?>
					  <div class="alert alert-error">	
						<strong>No attendees!</strong>
					</div>    	
				<?php endif; ?>							
			</div>			
	</div><!--/span--> 

</div>

<?php // Add/Edit activity Attendees?>		
<div class="modal hide fade" id="add-attendee-popup" data-backdrop="static" role="dialog" data-breakout-id="0" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Activity Attendee</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-save-attendee">Save Changes</a>
	</div>
</div>
<?php //Delete Activity Attendees?>		
<div class="modal hide fade" id="delete-attendee-popup" data-backdrop="static" role="dialog" data-breakout-id="0" data-program-id="0">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Activity Attendee</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-yes-delete-attendee">Yes</a>
	</div>
</div>
<?php include('includes/footer.php'); ?>