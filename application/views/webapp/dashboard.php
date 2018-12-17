<?php  include('includes/header.php'); ?>
    <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">    	                
	    	<a href="/" id="btn-home">HOME</a>
	        <a href="/" id="btn-back">BACK</a>               
        	<h3><?php  echo $event['title']; ?></h3>
        </div>	                
     </div> 
     <div id="inner-main-navigation">
     	     <ul data-role="listview" data-divider-theme="a">                    
	            <li data-theme="c">
	                <a href="/webapp/event_details?id=<?php  echo $event['eventID']; ?>" data-transition="slide" data-ajax="false">
	                   <div class="icon-profile"></div><span class="title">Details</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li>
	            <li data-theme="c">
	                <a href="/webapp/schedule?id=<?php  echo $event['eventID']; ?>" data-transition="slide" class="h32" data-ajax="false">
	                   <div class="icon-date"></div><span class="title">Agenda</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li>
	            <?php if(!empty($speakers)): ?>
	            <li data-theme="c">
	                <a href="/webapp/speakers?id=<?php  echo $event['eventID']; ?>" data-transition="slide" class="h32" data-ajax="false">
	                   <div class="icon-speaker"></div><span class="title">Speakers</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li>
	            <?php endif; ?>
	            <li data-theme="c">
	                <a href="/webapp/attendees?id=<?php  echo $event['eventID']; ?>" data-transition="slide" class="h32" data-ajax="false">
	                   <div class="icon-users"></div><span class="title">Attendees</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li> 
	            <li data-theme="c">
	                <a href="/webapp/presentations?id=<?php  echo $event['eventID']; ?>" data-transition="slide" class="h32" data-ajax="false">
	                   <div class="icon-presentation"></div><span class="title">Presentations</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li>    
	            <li data-theme="c">
	                <a href="/webapp/maps?id=<?php  echo $event['eventID']; ?>" data-transition="slide" class="h32" data-ajax="false">
	                   <div class="icon-globe"></div><span class="title">Maps</span>
	                   <div class="forward-button"></div>
	                </a>	                
	            </li>                          
	        </ul>    
    </div>            
 <?php  include('includes/footer.php'); ?>