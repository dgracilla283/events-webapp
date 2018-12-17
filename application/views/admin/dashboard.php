<?php include('includes/header.php'); ?>
	<div id="content" class="span10">
		<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class=" icon-th-list"></i> Events</h2>
					</div>
					<div class="box-content">
						<ul class="nav nav-tabs" id="myTab">
							<li class="active"><a href="#published">Published</a></li>
							<li><a href="#unpublished">Unpublished</a></li>
							<li><a href="#completed">Completed</a></li>		
						</ul>
						<div id="myTabContent" class="tab-content">
							<div class="tab-pane active" id="published">
								<div id="div-published" class="clearfix">
									<br /><span class="loader">&nbsp;</span><br />
								</div>
							</div>
							<div class="tab-pane" id="unpublished">
								<div id="div-unpublished" class="clearfix">
									<br /><span class="loader">&nbsp;</span><br />
								</div>
							</div>
							<div class="tab-pane" id="completed">
								<div id="div-completed" class="clearfix">
									<br /><span class="loader">&nbsp;</span><br />
								</div>
							</div>
						</div>
					</div>
				</div><!--/span-->
			</div><!--/row-->
	</div>

<div class="modal hide fade" id="popup-event-delete" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Delete Event</h3>
	</div>
	<div class="modal-body">
		Loading ...
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<a href="#" class="btn btn-primary" id="btn-confirm-event-delete">Yes</a>
	</div>
</div>

<div class="modal hide fade" id="popup-event-duplicate" data-backdrop="static">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>Duplicate Event</h3>
	</div>
	<form name="duplicate_event_form" id="duplicate_event_form" enctype="multipart/form-data" method="post">
	<div class="modal-body">
		Loading ...
	</div>
	<div class="modal-body-form">
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">No</a>
		<button type="submit" class="btn btn-primary" id="btn-confirm-event-duplicate">Yes</button>
	</div>
	</form>
</div>

<?php include('includes/footer.php'); ?>