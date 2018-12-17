<?php if(!empty($guestActivities)): ?>
This Attendee has concurrent activities.


<?php 

foreach($guestActivities as $guest): 
$startDate = date_format(date_create($guest['start_date_time']), "F j, Y, g:i a");
$endDate = date_format(date_create($guest['end_date_time']), "F j, Y, g:i a");
?>
<?php echo $guest['title']." - ".$startDate." to ".$endDate;?>
		<?php //print_r($guestActivities);?>

<?php endforeach; ?>

<?php else: ?>
<?php endif; ?>