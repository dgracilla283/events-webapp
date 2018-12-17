<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home" data-ajax="false">HOME</a> 
	        <a id="btn-back" href="/webapp/dashboard?id=<?php echo $event['eventID'];?>" data-ajax="false">BACK</a>
	        <h3>Speaker Confirmation</h3>                
	    </div>
     </div>      
     <div data-role="content">
     	<div class="box-white">
			
			<img class="event-details-image" src="/img/Logo.jpg" width="80" height="100" align="left" /> 
			<div class="right-panel"> 
				<p class="title"><?php echo $event['title']?></p>					
				<p><?php echo date('l, F d, Y h:i A', strtotime($event['start_date_time'])) .' to '. date('l, F d, Y h:i A', strtotime($event['end_date_time'])); ?></p>						
				<p>Location: <?php echo $event['location']?></p>
			</div> 
		</div>		
    </div>   
    <br />
<?php include('includes/speaker_confirmation_schedule.php'); ?>
<?php include('includes/footer.php'); ?>