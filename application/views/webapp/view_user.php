<?php include('includes/header.php'); ?>
     <div data-theme="b" data-role="header" data-position="fixed" id="header-inner">
    	<div class="header-inner-content">                 
	    	<a href="/" id="btn-home">HOME</a> 
	        <a id="btn-back" data-theme="b" 
	        <?php if (!$breakoutId): ?>
	        href="javascript:"  onclick="history.go(-1); return false;"
		    <?php else :?>		
		     href="/webapp/view_itinerary?iid=<?php echo $itineraryId;?>&eid=<?php echo $eventId;?>&bid=<?php echo $breakoutId?>"
		     <?php endif;?>    
      			>BACK</a> 
	       	<h3>
	        <?php if($fromSpeaker): //If speaker ?>
	        Speaker Details
	        <?php elseif(!$fromSpeaker && empty($userInfo['primary_user'])): ?>
	        Guest Details
	        <?php else: ?>
	        Companion Details
	        <?php endif; ?>
		    </h3>
		    	                
	    </div>    
     </div> 
     
     <div data-role="content" class="speaker-details" id="user-details">   
     	<!-- User Details -->	
		<div class="speaker-details-info">
			<!-- User Photo -->
		    <?php if(!empty($userInfo['uploaded_photo'])): ?>
				<img src="/img/upload/user/<?php echo $userInfo['uploaded_photo']['s_fname'] ?>" title="<?php $userInfo['first_name'] . ' ' . $userInfo['last_name'] ?>" width="70" height="70" />
			<?php else: ?>
				<img src="/img/no_photo_icon_large.png" width="70" height="70" />
			<?php endif;?>
			<!-- End User Photo -->
			
			<!-- User General Info -->
			<div> 
				<h3 class="title"><?php echo $userInfo['first_name'].' '.$userInfo['last_name'];?></h3>
				<p class="subtitle"><?php echo $userInfo['title']?></p>
				<p class="subtitle"><?php echo $userInfo['affiliation']?></p>
				<p class="subtitle"><?php echo $userInfo['industry']?></p>
				<?php if(!empty($userInfo['primary_user'])): ?>
				<p class="subtitle">Companion of 
					<a href="/webapp/view_user?uid=<?php echo $userInfo['primary_user']['userID'] ?>&eid=<?php echo $eventId ?>">
					<?php echo $userInfo['primary_user']['first_name'] . ' ' . $userInfo['primary_user']['last_name'] ?>
					</a>
				</p>
				<?php endif; ?>
			</div> 
			<!-- End User General Info -->
		</div>
		
		<!-- User Bio -->
		<?php if($userInfo['bio']): ?>
		<div class="bio-details">
			<p><strong>BIO: </strong><?php echo $userInfo['bio']?></p>			 	
		</div>
		<?php endif;?>
		<!-- End User Bio -->
		
		<!-- Programs List -->		
		<?php
		if(!empty($programs)) : ?>	
			
	     	<div class="speaker-session-header">  		
				<?php echo count($programs) > 1 ? 'Activities' : 'Activity'; ?>: 							             
			</div>
			
			<div id="speaker-session">
		     	<ul data-role="listview" data-divider-theme="a">
		     	<?php 
		  		$eventDates = array();
		  		$newDate = true;  
		  		foreach ($programs as $program):		  			
	  				if(!empty($program['itineraryID'])) { 
	  					$id = $program['itineraryID']; 
						$url = '/webapp/view_itinerary?iid='.$program['itineraryID'].'&eid='.$eventId;
	  				} elseif(!empty($program['breakoutID'])) {
	  					$url = '/webapp/view_itinerary?iid='.$program['breakoutID'].'&eid='.$eventId;
	  					$id = $program['itinerary_id']; 
	  				} 
		  			$programStartDate = date('l, F d, Y', strtotime(str_replace('-', '/',$program['start_date_time']))); 
		  			$programStartTime = date('h:i A', strtotime($program['start_date_time']));
		  			$programEndDate = date('m-d-Y', strtotime(str_replace('-', '/',$program['end_date_time'])));
		  			$programEndTime =	date('h:i A', strtotime($program['end_date_time']));  			
		  		?>
		  		<?php if(!empty($id)): ?>
		  		<li data-theme="c"> 
		            <a href="/webapp/view_itinerary?iid=<?php echo $id;?>&eid=<?php echo $eventId;?>" data-transition="slide">
		               <p class="title"><?php echo $program['title']; ?></p>
		               <p class="subtitle"><?php echo $programStartDate;?></p>
		               <p class="subtitle"><?php echo $programStartTime. ' - ' .$programEndTime; ?></p>
		               <?php if($program['location']) : ?> 
		               <p class="subtitle">Location: <?php echo $program['location']; ?></p> 
		               <?php endif; ?>		             
		               <?php if(!empty($userPreferences)): ?>		            
			            <div class="user-preferences">			            	
			            	<?php		            		
			            		foreach($program['preferences'] as $pref):
			            			$getValue = ('textbox' == $pref['optionDisplayType'] || 'textarea' == $pref['optionDisplayType']);		            			
			            			$userSelectedOptions = !empty($userPreferences[$pref['activityPreferenceID']]) ? $userPreferences[$pref['activityPreferenceID']] : array();
			            			$options = make_new_key($pref['options'], 'activityPreferenceOptionID');
			            			$selected = array();   
			            			if(!empty($userSelectedOptions[0])){
				            			foreach($userSelectedOptions as $key => $val){
				            				$selected[] = $getValue ? $val : $options[$val]['title']; 
				            			}
				            		}
			            	?>
			            	<?php if(!empty($selected)):?><p class="subtitle"><?php echo $pref['title'];?>: <?php echo implode(', ', $selected);?></p><?php endif; ?>
			            	<?php endforeach;?>
			            </div>
			            <?php endif;?>  
		               <div class="forward-button"></div>		                
		            </a>		            
			    </li>
			    <?php endif; ?>
				<?php endforeach; ?>
				</ul>
			</div>
		<?php endif;?>
		<!-- End Program List -->
		
		<!-- Pending Requests -->		
		<?php
		if(!empty($pendingRequests)) : ?>	
			
	     	<div class="speaker-session-header">  		
				<?php echo count($pendingRequests) > 1 ? 'Pending Requests' : 'Pending Request'; ?>: 							             
			</div>
			
			<div id="speaker-session">
		     	<ul data-role="listview" data-divider-theme="a">
		     	<?php 
		  		$eventDates = array();
		  		$newDate = true;  
		  		foreach ($pendingRequests as $program):		  			
	  				if(!empty($program['itineraryID'])) { 
	  					$id = $program['itineraryID']; 
						$url = '/webapp/view_itinerary?iid='.$program['itineraryID'].'&eid='.$eventId;
	  				} elseif(!empty($program['breakoutID'])) {
	  					$url = '/webapp/view_itinerary?iid='.$program['breakoutID'].'&eid='.$eventId;
	  					$id = $program['itinerary_id']; 
	  				} 
		  			$programStartDate = date('l, F d, Y', strtotime(str_replace('-', '/',$program['start_date_time']))); 
		  			$programStartTime = date('h:i A', strtotime($program['start_date_time']));
		  			$programEndDate = date('m-d-Y', strtotime(str_replace('-', '/',$program['end_date_time'])));
		  			$programEndTime =	date('h:i A', strtotime($program['end_date_time']));  			
		  		?>
		  		<?php if(!empty($id)): ?>
		  		<li data-theme="c"> 
		            <a href="/webapp/view_itinerary?iid=<?php echo $id;?>&eid=<?php echo $eventId;?>" data-transition="slide">
		               <p class="title"><?php echo $program['title']; ?></p>
		               <p class="subtitle"><?php echo $programStartDate;?></p>
		               <p class="subtitle"><?php echo $programStartTime. ' - ' .$programEndTime; ?></p>
		               <?php if($program['location']) : ?> 
		               <p class="subtitle">Location: <?php echo $program['location']; ?></p> 
		               <?php endif; ?>		             
		               <?php if(!empty($userPreferences)): ?>		            
			            <div class="user-preferences">			            	
			            	<?php		            		
			            		foreach($program['preferences'] as $pref):
			            			$getValue = ('textbox' == $pref['optionDisplayType'] || 'textarea' == $pref['optionDisplayType']);		            			
			            			$userSelectedOptions = !empty($userPreferences[$pref['activityPreferenceID']]) ? $userPreferences[$pref['activityPreferenceID']] : array();
			            			$options = make_new_key($pref['options'], 'activityPreferenceOptionID');
			            			$selected = array();   
			            			if(!empty($userSelectedOptions[0])){
				            			foreach($userSelectedOptions as $key => $val){
				            				$selected[] = $getValue ? $val : $options[$val]['title']; 
				            			}
				            		}
			            	?>
			            	<?php if(!empty($selected)):?><p class="subtitle"><?php echo $pref['title'];?>: <?php echo implode(', ', $selected);?></p><?php endif; ?>
			            	<?php endforeach;?>
			            </div>
			            <?php endif;?>  
		               <div class="forward-button"></div>		                
		            </a>		            
			    </li>
			    <?php endif; ?>
				<?php endforeach; ?>
				</ul>
			</div>
		<?php endif;?>
		<!-- End Pending Requests -->
		
		<!-- Companion List -->
		<?php if(!empty($companions)): ?>
		
		<div class="speaker-session-header">Companions</div>
		<ul data-inset="false" data-role="listview" data-divider-theme="a">
		<?php foreach($companions as $companion): ?>
		<li data-theme="c">
			<a class="title" href="/webapp/view_user?uid=<?php echo $companion['userID'] ?>&eid=<?php echo $eventId ?>&spr=0" data-transition="slide">
				<?php echo $companion['last_name'] . ', ' .$companion['first_name'] ?>
			</a>
		</li>      
		<?php endforeach; ?>
		<?php endif; ?>
		<!-- End Companion list -->
    </div>            
 <?php include('includes/footer.php'); ?>