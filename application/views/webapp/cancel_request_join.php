<?php if(!empty($response)): ?>
<a class="btn-join-activity join_button"  
	data-id="<?php echo $response['reference_id']?>" 
	data-reftype="<?php echo $response['reference_type']?>" 
	data-userid="<?php echo $response['user_id']?>" 
	href="javascript:;">
	<?php if($response['user_id'] == $primaryUserID): ?>
		Request Join to this Activity
	<?php else: ?>
		Request Join for <?php echo $response['first_name'].' '.$response['last_name']?>
	<?php endif;?>	 
</a>
<?php endif; ?>