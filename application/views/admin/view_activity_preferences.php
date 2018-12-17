<div class="tab-pane active" id="view-activity-preferences">
	<?php if(!empty($activityPreferences)): ?>					 
			<table class="table" id="table-activity-preferences">
		 		<tr>
		 			<th>Title</th>
		 			<th>Display Type</th>
		 			<th>Options</th>
		 		</tr>
		 		<?php foreach($activityPreferences as $pref):?>				 		
		 		<tr>
		 			<td><?php echo htmlentities($pref['title'])?></td>
		 			<td><?php echo htmlentities($pref['optionDisplayType'])?></td>
		 			<td>
		 				<?php 
		 					$options = array();
			 				foreach($pref['options'] as $option) : 
			 					$options[] = $option['title']; 
			 				endforeach; 
			 				echo implode(', ', $options); 
		 				?> 
		 			</td>
		 		</tr>		 
		 		<?php endforeach; ?>	
		 	</table>	
	<?php else:?>
		<p> No Activity Preferences. </p>
	<?php endif; ?>
</div>
