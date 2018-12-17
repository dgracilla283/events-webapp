<?php include('includes/header.php'); ?>
       <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a id="btn-back" href="/webapp/dashboard?id=<?php echo $eventId;?>">
	            BACK
	        </a> 
	        <h3>Speakers</h3>                
	    </div>
     </div>  
     <div id="speakers-list">  
     	<?php if(!empty($speakers)): ?>      
        <ul data-role="listview" data-divider-theme="a">  
        	<?php foreach($speakers as $spk) : ?>        	                    
            <li data-theme="c" class="speaker-highlight">   
            	 <a href="/webapp/view_user?uid=<?php  echo $spk['user_id'];?>&eid=<?php echo $eventId;?>&spr=1" data-transition="slide">         
                 	<?php if (!empty($spk['uploaded_photo'])):?>
                 	<img src="/img/upload/user/<?php echo$spk['uploaded_photo']['s_fname'];?>" width="48" height="48" title="<?php $spk['first_name'].' '.$spk['last_name'];?>"/>                 	
                 	<?php else:?>
                 	<img src="/img/no_photo_icon.png" />
                 	<?php endif;?>
                 	<p class="title nopad"><?php echo $spk['last_name']. ', '.$spk['first_name']; ?></p>
                 	<p class='subtitle'><?php echo $spk['title']; ?></p>
                 	<div class="forward-button"></div>
                 </a>                 
            </li>                    
            <?php endforeach; ?> 
        </ul>
        <?php else: ?>
        <p class="no-speaker">There are no speaker(s) for this event.</p>
        <?php endif; ?>
    </div>            
<?php include('includes/footer.php'); ?> 