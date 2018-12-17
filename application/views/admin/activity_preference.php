<ul class="nav nav-tabs" id="activityPreferenceTabControl">							
	<li class="active"><a href="#view-activity-preferences">Activity Preferences</a></li>
	<li><a href="#add-activity-preferences">Add</a></li>
</ul> 	
<form id="form-activity-preference" class="form-horizontal frm-breakout" method="post" action="/admin/activity_preference">
<fieldset>				 
	<div id="activityPreferenceTab" class="tab-content">
		<div class="tab-pane active" id="view-activity-preferences">
			<?php if(!empty($activityPreferences)): ?>					 
					<table class="table" id="table-activity-preferences">
				 		<tr>
				 			<th>Title</th>
				 			<th>Description</th>			
				 			<th>Actions</th>
				 		</tr>
				 		<?php foreach($activityPreferences as $pref):?>				 		
				 		<tr>
				 			<td><?php echo htmlentities($pref['title'])?></td>
				 			<td><?php echo htmlentities($pref['description'])?></td>
				 			<td>
				 				<a class="icon-black icon-edit btn-edit-activity-preference" title="Edit Activity Preference" data-activity-preference-id="<?php echo $pref['activityPreferenceID']?>" href="#"></a>
				 				<a class="icon-black icon-trash btn-delete-activity-preference" title="Delete Activity Preference" data-activity-preference-id="<?php echo $pref['activityPreferenceID']?>" href="#"></a>				 				
				 			</td>
				 		</tr>		 
				 		<?php endforeach; ?>	
				 	</table>	
			<?php else:?>
				<p> No Activity Preferences. </p>
			<?php endif; ?>
		</div>
		<div class="tab-pane" id="add-activity-preferences">
			<table class="table">	  
			  <tbody>
				<tr>
					<td>Title</td>
					<td class="center"><input class="focused input-xlarge" type="text" name="title" value="<?php echo !empty($breakoutInfo) ? $breakoutInfo['title'] : '';?>" maxlength="100"/></td>			                                       
				</tr>
				<tr>
					<td>Option Display Type</td>
					<td class="center" colspan="3">
						<select name="optionDisplayType" class="focused input-xlarge">
							<option value=""></option>
							<option value="selectbox">Select Box</option>
							<option value="radio">Radio</option>
							<option value="checkbox">Check Box</option>
							<option value="textbox">Text Box</option>
							<option value="textarea">Text Area</option>
						</select>
					</td>			                                   
				</tr>
				<tr>
					<td>Description</td>
					<td class="center"><textarea name="description" rows="5" class="input-xlarge" maxlength="200"><?php echo !empty($breakoutInfo) ? $breakoutInfo['description'] : '';?></textarea></td>			                                   
				</tr>					
				<tr class="activity-options">
					<td>Option 1</td> 
					<td class="center">
						<input class="focused input-medium" type="text" name="options[]" value="" maxlength="100"/>
						<a class="btn btn-add-more-option" href="javascript:;" title="Add more option" >
							<i class="icon icon-darkgray icon-gear"></i>                     
						</a>
						<a class="btn btn-remove-option" href="javascript:;" title="Delete Option">
							<i class="icon-trash icon-black"></i>                     
						</a>						
					</td>
								                                   
				</tr>		
				<input type="hidden" name="activityPreferenceID" value="" />  
				<input type="hidden" name="referenceID" value="<?php echo $getVar['rid'];?>" />
				<input type="hidden" name="eventID" value="<?php echo $getVar['eid'];?>" />
				<input type="hidden" name="referenceType" value="<?php echo $getVar['rtype'];?>" /> 
				<input type="hidden" name="isRequired" value="0" />
			</tbody>
			</table>
		</div>
						
	</div>	
	
	</fieldset>	
  </form>