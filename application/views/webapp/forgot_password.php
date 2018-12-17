<?php include('includes/header.php'); ?>

<div data-role="content" class="login_page">
	<div class="login_top_background">
			<div class="login_mid_bar"></div>
			
			<div class="error-container">
			<?php if(!empty($errorMsg)) {
				echo $errorMsg;
			}?>
			<?php if (!empty($successMsg)) {
				echo $successMsg;
			}?>
			</div>
			<form method="post" data-ajax="false" id="forgot_password">
				<div class="login_input_text">
					<input type="text" name="email" id="email" value="" class="input-xlarge focused" />
				</div>
				<div class="email_submit_btn">
					<input value="" type="submit">
				</div>
			</form>
			<a href="/webapp/login" id="login-back-button"></a>
			
	</div>
</div>
	</div>                
    </body>
</html>