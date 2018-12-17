<form class="form-horizontal" method="post" action="/admin/add_event_companion" id="form-add-companion">
<fieldset>	
	<ul id="attendees"> 
		<?php foreach ($companions as $attendeeCompanions) :?>
		<?php foreach($attendeeCompanions as $val): ?>
		<li class="user-block clearfix"> 
			<p>Companion of <?php echo $users[$val['primary_user_id']]['first_name'].' '.$users[$val['primary_user_id']]['last_name']; ?></p> 
			<a href="javascript:;" class="add-user-btn<?php echo (array_key_exists($val['user_id'],$attendees)) ? ' selected' : '';?>" data-userid="<?php echo $val['user_id'];?>" title="<?php echo $users[$val['user_id']]['last_name'].', '.$users[$val['user_id']]['first_name']; ?>"> 
			<?php if(!empty($users[$val['user_id']]['uploaded_photo'])): ?>
				<img src="/img/upload/user/<?php echo $users[$val['user_id']]['uploaded_photo']['s_fname'];?>" height="48" width="48" title="<?php $users[$val['user_id']]['first_name'].' '.$users[$val['user_id']]['last_name'];?>"/> 
			<?php else: ?>
				<img src="/img/no_photo_icon.png" />
			<?php endif;?>
			<span class="user_name"><?php echo $users[$val['user_id']]['last_name'].', '.$users[$val['user_id']]['first_name']; ?></span>
			</a> 
			<?php if(array_key_exists($val['user_id'],$attendees)): ?>
			<input type="hidden" name="user_id[]" value="<?php echo $val['user_id'];?>" /> 		
			<?php endif;?>			
		</li>
		<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
	
	<input type="hidden" name="event_id" value="<?php echo $eventId; ?>" /> 
</fieldset>
</form>