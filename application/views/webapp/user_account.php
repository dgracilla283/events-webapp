<?php include('includes/header.php');?>				
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <h3>User Information</h3> 
	    </div>               
     </div>  
	<div id='update_user_information'>
	
	        <form method='post' name='user_account_update' class="form-horizontal form-user" data-ajax="false"  enctype="multipart/form-data" method="post" autocomplete="off" id="user_account_update">
	            <input type="hidden" name="is_primary" value="<?php echo $userData['is_primary'];?>">
	            <input type="hidden" name="userID" value="<?php echo $userData['userID'];?>">
	            <?php if (!empty($error)):?>
				<div class="alert alert-error">
					<?php
						echo $error;
					?>
				</div>
				<?php endif;?>           
	            <?php if ($update_success) :?>
	            <div class="alert alert-success">					
						<strong>Successfully saved!</strong>
					</div>
					
				<?php endif;?>	
	            <fieldset>
	            	<label for='username'>Email</label>
	            	<input type='text' id='email' size='25'  readonly value="<?php echo $userData['email'];?>" disabled="disabled"/>
	            	<input type='hidden' name='email' value="<?php echo $userData['email'];?>" />
	            </fieldset>
	            <fieldset>
	            	<label for='username'>First Name</label>
	            	<input type='text' name='first_name' id='first_name' size='25'  value="<?php echo $userData['first_name'];?>"/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Last Name</label>
	            	<input type='text' name='last_name' id='last_name' size='25'  value="<?php echo $userData['last_name'];?>"/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Affiliation</label>
	            	<input type='text' name='affiliation' id='affiliation' size='25'  value="<?php echo $userData['affiliation'];?>"/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Industry</label>
	            	<input type='text' name='industry' id='industry' size='25'  value="<?php echo $userData['industry'];?>"/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Title</label>
	            	<input type='text' name='title' id='title' size='25'  value="<?php echo $userData['title'];?>"/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Bio</label>
	           		<textarea maxlength="500" class="input-xlarge" rows="5" id="user-bio" name="bio"><?php echo $userData['bio'];?></textarea>     
	           	</fieldset>
	           	
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
				<fieldset>
	        		<label for='old_password'>Old Password</label>
	            	<input type='password' name='old_password' id='old_password' size='25' /> 
	            </fieldset>
	            <fieldset>
	            	<label for='new_password'>New Password</label>
	            	<input type='password' name='new_password' id='new_password' size='25' />                            
				</fieldset>
				<fieldset>
	        		<label for='new_password2'>Retype New Password</label>
	            	<input type='password' name='new_password2' id='new_password2' size='25' />
	            </fieldset>
	            <button data-theme="b" value="Update" name="Update" type="submit" class="ui-btn-hidden" data-disabled="false">Update Account</button>         
	        </form>
	    </div>
<?php include('includes/footer.php'); ?>