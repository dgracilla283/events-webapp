<?php include('includes/header.php'); ?>
    	<div id='update_user_information'>
	        <form id="form-user" class="form-horizontal form-user" autocomplete="off" method="post" enctype="multipart/form-data" novalidate="novalidate">
	            <input type="hidden" name="is_primary" value="1">
	            <?php if (!empty($error)):?>
				<div class="alert alert-error">
					<?php
						echo $error;
					?>
				</div>
				<?php endif;?>           
	           	<div class="control-group" style="width:100%;">
	            	<label for='email' style="float:left;">Email</label>
	            	<label class="error" for="email" style="float:left;"></label>
	            </div>
	            <input type='text' name='email' id='email' size='25' value=""/>
	            
	            <label for='username'>First Name</label>
	            <input type='text' name='first_name' id='first_name' size='25'  value=""/>
	            <label for='username'>Last Name</label>
	            <input type='text' name='last_name' id='last_name' size='25'  value=""/>
	            <label for='username'>Affiliation</label>
	            <input type='text' name='affiliation' id='affiliation' size='25'  value=""/>
	            <label for='username'>Industry</label>
	            <input type='text' name='industry' id='industry' size='25'  value=""/>
	            <label for='username'>Title</label>
	            <input type='text' name='title' id=''title'' size='25'  value=""/>
	            <label for='username'>Bio</label>
	           	<textarea maxlength="500" class="input-xlarge" rows="5" id="user-bio" name="bio"></textarea>     
	           	<input type="hidden" name="userPhotoID" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['userPhotoID'] : '';?>" />
				<input type="hidden" name="s_current_photo" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'] : '';?>" />
				<label class="control-label" for="focusedInput">Photo</label>
				<div class="photo_controls">
					<?php 
					if (!empty($userInfo)) :
						if (!empty($userInfo) && !empty($userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'])) { ?>
							<img src="/img/upload/user/<?php echo $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname']?>" width="48" />
					<?php 
						} else {
					?>
							<img src="/img/no_photo_icon.png" width="48" />
					<?php 
						}
					endif;?>
					<br />
						<div class="remove_photo">
							<input type="checkbox" name="s_remove_photo" />
							<label class="control-label" for="focusedInput">Remove photo</label>
							<br />
							<input type="file" name="user_photo" id="user-photo"  class="input-medium focused" />
						</div>
				</div>   
				<div class="content_border"></div>
	        	<label for='old_password'>Old Password</label>
	            <input type='password' name='old_password' id='old_password' size='25' /> 
	            <label for='new_password'>New Password</label>
	            <input type='password' name='new_password' id='new_password' size='25' />                            
	        	<label for='new_password2'>Retype New Password</label>
	            <input type='password' name='new_password2' id='new_password2' size='25' />
	            <button data-theme="b" value="Submit" name="Update" type="submit" class="ui-btn-hidden">Submit</button>         
	        </form>
	    </div>
 <?php //include('includes/footer.php'); ?>