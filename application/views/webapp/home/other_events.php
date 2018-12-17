 <!-- Other Events Container -->
  <!-- todo: change id name -->
    <div id="home-navigation"  class="otherEvents">   
    	<div class="events-header">Upcoming Events</div>
        <ul>  
        	<?php foreach($otherEvents as $otherEvent) : ?> 
            <li data-theme="c" style="cursor: auto;">
            	<a href="/webapp/event_details?id=<?php echo $otherEvent['eventID'] ?>" data-transition="slide" data-ajax="false">  
                	<div class="icon-calendar">
	                   	<p class="month"><?php echo strtoupper(date('M', strtotime($otherEvent['start_date_time']))); ?></p>
	                	<p class="day"><?php echo date('d',strtotime($otherEvent['start_date_time'])); ?></p>
	               	</div>
	            	<div class="event-title"><?php echo $otherEvent['title']; ?></div>
	            	<div class="next-arrow-icon"></div>
	            <!---  <a href="/webapp/join_event?id=33"  style="text-align: right; margin-right:20px;">Join</a> -->
               	</a>
            </li>                    
            <?php endforeach; ?>
        </ul>            
    </div>