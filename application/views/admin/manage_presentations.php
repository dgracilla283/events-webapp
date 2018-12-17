<?php include('includes/header.php'); ?>
<div id="content" class="span10">
	<div class="row-fluid sortable">
		<div class="box">
			<div class="box-header well">
				<h2><i class="icon-th"></i> Add Presentation</h2>
				<div class="box-icon">
					<a class="btn btn-close btn-round" href="/admin/dashboard" title="Back to events list"><i class=" icon-home"></i></a>
				</div>
			</div>
			<div class="box-content clearfix">
				<?php if (!empty($transactionMessage)): ?>
					<?php  $alertClass = ($transaction) ? 'alert-success' : 'alert-error' ?>
					<div class="alert <?php echo $alertClass; ?>">
						<strong><?php echo $transactionMessage; ?></strong>
					</div>
				<?php endif; ?>
				<div class="clearfix">
					<?php if (!empty($getVar['pid'])) { ?>
					<a class="btn btn-right" href="/admin/manage_presentations?id=<?php echo $getVar['id']; ?>&eid=<?php echo $getVar['eid']; ?>" style="margin-right: 10px">
						Add Presentation
					</a>
					<?php } ?>
					<a id="btn-add-back" class="btn btn-right" href="/admin/view_event?id=<?php echo $getVar['eid']?>" style="margin-right: 10px">
						<i class="icon icon-undo icon-black"></i>
						Back to event
					</a>
				</div>
				<form id="form-presentation" class="form-horizontal frm-presentation" method="post" action="/admin/add_edit_presentation" enctype="multipart/form-data">
					<fieldset>
						<div class="control-group">
							<label class="control-label">Display Type</label>
							<div class="controls">
								<?php
									$rbtUrl = $rbtDocument = '';

									if (!empty($postback['display_type'])) {
										if ($postback['display_type'] == 'url') {
											$rbtUrl = 'checked="checked"';
										} else {
											$rbtDocument = 'checked="checked"';
										}
									} else {
										$rbtUrl = 'checked="checked"';
										if (!empty($presentation)) {
											if ($presentation[0]['display_type'] == 'url') {
												$rbtUrl = 'checked="checked"';
											} else {
												$rbtDocument = 'checked="checked"';
											}
										}
									}
								?>
								<input type="radio" name="rbtnDisplayType[]" class="valign-top rbtnDisplayType" style="margin-top: 6px" value="url" <?php echo $rbtUrl; ?> /> URL
								&nbsp;&nbsp;&nbsp;
								<input type="radio" name="rbtnDisplayType[]" class="valign-top rbtnDisplayType" style="margin-top: 6px" value="document" <?php echo $rbtDocument; ?> /> Document
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Title</label>
							<div class="controls">
								<?php
									$value = '';
									if (!empty($postback['title'])) {
										$value = $postback['title'];
									} else {
										if (!empty($presentation)) {
											$value = $presentation[0]['title'];
										}
									}
								?>
								<input class="focused input-xlarge" type="text" name="title" value="<?php echo $value ;?>" maxlength="100"/>
							</div>
						</div>
						<div id="presentationGrpLink" class="control-group">
							<label class="control-label">Link</label>
							<div class="controls">
								<?php
									$value = '';
									if (!empty($postback['url'])) {
										$value = $postback['url'];
									} else {
										if (!empty($presentation)) {
											$value = $presentation[0]['url'];
										}
									}
								?>
								<input class="focused input-xlarge" type="text" name="url" value="<?php echo $value ;?>" maxlength="100"/>
							</div>
						</div>
						<div id="presentationGrpFile" class="control-group hide">
							<label class="control-label">File</label>
							<div class="controls">
								<input type="file" name="filePresentation" /><br />
									<?php
										if ($presentation[0]['document_meta']) {
											$documentMeta = unserialize($presentation[0]['document_meta']);
									?>
											<a href="/img/upload/presentation/<?php echo $documentMeta['file_name']; ?>" target="_blank"><?php echo $documentMeta['file_name']; ?></a>
									<?php } ?>
							</div>
						</div>
						<input type="hidden" name="presentation_category_id" value="<?php echo $presentationCategory['presentationCategoryID']; ?>" />
						<input type="hidden" name="presentationID" value="<?php echo $getVar['pid']; ?>" />
						<input type="hidden" name="hfEventId" value="<?php echo $getVar['eid']; ?>" />
					</fieldset>
					<div class="form-actions">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button class="btn" id="cancel-add">Cancel</button>
					</div>
				</form>
			</div>
		</div>
		<div class="box">
			<div class="box-header well">
				<h2>
					<i class="icon-th-list"></i> <?php echo htmlentities($presentationCategory['name'])?> - Presentation
				</h2>
			</div>
			<div class="box-content" id="add_presentation">
				<?php if(!empty($presentations)): ?>
					<div class="control-group pull-right">
						<a id="btn-save-order" class="btn btn-right" style="margin-right: 10px">Save Order</a>
					</div>
					<div id="order-update-message" class="pull-right" style="display:none; margin-top: 8px; margin-right: 10px"></div>
					<div class="clear"></div>
				<?php endif; ?>
				<div id="presentation_list">
					<?php if(!empty($presentations)): ?>
						<div id="view-presentations">
							<table class="table" id="table-presentations">
								<thead>
								<tr>
									<th width="20%">Title</th>
									<th width="20%">URL</th>
									<th width="40%">File</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach($presentations as $presentation):?>
								<tr class="presentation_entries">
									<td>
										<input type="hidden" name="presentation_order" value="<?php echo $presentation['presentationID']?>" />
										<?php echo htmlentities($presentation['title'])?>
									</td>
									<td>
										<?php
											echo ($presentation['url']) ? '<a href="'.prep_url($presentation['url']).'" target="_blank">'.htmlentities($presentation['url']). '</a>' : '-';
										?>
									</td>
									<td>
										<?php
											if ($presentation['document_meta']) {
												$documentMeta = unserialize($presentation['document_meta']);
										?>
												<a href="/img/upload/presentation/<?php echo $documentMeta['file_name']; ?>" target="_blank"><?php echo $documentMeta['file_name']; ?></a>
										<?php
											} else {
												echo '-';
											}
										?>

									</td>
									<td class="center">
										<a class="btn btn-edit-presentation" href="manage_presentations?id=<?php echo $presentation['presentation_category_id'];?>&pid=<?php echo $presentation['presentationID']; ?>&eid=<?php echo $getVar['eid']; ?>" data-pid="<?php echo $presentation['presentationID'];?>" data-presentation-category-id="<?php echo $presentation['presentation_category_id'];?>" title="Edit Presentation">
											<i class="icon-black icon-edit"></i>
										</a>
										<a class="btn btn-delete-presentation" href="#" data-pid="<?php echo $presentation['presentationID'];?>" title="Delete Presentation">
											<i class="icon-black icon-trash"></i>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php else: ?>
						<div class="alert alert-error">
							<strong>No Entries.</strong>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php // Add/Edit activity Attendees?>
<div class="modal hide fade" id="add-presentation-popup" data-backdrop="static" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Add Presentation</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br />
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Close</a>
		<a href="#" class="btn btn-primary btn-save" id="btn-save-presentation">Save Changes</a>
	</div>
</div>

<?php // Add/Edit activity Attendees?>
<div class="modal hide fade" id="delete-presentation-popup" data-backdrop="static" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Presentation</h3>
	</div>
	<div class="modal-body">
		  <span class="loader">&nbsp;</span> <br />
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-yes-delete-presentation">Yes</a>
	</div>
</div>

<?php include('includes/footer.php'); ?>