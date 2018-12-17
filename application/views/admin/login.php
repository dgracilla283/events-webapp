<!DOCTYPE html>
<html lang="en">
<head>	
	<meta charset="utf-8">
	<title>RCG Event Planner</title>
	<link id="bs-css" href="/css/admin.css" rel="stylesheet" />
	<link rel="shortcut icon" href="/img/favicon.ico" />		
</head>
<body>
		<div class="container-fluid">
		<div class="row-fluid">		
			<div class="row-fluid">
				<div class="span12 center login-header">
					<h2>Welcome to RCG Event Planner CMS</h2>
				</div>
			</div>			
			<div class="row-fluid">
				<div class="well span5 center login-box">
					<div class="alert alert-info">
						<?php if (!empty($loginErrorMsg)):
							echo $loginErrorMsg;
						?>
						<?php else:?>
						Please login with your Username and Password.
						<?php endif;?>
					</div>
					<form class="form-horizontal" action="/login/index" method="post" autocomplete="off">
					<input type="hidden" value="<?php echo isset($back_url) ? $back_url : ''; ?>" name="back_url">
						<fieldset>
							<div class="input-prepend" title="Username" data-rel="tooltip">
								<span class="add-on"><i class="icon-user"></i></span><input autofocus class="input-large span10" name="username" id="username" type="text" value="<?php echo isset($username) ? $username : ''; ?>" autocomplete="off" />
							</div>
							<div class="clearfix"></div>

							<div class="input-prepend" title="Password" data-rel="tooltip">
								<span class="add-on"><i class="icon-lock"></i></span><input class="input-large span10" name="password" id="password" type="password" value="" autocomplete="off" />
							</div>
							<div class="clearfix"></div>							
							<p class="center span5">
							<button type="submit" class="btn btn-primary">Login</button>
							</p>
						</fieldset>
					</form>
				</div>
			</div>
				</div>
		
	</div>		
</body>
</html>
