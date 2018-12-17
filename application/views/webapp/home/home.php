<?php $this->load->view('webapp/includes/header.php') ?>

     <div data-theme="b" data-role="header" data-tap-toggle="false" data-update-page-padding="false" data-position="fixed" id="header-inner">
     	<a href="#" data-slidemenu="#slidemenu" data-slideopen="false" data-icon="smico" data-corners="false" data-iconpos="notext">Menu</a>     	
    	<div class="header-inner-content">   		    
	    	<h1>RCG Events</h1>
	        <div class="settings_wrapper">
	        	<ul>
	        		<li>
	        			<a href="/webapp/user_account">
		       				<div class="my_account_icon_big"></div>
		       				<span>My Account</span>
	       				</a>
	        		</li>
	        		<li class="line_spacer"></li>
	        		<?php if(!empty($events['myEvents'])): ?>
	        		<li>
	        			<a href="/webapp/my_activities">
	        				<div class="my_activity_icon_big"></div>
		        			<span>My Activities</span>
	        			</a> 
	        		</li>
	        		<?php endif; ?>
	        	</ul>
	        </div>
	    </div>	    
     </div>
     
   <div id="events-list">
   <?php 
   		//-- load My Events
		$this->load->view('webapp/home/my_events.php', $events);
		
		if(!empty($events['myPendingEvents'])) {
			//-- load My Pending Events
	   		$this->load->view('webapp/home/my_pending_events.php', $events);
		}
		
	   	if(!empty($events['otherEvents'])) {
   			//- load My Other Events
	   		$this->load->view('webapp/home/other_events.php', $events);
	   	}
	   	
	   	if(!empty($events['myPastEvents'])) {
	   		//- load My Past Events
	   		$this->load->view('webapp/home/my_past_events.php', $events);
	   	}
   	?>
   	</div>
    
    <div data-role="content" id="download-c">
    		<a href="/downloads/RCGEventPlanner.apk" id="download-link" rel="external">
	        	<i class="icon-android"></i>
	            <span>Download Android App</span>
        	</a> 	         
    </div>    	   
<?php $this->load->view('webapp/includes/footer.php') ?>