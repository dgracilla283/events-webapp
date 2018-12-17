<?php if(!empty($attendees)): ?>
<table class="table table-striped">
	  <thead>
		  <tr>
			  <th>Name</th>
			  <th>Position/Title</th>
			  <th>Company</th>
			  <th>Industry</th>
			  <th>Actions</th>                              
		  </tr>
	  </thead>   
	  <tbody>
	  	<?php foreach($attendees as $guest): ?>  
		<tr>
			<td width="20%"><?php echo $guest['last_name'].', ' .$guest['first_name']?></td>			
			<td width="30%"><?php echo $guest['title'];?></td>
			<td width="20%"><?php echo $guest['affiliation'];?></td>
			<td width="15%"><?php echo $guest['industry'];?></td>
			<td class="center"> 				 										
				<a class="btn btn-delete-guest<?php if(!empty($userCompanions[$guest['userID']])): echo ' has-companions';endif;?>" href="#" data-user-id="<?php echo $guest['userID'];?>"" title="Delete Attendee"><i class="icon-black icon-trash"></i></a>
			</td>                                       
		</tr>
		<?php endforeach; ?>								
	  </tbody>
 </table>  
 <?php else: ?>
  <div class="alert alert-error">	
	<strong>No event guests!</strong>
</div>    	
 <?php endif; ?>