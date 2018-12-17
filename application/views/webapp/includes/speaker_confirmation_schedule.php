<div id="speaker-schedule-list">     	
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
					   	<?php					   	
		              	if (!isset($it['itineraryID'])){
							$id = $it['breakoutID'];                        	
						}else{
							$id = $it['itineraryID'];
						}						
                        ?>
                        <p class="invite-btn">
                           <a class="btn-accept join_button" href="/webapp/accept_speaker_invite?id=<?php echo $id;?>&eid=<?php echo $event['eventID'];?>&iid=<?php echo $itineraryId;?>&rtype=<?php echo $referenceType;?>&uid=<?php echo $user['userID'];?>">Accept</a><a class="btn-decline join_button marLt5px" href="/webapp/cancel_speaker_invite?id=<?php echo $id;?>&eid=<?php echo $event['eventID'];?>&iid=<?php echo $itineraryId;?>&rtype=<?php echo $referenceType;?>&uid=<?php echo $user['userID'];?>">Decline</a>
                        </p>                        
                        <a href="/webapp/view_itinerary?id=<?php echo $id;?>&eid=<?php echo $event['eventID'];?>" data-transition="slide">                           
                           <p class="title"><?php echo $it['title']; ?></p>                           
                           <p class="subtitle"><?php echo date('h:i A', strtotime($it['start_date_time'])). ' - ' .date('h:i A', strtotime($it['end_date_time'])); ?></p>
                           <?php if($it['location']) : ?>               
                           <p class="subtitle">Location: <?php echo $it['location']; ?></p> 
                           <?php endif; ?>                           
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div> 
    <?php endforeach; ?>
    <?php else: ?>
    <div class="warning">You have no pending invitation.</div>    
    <?php endif; ?> 
</div>