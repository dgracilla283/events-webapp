<?php include('includes/header.php');?>		
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <h3>Reset Password</h3> 
	    </div>               
     </div>  		
<div id="register">
        <form method='post' name='reset_password' class="form-horizontal form-reset-password" data-ajax="false"  method="post" autocomplete="off" id="form_reset_password">
            <input type="hidden" name="key" value="<?php echo $activationKeyInfo['key'];?>">
            <input type="hidden" name="user_id" value="<?php echo $activationKeyInfo['user_id'];?>">
            <?php if (!empty($errorMsg)):?>
			<div class="alert alert-error">
				<?php
					echo $errorMsg;
				?>
			</div>
			<?php endif;?>          
            <label for='username'>Username</label>
            <input type='text' name='email' id='email' size='25'  readonly value="<?php echo $userInfo['email'];?>"/>
            <label for='username'>Choose a new password</label>
            <input type='password' name='new_password' id='new_password' size='25'/>
            <label for='username'>Confirm your new password</label>
            <input type='password' name='new_password2' id='new_password2' size='25' />
         
            <button data-theme="b" value="Reset My Password" name="reset_password_submit" type="submit" class="ui-btn-hidden" data-disabled="false">Reset My Password</button>         
        </form>
    </div>
<?php include('includes/footer.php'); ?>