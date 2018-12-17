<form class="form-horizontal" method="post" action="/admin/add_edit_guest" id="form-add-edit-guest">
	<fieldset>		
		<div class="control-group">		
			<label class="control-label" for="focusedInput">Email</label>
			<div class="controls">	  		 
		  		<input class="input-xlarge focused" id="email" type="text" name="email" value="<?php echo !empty($userInfo) ? $userInfo['email'] : '';?>" maxlength="30" />		
			</div>
		</div>
		<div class="control-group">		
			<label class="control-label" for="focusedInput">First Name</label>
			<div class="controls">	  		 
		  		<input class="input-xlarge focused" id="first-name" type="text" name="first_name" value="<?php echo !empty($userInfo) ? $userInfo['first_name'] : '';?>" maxlength="30">		
			</div>
		</div>
		<div class="control-group">		
			<label class="control-label" for="focusedInput">Last Name</label>
			<div class="controls">	  		 
		  		<input class="input-xlarge focused" id="last-name" type="text" name="last_name" value="<?php echo !empty($userInfo) ? $userInfo['last_name'] : '';?>" maxlength="30">		
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="focusedInput">Position / Title</label>
			<div class="controls">
				<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['title'] : '';?>" name="title" maxlength="100">
			</div>
		</div>		
		<div class="control-group">
			<label class="control-label" for="focusedInput">Company</label>
			<div class="controls">
				<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['affiliation'] : '';?>" name="affiliation" maxlength="100">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="focusedInput">Industry</label>
			<div class="controls">
				<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['industry'] : '';?>" name="industry" maxlength="100">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="focusedInput">Bio</label>
			<div class="controls">
				<textarea name="description" id="description" rows="5" class="input-xlarge" maxlength="500"><?php if(!empty($userInfo)) {echo htmlentities($userInfo['bio']);}?></textarea>
			</div>
		</div>
		<input type="hidden" name="userID" value="<?php echo !empty($userInfo) ? $userInfo['userID'] : '';?>" /> 		
	</fieldset>
 </form>