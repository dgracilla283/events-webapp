<form class="form-horizontal" method="post" action="/admin/add_agenda_attendees" id="form-add-edit-guest">
<fieldset>
	<div class="control-group">		
		<div class="pull-left">Search: <input class="input-large focused" id="search-user" type="text" value="" /></div>
		<div class="pull-left" style="margin-left:10px">
			<input type="radio" id="btn-select-all" name="select_type" style="vertical-align: top"/> Select All
			<input type="radio" id="btn-deselect-all" name="select_type" style="vertical-align: top"/> Deselect All
		</div>
	</div>
	<ul id="attendees"> 
		<?php foreach($eventAttendees as $user): ?>
		<li class="user-block clearfix"> 
			<a href="javascript:;" class="add-user-btn<?php echo (array_key_exists($user['userID'],$agendaAttendees)) ? ' selected' : '';?>" data-userid="<?php echo $user['userID'];?>" title="<?php echo $user['last_name'].', '.$user['first_name']; ?>"> 
			<?php if(!empty($user['uploaded_photo'])): ?>
				<img src="/img/upload/user/<?php echo $user['uploaded_photo']['s_fname'];?>" height="48" width="48" title="<?php $user['first_name'].' '.$user['last_name'];?>"/> 
			<?php else: ?>
				<img src="/img/no_photo_icon.png" />
			<?php endif;?>
			<span class="user_name"><?php echo $user['last_name'].', '.$user['first_name']; ?></span>			
			</a> 
			<?php if(array_key_exists($user['userID'],$agendaAttendees)): ?>
			<input type="hidden" name="user_id[]" value="<?php echo $user['userID'];?>" /> 		
			<?php endif;?>
		</li>
		<?php endforeach; ?>
	</ul>
	
	<input type="hidden" name="event_id" value="<?php echo $eventId; ?>" /> 
	<input type="hidden" name="agenda_id" value="<?php echo $agendaId; ?>" />
	<input type="hidden" name="referenceType" value="<?php echo $getVar['rtype']; ?>" />
</fieldset>
</form>