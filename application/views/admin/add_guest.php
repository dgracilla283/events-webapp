<?php include('includes/header.php'); ?>
<div id="content" class="span10">
	<div class="row-fluid sortable">
		<div class="box ">
			<div data-original-title="" class="box-header well">
				<h2>
					<i class="icon-user"></i> <?php echo !empty($userInfo)  ? 'Edit Companion' : 'Add Companion'; ?>
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
				<form class="form-horizontal form-user" enctype="multipart/form-data" method="post" autocomplete="off" id="form-guest-user">
					<fieldset>
						<table>
							<tr>
								<td valign="top">
									<div class="control-group">
										<label class="control-label" for="focusedInput">Companion Of</label>
										<div class="controls">
											<select class="focused input-medium speaker-select required" type="text" name="primary_user_id" id="primary_user_id">
								  			<option value=""></option>
									  		<?php foreach($users as $user): ?>

								  				<option value="<?php echo $user['userID']?>" <?php echo (!empty($userInfo) && ($userInfo['primary_key_id'] == $user['userID'])) ? 'selected': '';?>><?php echo $user['last_name'].', '.$user['first_name']?></option>
								  			<?php endforeach; ?>
								  		  </select>
								  		  <label for="primary_user_id" class="error" style="display: none;">Companion is required.</label>
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
										<label class="control-label" for="focusedInput">Type</label>
										<div class="controls">
											<select class="focused input-medium speaker-select required" type="text" name="type" id="type">
								  			<option value=""></option>
									  		<option value="adult" <?php echo (!empty($userInfo) && ($userInfo['type'] == 'adult')) ? 'selected': '';?>>Adult</option>
								  		 	<option value="child" <?php echo (!empty($userInfo) && ($userInfo['type'] == 'child')) ? 'selected': '';?>>Child</option>
								  		 	</select>
								  		  <label for="type" class="error" style="display: none;">Type is required.</label>
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
								</td>
							</tr>
						</table>
						<div class="form-actions">
							<input type="hidden" value="0" name="is_primary" />
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
					<i class="icon-th-list"></i> Companions
				</h2>
			</div>
			<div class="box-content" id="add_guest">
				<div class="control-group pull-right">
					<input type="text" name="txtSearchUser" id="txtSearchUser" value="" class="input-xlarge">
					<button class="btn valign-top btn-primary" type="button" id="btn-search-users">Search</button>
				</div>
				<div class="clear"></div>
				<div id="user_guest_list">
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