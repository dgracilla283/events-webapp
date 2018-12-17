<?php if($response): ?>
<div class="cancel-join">  
	<a class="btn-cancel-join join_button" data-id="<?php echo $response['eventAttendeeID'];?>" href="javascript:;">
		<?php if($response['user_id'] == $primaryUserID): ?>	
       		Cancel My Join Request
       	<?php else: ?>
       		Cancel <?php echo $response['first_name'].' '.$response['last_name'];?> Join Request
       	<?php endif; ?>	
    </a> 
</div>
<?php endif; ?>