<form id="form-edit-presentation-category" class="form-horizontal form-presentation-category" method="post" action="/admin/save_presentation_category">
	<fieldset>			
	  <div class="control-group">
		<label class="control-label">Name</label>
		<div class="controls">
		  <input class="input-xlarge" type="text" name="name" value="<?php echo htmlentities($presentation_category['name']);  ?>" maxlength="50"/>
		</div>
	  </div>	
 	</table> 	
	<input type="hidden" value="<?php echo $presentation_category['event_id'];?>" name="event_id" />			 		
	<input type="hidden" value="<?php echo $presentation_category['presentationCategoryID']; ?>" name="presentationCategoryID" />	
	</fieldset>
  </form>
  <?php exit; ?>