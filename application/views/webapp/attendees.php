<?php  include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a href="/webapp/dashboard?id=<?php  echo $eventId; ?>" id="btn-back">BACK</a> 
	        <h3>Event Attendees</h3> 
	    </div>               
     </div>  
     <div id="speakers-list">        
        <ul data-role="listview" data-divider-theme="a"> 
        	<?php  foreach($users as $attendee) : ?>        		
        	 	<?php 
        	    	$spkr = 0; 
        	    	$hilightSpeaker = ''; 
        	    	if(array_key_exists($attendee['userID'],$speakers)): 
	        	    	$hilightSpeaker = ' class="speaker-highlight"';
	        	    	$spkr = 1;
	        	    endif;   
        	 	?>                 
	            <li data-theme="c"<?php echo $hilightSpeaker;?>>
	            	<a href="/webapp/view_user?uid=<?php  echo $attendee['userID'];?>&eid=<?php echo $eventId;?>&spr=<?php echo $spkr;?>" data-transition="slide">
	            	<?php if (!empty($attendee['uploaded_photo'])):?>
                 	<img src="/img/upload/user/<?php echo$attendee['uploaded_photo']['s_fname'];?>" height="48" width="48" title="<?php $attendee['first_name'].' '.$attendee['last_name'];?>"/> 
                 	
                 	<?php else:?>            
	                 <img src="/img/no_photo_icon.png" />
	                 <?php endif; ?>
	                 <p class="title"><?php  echo $attendee['last_name']. ', '.$attendee['first_name']; ?></p>
	                 <p class='subtitle'><?php echo $attendee['title']; ?></p>
	                </a>
	                <?php if ('1' != $attendee['is_primary']):?>
	                <a class="companion_icon companion-icon" href="#companion-icon-<?php echo $attendee['userID'];?>" data-rel="popup" data-position-to="window"></a>
	                	    <div id="companion-icon-<?php echo $attendee['userID'];?>" data-role="popup">
						    	<div class="popup_header">Companion of</div>
								<p><?php echo $primaryUsers[$attendee['primary_user_id']]['first_name']?> <?php echo $primaryUsers[$attendee['primary_user_id']]['last_name']?></p>
							</div>
	                <?php endif;?>
	                <div class="forward-button"></div>
	            </li>                    
            <?php  endforeach; ?> 
        </ul>
    </div>            
<?php  include('includes/footer.php'); ?>