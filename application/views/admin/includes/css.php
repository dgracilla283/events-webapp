<link type="text/css" rel="stylesheet" href="/css/admin.css?v=<?php $this->config->item('version'); ?>" />
<?php if($method == 'edit_event' || $method == 'add_event' || $method == 'view_event' || $method == 'manage_activity_attendees' || $method == 'dashboard' || $method == 'duplicate_event_form') { ?>
	<link type="text/css" rel="stylesheet" href="/css/jquery-ui.css?v=<?php $this->config->item('version'); ?>" />
<?php } ?>
<?php if($method == 'manage_presentations') { ?>
	<link type="text/css" rel="stylesheet" href="/css/jquery-ui-1.9.2.custom.css?v=<?php $this->config->item('version'); ?>" />
<?php } ?>
<link type="text/css" rel="stylesheet" href="/css/sprite.css?v=<?php $this->config->item('version'); ?>" />