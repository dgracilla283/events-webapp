<form id="form-activity-preference" class="form-horizontal frm-breakout" method="post" action="/admin/add_edit_activity_preference">
<table class="table">	  
<tbody>
<tr>
	<td>Title</td>
	<td class="center"><input class="focused input-xlarge" type="text" name="title" value="<?php echo !empty($preferenceInfo) ? $preferenceInfo['title'] : '';?>" maxlength="100"/></td>			                                       
</tr>
<tr>
	<td>Option Display Type</td>
	<td class="center" colspan="3">
		<?php 
			$optionDisplayType = !empty($preferenceInfo['optionDisplayType']) ? $preferenceInfo['optionDisplayType'] : ''; 
		?> 
		<select name="optionDisplayType" class="focused input-xlarge">
			<option value=""></option>
			<option value="selectbox"<?php echo ($optionDisplayType == 'selectbox') ? ' selected':''?>>Select Box</option>
			<option value="radio"<?php echo ($optionDisplayType == 'radio') ? ' selected':''?>>Radio</option>
			<option value="checkbox"<?php echo ($optionDisplayType == 'checkbox') ? ' selected':''?>>Check Box</option>
			<option value="textbox"<?php echo ($optionDisplayType == 'textbox') ? ' selected':''?>>Text Box</option>
			<option value="textarea"<?php echo ($optionDisplayType == 'textarea') ? ' selected':''?>>Text Area</option>
		</select>
	</td>			                                   
</tr>
<tr>
	<td>Description</td>
	<td class="center"><textarea name="description" rows="5" class="input-xlarge" maxlength="2000"><?php echo !empty($preferenceInfo) ? $preferenceInfo['description'] : '';?></textarea></td>			                                   
</tr>	
<?php 
$count = 1; 
if(!empty($preferenceInfo['options'])): 
	foreach($preferenceInfo['options'] as $option): 
?>				
<tr class="activity-options">
	<td>Option <?php echo $count ?></td> 
	<td class="center">		
		<input class="focused input-medium" type="text" name="options[<?php echo $option['activityPreferenceOptionID'];?>]" value="<?php echo $option['title'];?>" maxlength="200"/>
		<a class="btn btn-add-more-option" href="javascript:;" title="Add more option" >
			<i class="icon icon-darkgray icon-gear"></i>                     
		</a>
		<a class="btn btn-remove-option" href="javascript:;" title="Delete Option">
			<i class="icon-trash icon-black"></i>                     
		</a>		
	</td>				                                   
</tr>	
<? $count++; endforeach; else: ?>
<tr class="activity-options">
	<td>Option 1</td> 
	<td class="center">	
		<input class="focused input-medium" type="text" name="options[]" value="" maxlength="200"/>
		<a class="btn btn-add-more-option" href="javascript:;" title="Add more option" >
			<i class="icon icon-darkgray icon-gear"></i>                     
		</a>
		<a class="btn btn-remove-option" href="javascript:;" title="Delete Option">
			<i class="icon-trash icon-black"></i>                     
		</a>			
	</td> 
</tr> 
<?php endif; ?> 
	
<input type="hidden" name="activityPreferenceID" value="<?php echo $getVar['apid'];?>" />  
<input type="hidden" name="referenceID" value="<?php echo $getVar['id'];?>" />
<input type="hidden" name="eventID" value="<?php echo $getVar['eid'];?>" />
<input type="hidden" name="referenceType" value="<?php echo $getVar['rtype'];?>" /> 
<input type="hidden" name="isRequired" value="0" />
</form> 