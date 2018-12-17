<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a>
	        <a id="btn-back" href="javascript:" onclick="history.go(-1); return false;">
	            BACK
	        </a>
	        <h3><?php if(!$itinerary['breakout_status'] || !empty($breakout)): echo 'Activity Details'; else: echo 'Activity Sessions'; endif; ?></h3>
	    </div>                    
     </div>  
     <div data-role="content" id="team-session">    
     	<?php if(!$itinerary['breakout_status']): ?>  
     	    	<?php include('includes/agenda_view.php'); ?>        
		<?php elseif(!empty($breakout)): // breakout found 1 ?>
				<?php include('includes/activity_view.php'); ?> 	 	
		<?php else: //Multiple brekouts Found ?>
				<?php include('includes/agenda_view.php'); ?>
				<?php include('includes/multiple_activity_view.php'); ?>
		<?php endif; ?>  
    </div>            
 <?php include('includes/footer.php'); ?>