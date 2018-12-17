<div class="clearfix" style="margin-bottom: 10px">
	<div class="alert alert-warning"> 
		<span>This activity has preferences. You have to manage each attendees and their preferences!</span>
	</div>
<?php if(!empty($activityAttendees)): ?>
	<ul id="attendees"> 
	<?php foreach($activityAttendees as $user): ?>
	<li class="user-block clearfix"> 
		<a href="javascript:;" class="add-user-btn selected" data-userid="<?php echo $user['userID'];?>"> 
		<?php if(!empty($user['uploaded_photo'])): ?>
			<img src="/img/upload/user/<?php echo $user['uploaded_photo']['s_fname'];?>" height="48" width="48" title="<?php $user['first_name'].' '.$user['last_name'];?>"/> 
		<?php else: ?>
			<img src="/img/no_photo_icon.png" />
		<?php endif;?>
		<span class="user_name"><?php echo $user['last_name'].', '.$user['first_name']; ?></span>			
		</a>
	</li>
	<?php endforeach; ?>
	</ul>
</div>
<?php else: ?>
<div class="alert alert-error">					
	<strong>No attendees!</strong>
</div>
<?php endif; ?>