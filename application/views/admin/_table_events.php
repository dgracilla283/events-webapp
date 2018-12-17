<table class="table">
	<thead>
		<tr>
			<th width="20%">Title</th>
			<th>Description</th>
			<th width="20%">Date Start</th>
			<th width="20%">Date End</th>
			<th width="18%">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if(!empty($events)) :
				foreach($events as $event):
		?>
		<tr>
			<td><?php echo $event['title'];?></td>
			<td class="center"><?php echo $event['description'];?></td>
			<td class="center"><?php echo date('F d, Y h:i A',strtotime(str_replace('-','/', $event['start_date_time'])));?></td>
			<td class="center"><?php echo date('F d, Y h:i A',strtotime(str_replace('-','/',$event['end_date_time'])));?></td>
			<td class="center" style="vertical-align: middle">
				<a class="btn btn-success btn-mini" href="/admin/view_event?id=<?php echo $event['eventID']?>" title="View Event">
					<i class="icon-zoom-in icon-white"></i>
				</a>
				<a class="btn btn-info btn-mini" href="/admin/edit_event?id=<?php echo $event['eventID'] ?>" title="Edit Event">
					<i class="icon-edit icon-white"></i>
				</a>
				<?php if($pendingRequestsCount =  count($event['pending_requests'])): ?>
				<a class="btn btn-mini btn-primary" style="position:relative" href="/admin/manage_requests/?eid=<?php echo $event['eventID'] ?>" data-event-id="<?php echo $event['eventID'] ?>" title="Manage Requests">
					<i class="icon-tag icon-white"></i>
					<span class="notification red"><?php echo $pendingRequestsCount;?></span>
				</a>
				<?php endif; ?>
				<a class="btn btn-event-duplicate icon-duplicate" href="/admin/duplicate_event_form/?id=<?php echo $event['eventID'] ?>" data-event-id="<?php echo $event['eventID'] ?>" title="Duplicate Event">
					<i class="duplicate-img"></i>
				</a>
				<a class="btn btn-danger btn-mini btn-event-delete" href="#" data-event-id="<?php echo $event['eventID'] ?>" title="Delete Event">
					<i class="icon-trash icon-white"></i>
				</a>
			</td>
		</tr>
		<?php
				endforeach;
			else:
		?>
		<tr><td colspan="5">No records found.</td></tr>
		<?php endif; ?>
	</tbody>
</table>