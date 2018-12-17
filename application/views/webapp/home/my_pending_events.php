 <!-- Pending Events Conatiner -->
 <!-- todo: change id name -->
    <div id="home-navigation"  class="pendingEvents">
    	<div class="events-header">Pending Event Requests (Awaiting approval from Admin):</div>    	
        <ul>  
        	<?php foreach($myPendingEvents as $myPendingEvent) : ?> 
            <li data-theme="c" style="cursor: auto;">
            	<a href="/webapp/event_details?id=<?php echo $myPendingEvent['eventID'];?>" data-transition="slide" data-ajax="false">  
                	<div class="icon-calendar">
	                   	<p class="month"><?php echo strtoupper(date('M', strtotime($myPendingEvent['start_date_time']))); ?></p>
	                	<p class="day"><?php echo date('d',strtotime($myPendingEvent['start_date_time'])); ?></p>
	               	</div>
	            	<div class="event-title"><?php echo $myPendingEvent['title']; ?></div>
	            	<div class="next-arrow-icon"></div>
               	</a>
            </li>                    
            <?php endforeach; ?>
        </ul>          
    </div>