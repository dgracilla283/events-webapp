<?php $this->load->view('webapp/includes/header.php') ?>

<div id='update_user_information'>

	<form method='post' name='user_account_update'
		class="form-horizontal form-user" data-ajax="false"
		enctype="multipart/form-data" method="post" autocomplete="off"
		id="user_account_update">
		<input type="hidden" name="is_primary" value="0"> <input type="hidden"
			name="userID"
			value="<?php echo !empty($userInfo) ? $userInfo['userID'] : '';?>">
			<?php if (!empty($error)):?>
		<div class="alert alert-error">
		<?php
		echo $error;
		?>
		</div>
		<?php endif;?>
		<?php if (!empty($save_success)) :?>
		<div class="alert alert-success">
			<strong>Successfully saved!</strong>
		</div>

		<?php endif;?>
		<fieldset>
			<label for='username'>First Name</label> <input type='text'
				name='first_name' id='first_name' size='25'
				value="<?php echo !empty($userInfo) ? $userInfo['first_name'] : '';?>" />
		</fieldset>
		<fieldset>
			<label for='username'>Last Name</label> <input type='text'
				name='last_name' id='last_name' size='25'
				value="<?php echo !empty($userInfo) ? $userInfo['last_name'] : '';?>" />
		</fieldset>
		<fieldset>
			<label for='username'>Type</label> <select
				class="focused input-medium speaker-select required" type="text"
				name="type" id="type">
				<option value=""></option>
				<option value="adult"
				<?php echo (!empty($userInfo) && ($userInfo['type'] == 'adult')) ? 'selected': '';?>>Adult</option>
				<option value="child"
				<?php echo (!empty($userInfo) && ($userInfo['type'] == 'child')) ? 'selected': '';?>>Child</option>
			</select>
		</fieldset>

		<label class="control-label" for="focusedInput">Photo</label>
		<div class="photo_controls">
			<input type="hidden" name="userPhotoID"
				value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['userPhotoID'] : '';?>" />
			<input type="hidden" name="s_current_photo"
				value="<?php echo !empty($userInfo) ? $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'] : '';?>" />
			<div class="remove_photo">
			<?php
			if (!empty($userInfo)) :
			if (!empty($userInfo) && !empty($userInfo['uploaded_photo'][$userInfo['userID']]['s_fname'])): ?>
				<img
					src="/img/upload/user/<?php echo $userInfo['uploaded_photo'][$userInfo['userID']]['s_fname']?>"
					width="150" />
				<div>
					<input type="checkbox" name="s_remove_photo" /> Remove photo
				</div>
				<?php
				endif;
				endif;?>			
			</div>
			<input type="file" name="user_photo" id="user-photo"
				class="input-medium focused" /> 
		</div>
		<div class="content_border"></div>
		<button data-theme="b" value="add_companion" name="add_companion"
			type="submit" class="ui-btn-hidden" data-disabled="false">Add
			Companion</button>
	</form>
</div>
<br />
<br />
<div id="companion-list">
<?php if(!empty($companions)): ?>
	Companions:
	<ul data-role="listview" data-divider-theme="a">
	<?php foreach($companions as $companion) :?>
		<li data-role="listview" data-divider-theme="a"><a
			href="?uid=<?php echo $userId?>&cid=<?php echo $companion['userID'];?>" data-transition="slide">
			<?php if (!empty($companion['userPhotoID'])):?> <img
				src="/img/upload/user/<?php echo $companion['s_fname'];?>"
				width="48" height="48"
				title="<?php $companion['first_name'].' '.$companion['last_name'];?>" />
				<?php else: ?> <img src="/img/no_photo_icon.png" /> <?php endif;?>
				<p class="title nopad">
				<?php echo $companion['first_name']. ' ' . $companion['last_name']?>
				</p>
				<div class="forward-button"></div> </a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php else: ?>
	<p class="no-companion">There are no Companions.</p>
	<?php endif; ?>
</div>
