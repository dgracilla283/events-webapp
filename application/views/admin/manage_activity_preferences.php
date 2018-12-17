<?php include('includes/header.php'); ?>
<div id="content" class="span10">	
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-th"></i> <?php echo htmlentities($activityInfo['title'])?> - Preferences</h2>	
				<div class="box-icon"> 
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>			
			</div>
			<div class="box-content clearfix">
				<div class="clearfix">										
					<a id="btn-add-preference" class="btn btn-right" href="javascript:;">
						<i class="icon icon-user icon-black"></i>
						Add Preference  
					</a>
					<a id="btn-add-back" class="btn btn-right" href="/admin/view_event?id=<?php echo $getVar['eid']?>" style="margin-right: 10px">
						<i class="icon icon-undo icon-black"></i>
						Back to event
					</a>
				</div>
				<?php if(!empty($activityPreferences)): ?>
				<div id="view-activity-preferences">
						<table class="table" id="table-activity-preferences">
					 		<tr>
					 			<th width="20%">Title</th>
					 			<th width="30%">Description</th> 			
					 			<th width="20%">Display Type</th>
					 			<th width="20%">Options</th>
					 			<th>Actions</th>
					 		</tr>
					 		<?php foreach($activityPreferences as $pref):?>				 		
					 		<tr>
					 			<td><?php echo htmlentities($pref['title'])?></td>
					 			<td><?php echo htmlentities($pref['description'])?></td>		 			
					 			<td><?php echo htmlentities($pref['optionDisplayType'])?></td>
					 			<td>
					 				<?php 
					 					$options = array();
						 				foreach($pref['options'] as $option) : 
						 					$options[] = $option['title']; 
						 				endforeach; 
						 				echo implode(', ', $options); 
					 				?> 
					 				
					 			</td>
					 			<td class="center"> 				 										
					 				<a class="btn btn-edit-preference" href="#" data-apid="<?php echo $pref['activityPreferenceID'];?>" title="Edit Preference"><i class="icon-black icon-edit"></i></a>
									<a class="btn btn-delete-preference" href="#" data-apid="<?php echo $pref['activityPreferenceID'];?>" title="Delete Preference"><i class="icon-black icon-trash"></i></a>
								</td>  
					 		</tr>		 
					 		<?php endforeach; ?>	
					 	</table>					
				</div>
				<?php else:?>
					 <div class="alert alert-error">	
						<strong>No Activity Preferences.</strong>
					</div>					 	
				<?php endif; ?>
						
			</div>			
	</div><!--/span--> 

</div>

<?php // Add/Edit activity Attendees?>		
<div class="modal hide fade" id="add-activity-preference-popup" data-backdrop="static" role="dialog">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Activity Preference</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-save-activity-preference">Save Changes</a>
	</div>
</div>

<?php // Add/Edit activity Attendees?>		
<div class="modal hide fade" id="delete-activity-preference-popup" data-backdrop="static" role="dialog">
	<div class="modal-header">				
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Activity Preference</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br /> 
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-yes-delete-activity-preference">Yes</a>
	</div>
</div>

<?php include('includes/footer.php'); ?>