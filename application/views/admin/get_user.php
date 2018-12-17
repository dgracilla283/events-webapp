<form class="form-horizontal form-user" method="post">
<input type="hidden" name="is_primary" value="1"/>
	<fieldset>
		<div class="control-group">
			<label class="control-label">Username</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="date_start"
					value="<?php echo $user['email'];?>" readonly />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="disabledInput">First Name</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="first_name"
					value="<?php echo $user['first_name'];?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="disabledInput">Last Name</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="first_name"
					value="<?php echo $user['last_name'];?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="disabledInput">Password</label>
			<div class="controls">
				<input class="input-xlarge" type="password" name="password"
					value="" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="disabledInput">Retype Password</label>
			<div class="controls">
				<input class="input-xlarge" type="password" name="password2"
					value="" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="disabledInput">Affiliation</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="affiliation"
					value="<?php echo $user['affiliation'];?>" />
			</div>
		</div>
	</fieldset>
</form>
