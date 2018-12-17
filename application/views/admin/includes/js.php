<script>
	var CMS = {};
	    CMS.editListener = false;
	<?php if(isset($eventId)): ?>
		CMS.event_id = <?php echo $eventId;?>;
	<?php endif; ?>
	<?php if($method == 'view_event') : ?>
		CMS.eventStartDateTime = '<?php echo date('F d, Y H:i:s', strtotime(str_replace('-','/',$event['start_date_time'])));?>';
		CMS.eventEndDateTime = '<?php echo date('F d, Y H:i:s', strtotime(str_replace('-','/',$event['end_date_time'])));?>';
	<?php endif; ?>
	<?php if($method == 'manage_activity_attendees' || $method == 'manage_activity_preferences') : ?>
		CMS.event_id = <?php echo $getVar['eid'];?>;
		CMS.activity_id = <?php echo $getVar['id']; ?>;
		CMS.activity_type = '<?php echo $getVar['rtype']; ?>';
	<?php endif;?>
	<?php if($method == 'manage_presentations'): ?>
		<?php if (!empty($getVar['eid'])) { ?>
			CMS.event_id = <?php echo $getVar['eid'];?>;
		<?php } ?>
		<?php if (!empty($getVar['id'])) { ?>
			CMS.presentation_category_id = <?php echo $getVar['id']; ?>;
		<?php } ?>
		<?php if (!empty($getVar['pid'])) { ?>
			CMS.presentation_id = <?php echo $getVar['pid']; ?>;
		<?php } ?>
<?php endif; ?>
</script>
<script src="/js/jquery.js" type="text/javascript"></script>
<script src="/js/dropdown.js"  type="text/javascript"></script>
<script src="/js/validate.js" type="text/javascript"></script>
<?php if($method == 'dashboard') :?>
	<script src="/js/modal.js"  type="text/javascript"></script>
	<script src="/js/tab.js"  type="text/javascript"></script>
<?php endif; ?>
<?php if($method == 'view_event' || $method == 'add_user' || $method == 'add_guest' || $method == 'manage_activity_attendees' || $method == 'manage_activity_preferences' || $method == 'manage_presentations') :?>
	<script src="/js/jquery-plugins.js"  type="text/javascript"></script>
	<script src="/js/tab.js"  type="text/javascript"></script>
	<script src="/js/modal.js"  type="text/javascript"></script>
	<script src="/js/jquery-ui.js" type="text/javascript"></script>
	<script src="/js/timepicker.js" type="text/javascript"></script>
<?php endif; ?>
<?php if($method == 'edit_event' || $method == 'add_event' || $method == 'dashboard' || $method == 'duplicate_event_form') :?>
	<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
	<script src="/js/jquery-ui.js" type="text/javascript"></script>
	<script src="/js/timepicker.js" type="text/javascript"></script>
<?php endif; ?>
<?php if($method == 'manage_requests') :?>
	<script src="/js/tab.js"  type="text/javascript"></script>
<?php endif; ?>

<script src="/js/main.js?v=<?php echo $this->config->item('version')?>" type="text/javascript"></script>