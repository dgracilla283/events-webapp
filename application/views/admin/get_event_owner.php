<?php if(!empty($owners)): ?>
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
	  	<?php foreach($owners as $owner): ?>  
		<tr>
			<td width="20%"><?php echo $owner['last_name'].', ' .$owner['first_name']?></td>			
			<td width="30%"><?php echo $owner['title'];?></td>
			<td width="20%"><?php echo $owner['affiliation'];?></td>
			<td width="15%"><?php echo $owner['industry'];?></td>
			<td class="center"> 
			<a class="btn btn-delete-event-owner" href="#" data-user-id="<?php echo $owner['userID'];?>"" title="Delete Owner"><i class="icon-black icon-trash"></i></a>			 										
			</td>                                       
		</tr>
		<?php endforeach; ?>								
	  </tbody>
 </table>  
 <?php else: ?>
  <div class="alert alert-error">	
	<strong>No event owners!</strong>
</div>    	
 <?php endif; ?>