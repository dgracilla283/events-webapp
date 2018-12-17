<?php include('includes/header.php'); ?>
<div id="content" class="span10">
	<div class="row-fluid sortable">
		<div class="box ">
			<div data-original-title="" class="box-header well">
				<h2>
					<i class="icon-user"></i> <?php echo !empty($userInfo)  ? 'Edit Attendees' : 'Add Attendees'; ?>
				</h2>
				<div class="box-icon">
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>
			</div>
			<?php if (!empty($error)):?>
			<div class="alert alert-error">
				<?php
					echo $error;
				?>
			</div>
			<?php endif;?>
			<div class="box-content">
				<?php if($edited): ?>
				<div class="alert alert-success">
					<strong>Successfully saved!</strong>
				</div>
				<?php endif; ?>
				<form class="form-horizontal form-user" enctype="multipart/form-data" method="post" autocomplete="off" id="form-user">
				<input type="hidden" name="is_primary" value="1"/>
					<fieldset>
						<table>
							<tr>
								<td valign="top">
									<div class="control-group">
										<label class="control-label" for="focusedInput">Username</label>
										<div class="controls">
											<?php if(empty($userInfo)): ?>
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['email'] : '';?>" name="email" maxlength="30">
											<?php else: ?>
												<p><?php echo $userInfo['email']; ?>
												<input class="input-xlarge focused" type="hidden" value="<?php echo $userInfo['email']; ?>" name="email">
											<?php endif; ?>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">First Name</label>
										<div class="controls">
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['first_name'] : '';?>" name="first_name" maxlength="30">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">Last Name</label>
										<div class="controls">
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['last_name'] : '';?>" name="last_name" maxlength="30">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Password</label>
										<div class="controls">
											<input class="input-xlarge focused" type="password" value="<?php echo !empty($userInfo) ? 'password' : '';?>" name="password" id="password1" maxlength="20">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Retype Password</label>
										<div class="controls">
											<input class="input-xlarge focused" type="password" value="<?php echo !empty($userInfo) ? 'password' : '';?>" name="password2" id="password2" maxlength="20">
										</div>
									</div>
									<?php if ($this->config->item('is_allow_user_photo') === true):?>
									<div class="control-group">
										<input type="hidden" name="userPhotoID" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['userPhotoID'] : '';?>" />
										<input type="hidden" name="s_current_photo" value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'] : '';?>" />
										<label class="control-label" for="focusedInput">Photo</label>
										<div class="controls">
											<?php
											if (!empty($userInfo)) :
											if (!empty($userInfo) && !empty($userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'])): ?>
												<img src="/img/upload/user/<?php echo $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname']?>" width="150" />
												<div>
												<input type="checkbox" name="s_remove_photo" /> Remove photo
												</div>
											<?php
											endif;
											endif;?>
											<input type="file" name="user_photo" id="user-photo"  class="input-medium focused" />
										</div>
									</div>
									<?php endif;?>
								</td>
								<td valign="top">
									<div class="control-group">
										<label class="control-label" for="focusedInput">Position / Title</label>
										<div class="controls">
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['title'] : '';?>" name="title" maxlength="100">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">Company</label>
										<div class="controls">
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['affiliation'] : '';?>" name="affiliation" maxlength="100">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">Industry</label>
										<div class="controls">
											<input class="input-xlarge focused" type="text" value="<?php echo !empty($userInfo) ? $userInfo['industry'] : '';?>" name="industry" maxlength="100">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="focusedInput">Bio</label>
										<div class="controls">
											<textarea name="bio" id="user-bio" rows="5" class="input-xlarge" maxlength="500"><?php if(!empty($userInfo)) {echo htmlentities($userInfo['bio']);}?></textarea>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<div class="form-actions">
							<input type="hidden" value="<?php echo !empty($userInfo) ? $userInfo['userID'] : '';?>" name="userID" id="user-id"/>
							<button type="submit" class="btn btn-primary">Save changes</button>
							<button class="btn" id="cancel-add">Cancel</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<div class="box">
			<div data-original-title="" class="box-header well">
				<h2>
					<i class="icon-th-list"></i> Users
				</h2>
			</div>
			<div class="box-content" id="add_user">
				<div class="control-group pull-right">
					<input type="text" name="txtSearchUser" id="txtSearchUser" value="" class="input-xlarge">
					<button class="btn valign-top btn-primary" type="button" id="btn-search-users">Search</button>
				</div>
				<div class="clear"></div>
				<div id="user_list">
					<br /><center><span class="loader">&nbsp;</span></center><br />
				</div>
			</div>
		</div>
	</div>
	<!--/row-->
</div>
<?php // delete user pop-up ?>
<div class="modal hide fade" id="guest-delete" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Guest</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br />
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-confirm-delete-guest">Yes</a>
	</div>
</div>
<?php include('includes/footer.php'); ?>