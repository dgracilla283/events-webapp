<div class="tab-pane active" id="view-presentations">
<?php if(!empty($presentations)): ?>
	<table class="table" id="table-presentationss">
		<tr>
			<th>Title</th>
			<th>Url</th>
		</tr>
		<?php foreach($presentations as $presentation):?>
		<tr>
			<td><?php echo htmlentities($presentation['title'])?>
			</td>
			<td><?php
			if ($presentation['url'])
			echo '<a href="'.prep_url($presentation['url']).'" target="_blank">'.htmlentities($presentation['url']). '</a>'
					 			?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else:?>
	<p>No Presentations.</p>
	<?php endif; ?>
</div>
