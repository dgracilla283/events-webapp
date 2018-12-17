<?php include('includes/header.php'); ?>
<div id="content" class="span10">
	<div class="row-fluid sortable">
		<div class="box span12">
			<div class="box-header well">
				<h2>
					<i class="icon-th-list"></i>Manage Requests
				</h2>
				<div class="box-icon">
					<a class="btn btn-close btn-round" href="/admin/dashboard"
						title="Back to events list"><i class=" icon-home"></i> </a>
				</div>
			</div>
			<div class="box-content clearfix">
				<ul class="nav nav-tabs" id="requests-tab">
					<li<?php if('pending' == $status) echo ' class="active"';?>><a href="?status=pending&eid=<?php echo $eventID?>">Pending Requests</a></li>
					<li<?php if('approved' == $status) echo ' class="active"';?>><a href="?status=approved&eid=<?php echo $eventID?>">Approved Requests</a></li>
					<li<?php if('rejected' == $status) echo ' class="active"';?>><a href="?status=rejected&eid=<?php echo $eventID?>">Rejected Requests</a></li>
				</ul>
				<div id="tab-content" class="tab-content">
					<div class="tab-pane active" id="pending">

					<?php if (!empty($errorMessage)) { ?>
						<div class="alert alert-error">
							<ul class="ul-list">
								<?php foreach ($errorMessage as $message) { ?>
									<li><strong><?php echo $message; ?></strong></li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>

					<?php if(!empty($requests)): ?>
						<form class="frm-manage-request" method="post">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><input type="checkbox" class="check-all" title="Check All" />
										</th>
										<th>Name</th>
										<th>Join Request</th>
										<th>Registration Date</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($requests as $request): ?>
									<tr>
										<td><input type="checkbox" name="eventAttendeeIDs[]"
											value="<?php echo $request['eventAttendeeID']?>" /></td>
										<td><?php echo $request['first_name'].' '.$request['last_name'];?>
										</td>
										<td class="center"><?php
										if('event' == $request['reference_type']):
										echo 'Event: '.$events[$request['reference_id']]['title'];
										elseif('agenda' == $request['reference_type'] && !empty($eventAgenda[$request['reference_id']])):
										echo 'Agendum: '.$eventAgenda[$request['reference_id']]['title'];
										elseif('activity' == $request['reference_type'] && !empty($eventActivities[$request['reference_id']])):
										echo 'Activity: '.$eventActivities[$request['reference_id']]['title'];
										endif;
										?>
										</td>
										<td><?php if ($request['date_joined']) echo date('l, F d, Y h:i A', strtotime($request['date_joined'] . " + 12 hours" ));?>  </td>
									</tr>
									<?php endforeach;?>
								<tbody>
							</table>
							<div class="actions">
								<div class="pagination" style="float:right">
									<ul id="user-pagination">
										<?php echo $pagination;?>
									</ul>
								</div>
								<select name="status">
									<option></option>
									<?php if('approved' != $status):?><option value="approved">Approve</option><?php endif;?>
									<?php if('rejected' != $status):?><option value="rejected">Reject</option><?php endif;?>
									<?php if('pending' != $status):?><option value="pending">Pending</option><?php endif;?>
								</select>
								<button class="btn btn-primary btn-update-status" class="btn"
									href="#">
									<i class="icon icon-save icon-white"></i> Save
								</button>
							</div>
						</form>
						<?php else: ?>
						<div class="alert alert-error">No <?php echo $status?> requests.</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>