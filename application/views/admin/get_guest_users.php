<table class="table table-striped">
	<thead>
		<tr>
			<th>Name</th>
			<th>Primary User</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($users)) { ?>
		<?php foreach ($users as $user):?>
			<tr>
				<td class="center"><?php echo $user['last_name']?>, <?php echo $user['first_name']?> (<?php echo ucfirst($user['type']);?>)</td>
				<td class="center"><?php echo $primaryUser[$user['primary_user_id']]['first_name']?> <?php echo $primaryUser[$user['primary_user_id']]['last_name']?></td>
				<td class="center">
					<a class='btn' href="/admin/add_guest?uid=<?php  echo $user['userID']; ?>"><i class="icon-black icon-edit"></i></a>
					<a class='btn btn-delete-user' data-user-id="<?php echo $user['userID']?>" href="javascript:;"><i class="icon-black icon-trash"></i></a>
				</td>
			</tr>
		<?php endforeach;?>
	<?php } else { ?>
		<tr>
			<td colspan="4" class="alert-error text-center">No record/s retrieved.</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div class="pagination pagination-centered">
	<ul id="user-pagination">
		<?php echo $pagination;?>
	</ul>
</div>