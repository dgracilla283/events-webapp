<?php $this->load->view('webapp/includes/header.php'); ?>
<div data-role="content" class="login_page">
	<div class="login_top_background">
			<div class="login_mid_bar"></div>
			<form action="/webapp/login"  method="post" data-ajax="false" id="form-login" autocomplete="off">
				<?php if(isset($loginErrorMsg)): ?>
				<label class="error"><?php echo $loginErrorMsg;?></label>
				<?php endif; ?>
				<div class="login_input_text">
					<div class="username_icon"></div>
					<input name="username" id="textinput1" placeholder="" value="<?php echo htmlentities($username);?>" type="text" autocomplete="off">
				</div>
				<div class="login_input_text">
					<div class="password_icon"></div>
					<input name="password" id="textinput3" placeholder="" value="" type="password" autocomplete="off">
					<input type="hidden" name="backUrl" id="backUrl" value="<?php echo $backUrl; ?>" />
				</div>
				<div class="login_submit_btn">
					<input value="" type="submit" id="submit-validation">
				</div>
				<p class="sigup_resetpass">
					<a href="/webapp/registration">Not a member? <b>Sign Up</b></a>
				</p>
				<p class="sigup_resetpass">
					<a href="/webapp/forgot_password"><b>Lost Password?</b></a>
				</p>
			</form>
	</div>
</div>

	</div>
    </body>
</html>