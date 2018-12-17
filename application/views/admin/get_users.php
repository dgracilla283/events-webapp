<table class="table table-striped">
	<thead>
		<tr>
			<th width="15%">Name</th>
			<th width="15%">Email / Username</th>
			<th width="25%">Position/Title</th>
			<th width="15%">Company</th>
			<th width="10%">Industry</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($users)) { ?>
	<?php foreach ($users as $user):?>
		<tr>
			<td class="center"><?php echo trim($user['last_name'])?>, <?php echo $user['first_name']?></td>
			<td><?php echo $user['email']?></td>
			<td class="center"><?php echo $user['title']?></td>
			<td class="center"><?php echo $user['affiliation']?></td>
			<td class="center"><?php echo $user['industry']?></td>
			<td class="center">
				<a class='btn' href="/admin/add_user?uid=<?php  echo $user['userID']; ?>"><i class="icon-black icon-edit"></i></a>
				<a class='btn btn-delete-user' data-user-id="<?php echo $user['userID']?>" href="javascript:;"><i class="icon-black icon-trash"></i></a>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php } else { ?>
		<tr>
			<td colspan="7" class="alert-error text-center">No record/s retrieved.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div class="pagination pagination-centered">
	<ul id="user-pagination">
		<?php echo $pagination;?>
	</ul>
</div>