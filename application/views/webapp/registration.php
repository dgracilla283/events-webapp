<?php include('includes/header.php');?>				
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <h3>User Registration</h3> 
	    </div>               
     </div>  
	<div id='update_user_information'>
	        <form method='post' name='"user_registration"' class="form-horizontal form-user" data-ajax="false"  enctype="multipart/form-data" method="post" autocomplete="off" id="user_registration">
	            <input type="hidden" name="is_primary" value="1">
	            <input type="hidden" name="userID" value="">
	            <?php if (!empty($errorMsg)):?>
				<div class="alert alert-error">
					<?php
						echo $errorMsg;
					?>
				</div>
				<?php endif;?>           
	            <?php if ($successMsg) :?>
	            <div class="alert alert-success">					
						<strong><?php echo $successMsg;?></strong>
					</div>
					
				<?php endif;?>	
	            <fieldset>
	            	<label for='username'>Email</label>
	            	<input type='text' name='email' id='email' size='25'/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>First Name</label>
	            	<input type='text' name='first_name' id='first_name' size='25'/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Last Name</label>
	            	<input type='text' name='last_name' id='last_name' size='25' />
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Affiliation</label>
	            	<input type='text' name='affiliation' id='affiliation' size='25' />
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Industry</label>
	            	<input type='text' name='industry' id='industry' size='25' />
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Title</label>
	            	<input type='text' name='title' id='title' size='25'/>
	            </fieldset>
	            <fieldset>
	            	<label for='username'>Bio</label>
	           		<textarea maxlength="500" class="input-xlarge" rows="5" id="user-bio" name="bio"></textarea>     
	           	</fieldset>
	           	<!--  
	           	<input type="hidden" name="userPhotoID" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['userPhotoID'] : '';?>" />
				<input type="hidden" name="s_current_photo" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'] : '';?>" />
				-->
				<label class="control-label" for="focusedInput">Photo</label>
				<div class="photo_controls">
						<div class="remove_photo">
						<br />
							<!-- <input type="checkbox" name="s_remove_photo" />  -->
							<input type="file" name="user_photo" id="user-photo"  class="input-medium focused" />
						</div>
				</div>   
				<div class="content_border"></div>
				<fieldset>
	        		<label for='old_password'>Pick a Password</label>
	            	<input type='password' name='password' id='password' size='25' autocomplete='off' /> 
	            </fieldset>
	            <fieldset>
	            	<label for='new_password'>Retype Password</label>
	            	<input type='password' name='password_confirm' id='password_confirm' size='25' autocomplete='off' />                            
				</fieldset>
	            <button data-theme="b" name="Register" type="submit" class="ui-btn-hidden" data-disabled="false">Register Account</button>         
	        </form>
	    </div>
<?php include('includes/footer.php'); ?>