<?php include('includes/header.php'); ?>

<div data-role="content" class="login_page">
	<div class="login_top_background">
			<div class="login_mid_bar"></div>
			<div class="spacer"></div>
			<?php if (!empty($errorMsg)):?>
			<div class="error-container"><?php echo $errorMsg;?></div>
			<?php endif; ?>
			<?php if (!empty($successMsg)):?>
				<div class="error-container"><?php echo $successMsg;?></div>
			<?php endif; ?>
			<form method="post" data-ajax="false">
				<div class="login_input_text">
					<input type="text" name="email" id="email" value="" class="input-xlarge focused" />
				</div>
				<div class="email_submit_btn">
					<input value="" type="submit">
				</div>
			</form>
			<a href="rcgeventplanner://" id="login-back-button"></a>
	</div>
</div>
	</div>                
    </body>
</html>