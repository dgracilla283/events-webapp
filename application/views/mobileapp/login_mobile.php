<?php include('includes/header.php'); ?>
<div data-role="content" class="login_page">
	<div class="login_top_background">
			<div class="login_mid_bar"></div>
			<form action="/mobileapp/login"  method="post" data-ajax="false">
				<div class="login_input_text">
					<div class="username_icon"></div>
					<input name="username" id="textinput1" placeholder="" value="" type="text">
				</div>
				<div class="login_input_text">
					<div class="password_icon"></div>
					<input name="password" id="textinput3" placeholder="" value="" type="password">
						
				</div>
				<div class="login_submit_btn">
					<input value="" type="submit">
				</div>
				<p class="sigup_resetpass">
					<a href="/webapp/forgot_password">Not a member? <b>Sign Up</b></a>
					<br />
					<a href="/webapp/forgot_password"><b>Lost Password?</b></a>
				</p>
			</form>
	</div>
</div>
	</div>                
    </body>
</html>