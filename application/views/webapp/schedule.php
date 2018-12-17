<?php include('includes/header.php'); ?>
    <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                
	    	<a href="/" id="btn-home">HOME</a>
	        <a id="btn-back" href="/webapp/dashboard?id=<?php echo $event['eventID'];?>">
	            BACK
	        </a>
	        <h3>Agenda</h3>                
	     </div>
     </div>       
     <div id="schedule-list">     	
     	<?php if(!empty($itineraries)) : ?>
     	<?php   
  		foreach ($itineraries as $key => $itinerary):?>
  		<div data-role="collapsible" data-collapsed="false" id="schedule-list-header">
        	<h3 class="title">
        		<div class="schedule-header-date"><div class="left-schedule-head"></div><?php echo date('l, F d, Y', strtotime($key))?></div>
        	</h3>
        	<div class="itinerary">
	        	<ul data-inset="false" data-role="listview" data-divider-theme="b">
	        		<?php foreach($itinerary as $it):?>
	        			<li data-theme="c">
				            <a href="/webapp/view_itinerary?iid=<?php echo $it['itineraryID'];?>&eid=<?php echo $event['eventID'];?>" data-transition="slide">
				               <p class="title"><?php echo $it['title']; ?></p>
				               <p class="subtitle"><?php echo date('h:i A', strtotime($it['start_date_time'])). ' - ' .date('h:i A', strtotime($it['end_date_time'])); ?></p>
				               <?php if($it['location']) : ?> 
				               <p class="subtitle">Location: <?php echo $it['location']; ?></p> 
				               <?php endif; ?>
				               <div class="forward-button"></div> 
				            </a>
					    </li>	
	        		<?php endforeach;?>
	        	</ul> 	 
        	</div>
  		</div> 
		<?php endforeach; ?>
		<?php else: ?> 
		<div class="box-white"> 
			No schedules yet.
		</div>
		<?php endif; ?> 
    </div>  
 <?php include('includes/footer.php'); ?>