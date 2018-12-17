<form class="form-horizontal" method="post" action="/admin/add_event_attendees" id="form-add-event-attendees">
<fieldset>
	<div class="control-group">
		Search: <input class="input-xlarge focused search-user" id="search-user" type="text" value="" />
	</div>
	<ul id="attendees" class="attendees">
		<?php foreach($users as $user): ?>
		<?php if($user['is_primary']):?>
		<li class="user-block clearfix">
			<a href="javascript:;" class="add-user-btn<?php echo (array_key_exists($user['userID'],$attendees)) ? ' selected' : '';?><?php if(array_key_exists($user['userID'],$userCompanions)): echo ' has-companions'; endif; ?>" data-userid="<?php echo $user['userID'];?>" title="<?php echo $user['last_name'].', '.$user['first_name']; ?>">
			<?php if(!empty($user['uploaded_photo'])): ?>
				<img src="/img/upload/user/<?php echo $user['uploaded_photo']['s_fname'];?>" height="48" width="48" title="<?php $user['first_name'].' '.$user['last_name'];?>"/>
			<?php else: ?>
				<img src="/img/no_photo_icon.png" />
			<?php endif;?>
			<span class="user_name"><?php echo $user['last_name'].', '.$user['first_name']; ?></span>
			</a>
			<?php if(array_key_exists($user['userID'],$attendees)): ?>
			<input type="hidden" name="user_id[]" value="<?php echo $user['userID'];?>" />
			<?php endif;?>
		</li>
		<?php endif; endforeach; ?>
	</ul>

	<input type="hidden" name="event_id" value="<?php echo $eventId; ?>" />
</fieldset>
</form>