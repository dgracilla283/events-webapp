<?php if(!empty($presentation_categories)): ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($presentation_categories as $category): ?>
			<tr>
				<td width="80%">
					<?php echo $category['name'];?>
				</td>
				<td class="center">
					<a class="btn edit-presentation-category" href="#" data-presentation-category-id="<?php echo $category['presentationCategoryID']; ?>" title="Edit Category">
						<i class="icon-black icon-edit"></i>
					</a>
					<a data-presentation-category-id="<?php echo $category['presentationCategoryID']; ?>" data-event-id="<?php echo $category['event_id']; ?>" title="Add Presentation" href="#" class="btn btn-presentation">
						<i class="icon-black icon-plus-sign"></i>
					</a>
					<a class="btn btn-delete-presentation-category" href="#" data-presentation-category-id="<?php echo $category['presentationCategoryID'];?>" title="Delete Category">
						<i class="icon-black icon-trash"></i>
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<div class="alert alert-error">
		<strong>No presentation categories!</strong>
	</div>
<?php endif; ?>