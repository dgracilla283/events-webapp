<div id="home-navigation">    
     	<div class="events-header">My Events</div>
     	<?php if(!empty($myEvents)): ?> 
     	
        <ul>  
        	<?php foreach($myEvents as $myEvent) : ?> 
            <li data-theme="c">
                <a href="/webapp/dashboard?id=<?php echo $myEvent['eventID'];?>" data-transition="slide">                   	
                   <div class="icon-calendar">
	                   	<p class="month"><?php echo strtoupper(date('M', strtotime($myEvent['start_date_time']))); ?></p>
	                   	<p class="day"><?php echo date('d',strtotime($myEvent['start_date_time'])); ?></p>
	               </div>
	               <div class="event-title"><?php echo $myEvent['title']; ?></div>
	               <div class="next-arrow-icon"></div>
                </a>
            </li>                    
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <div class="warning">You don't have any events</div>
        <?php endif; ?>               
    </div>