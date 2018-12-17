<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1" />  -->
        <title>RCG Events Planner</title>
        <link rel="stylesheet" href="/css/jquery.mobile-1.2.0.min.css?v=0.0.005" />             
        <link rel="stylesheet" href="/css/webapp.css?v=0.0.006" />
        <?php if('page3' == $page_id): ?>
        <link rel="stylesheet" href="/js/ckeditor/contents.css?v=0.0.005" />
        <?php endif; ?>     
        <link rel="shortcut icon" href="/img/favicon.ico" />
		<script src="/js/jquery.js?v=0.0.005" type="text/javascript"></script>
		<script src="/js/webapp.js?v=0.0.005"></script>         
        <script src="/js/jquery.mobile-1.2.0.min.js?v=0.0.005"></script>      
        <script src="/js/validate.js"></script>      
        <?php if('page5' == $page_id &&  $event['location']): ?>
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
        </script>              
    </head>
    <body> 
        <!-- Home -->
        <div data-role="page" id="<?php echo $page_id;?>">