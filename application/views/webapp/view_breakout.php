<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a id="btn-back" href="/webapp/view_itinerary?iid=<?php echo $breakout['itinerary_id'];?>&eid=<?php echo $eventId;?>">
	            BACK
	        </a>       
        	<h3>Session Details</h3>
        </div>                
     </div>  
     <div data-role="content">     	
     	<div class="box-white clearfix ui-listview ui-listview-inset ui-corner-all ui-shadow">		
			<div class="f-left span-left-10"> 
				<p class="title"><?php echo $breakout['title']?></p>
				<p>Date: <?php echo date('l, F d, Y', strtotime($breakout['start_date_time']));?></p>						
				<p>Time: <?php echo date('h:i A', strtotime($breakout['start_date_time']));?> - <?php echo date('h:i A', strtotime($breakout['end_date_time']));?></p>							
				<?php if($breakout['location']):?> <p>Location: <?php echo $breakout['location']?> </p><?php endif; ?> 
				<?php if($speakerName):?> <p>Speaker: <?php echo $speakerName?> </p><?php endif; ?> 							
			</div>    
		</div> 
		<div class="box-white clearfix ui-listview ui-listview-inset ui-corner-all ui-shadow">
			<?php if($breakout['description']):?> <p><span class="lead">Summary:</span> <?php echo $breakout['description']?></p><?php endif; ?> 	
			
			<?php if(!empty($breakout['guests'])):?> 
				<div class="ui-grid-a">
	                    <div class="ui-block-a"><b>Name</b></div>
	                    <div class="ui-block-b"><b>Team</b></div>
	             </div>						 	
				<?php foreach($breakout['guests'] as $guest):?> 
					<?php if(!empty($guest['team'])):?> 	
							 <div class="ui-grid-a">
				                    <div class="ui-block-a"><?php echo $users[$guest['user_id']]['first_name'] .' '.$users[$guest['user_id']]['last_name']?></div>
				                    <div class="ui-block-b"><?php echo $guest['team'] ?></div>
				             </div>						 	
	             <?php 	
	             			endif; 
	             		endforeach;              
	             	endif; 
	             ?> 
		</div> 	
		
    </div>            
 <?php include('includes/footer.php'); ?>