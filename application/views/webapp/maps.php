<?php include('includes/header.php'); ?>
 	
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a href="/webapp/dashboard?id=<?php echo $eventId;?>" id="btn-back">BACK</a>
	        <h3>Maps</h3>                 
	    </div>
     </div>
     <!-- Display google maps -->
     <div class="events-header maps">EVENT MAP:</div>
     <div data-role="content" class="center">
     	  <div class="ui-bar-c ui-corner-all ui-shadow" style="padding:1em;">
     	   <div id="map"></div>
     	  </div>
     </div>
    <div id="inner-main-navigation">
	   	<?php if(!empty($maps)): ?>
	   	<div class="events-header">OTHER MAPS:</div>
    	<ul data-role="listview" data-divider-theme="a" id="map-list">                    
	        <?php foreach($maps as $map): ?>
	        <li data-theme="c">
	        	<a href="/webapp/map_details?eid=<?php echo $eventId ?>&mid=<?php echo $map['mapPhotoID'] ?>">
	        		<span class="title"><?php echo $map['title'] ?></span>
	                <div class="forward-button"></div>
	        	</a>
	        </li>
	        <?php endforeach; ?>
	     </ul>
      	 <?php endif; ?>
    </div>
<?php include('includes/footer.php'); ?>