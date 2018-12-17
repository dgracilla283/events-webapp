<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>        
        <title>RCG Events Planner</title>
        <link rel="shortcut icon" href="/img/favicon.ico" />      	
        <link rel="stylesheet" href="/css/jquery.mobile-1.2.0.min.css?v=<?php echo $this->config->item('version')?>" />
        <link rel="stylesheet" href="/css/webapp.css?v=<?php echo $this->config->item('version')?>" />
        	 
   		<?php if('page3' == $page_id): ?>        	
        <link rel="stylesheet" href="/js/ckeditor/contents.css?v=<?php echo $this->config->item('version')?>" />
        <?php endif; ?>    
       
		<script src="/js/jquery.js?v=<?php echo $this->config->item('version')?>" type="text/javascript"></script>
		<script src="/js/webapp.js?v=<?php echo $this->config->item('version')?>"></script>
		<script src="/js/jquery.mobile-1.2.0.min.js?v=<?php echo $this->config->item('version')?>"></script>
		<script src="/js/validate.js?v=<?php echo $this->config->item('version')?>"></script>
		
		
		<?php if($page_id == 'page1'): ?>
			<script src="/js/slidemenu.js?v=<?php echo $this->config->item('version')?>"></script>
		<?php endif; ?>		
		<?php if(isset($isGoogleMap) && $isGoogleMap &&  $event['location']): ?>
	    <script src="http://maps.google.com/maps/api/js?key=AIzaSyC-OuO4-9vB-lH2yaWCqaKg5cDF5wBBA-0&sensor=false"></script>        
	    <?php endif;?>
	 
        <script>
        	var APP = {};     
        	<?php   if(!empty($event['location'])):  ?>
        		APP.eventLocation =  '<?php echo $event['location'];?>';
        		APP.eventTitle    = "<?php echo htmlentities($event['title']);?>";
        		APP.eventCoordsLat = '<?php echo $event['latitude'] ?>';
        		APP.eventCoordsLng = '<?php echo $event['longitude'] ?>';
        	<?php endif; ?>
			<?php if(!empty($userID)): ?>
				APP.userID = <?php echo $userID; ?>;
        	<?php endif; ?>
        	<?php if(!empty($eventId)): ?>
				APP.eventID = <?php echo $eventId; ?>;  
        	<?php endif;?> 	 
        </script>              
    </head>
    <body>   
    	<?php if($page_id == 'page1'): ?>
    	<!-- Slide Menu -->    	
    	<div id="slidemenu">
			<div id="profile">

				<?php if(!empty($userInfo['s_fname'])): ?>
					<img src="/img/upload/user/<?php echo $userInfo['s_fname']?>" title="" height="48" width="48" />
				<?php else: ?>
					<img src="/img/no_photo_icon.png?>" title="" height="48" width="48" />		
				<?php endif; ?>				
				<div class="profile_info"><strong><?php echo $userInfo['last_name'].' '.$userInfo['first_name']?></strong><small><?php echo $userInfo['title']?></small></div>

			</div>
			<h3>MENU</h3>
			<ul>

				<?php if(!empty($events['myEvents'])): ?>
				<li><a href="/webapp/my_activities"><span class="my_activity_icon"></span>My Activities</a></li>	        				
	        	<?php endif; ?>		
	        	<li><a href="/webapp/manage_companion"><span class="add_companion_icon"></span>Add Companion</a></li>
				<li><a href="/webapp/user_account"><span class="my_account_icon"></span>My Account</a></li>
				<li><a href="/webapp/logout"><span class="logout_icon"></span>Logout</a></li>
			</ul>
		</div>
    	<?php endif; ?>
        <!-- Home -->
        <div data-role="page" id="<?php echo $page_id;?>">