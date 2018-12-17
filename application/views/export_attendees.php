<?php 
	$alphabet = array( 'a', 'b', 'c', 'd', 'e',
                       'f', 'g', 'h', 'i', 'j',
                       'k', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
                       );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Export Event Attendees</title>
<?php if(!$export): ?>
<link href="/css/admin.css?v=" rel="stylesheet" type="text/css">
<link href="/css/sprite.css?v=" rel="stylesheet" type="text/css">
<?php endif; ?>
</head>
<body>
<div class="container-fluid">
	<div class="row-fluid">
		<table class="table"> 
			<thead> 
				<tr>
					<th colspan="3">RCG INFORMATION TECHNOLOGY PHILIPPINES INC.</th>
				</tr>
				<tr>
					<th colspan="3">Event Name: <?php echo $event['title']?></th> 
				</tr>	 
				<tr>
					<th>&nbsp;</th>
					<th>Surname</th> 
					<th>FirstName</th>
				</tr>	  
			</thead> 
			<tbody> 
			<?php 
			$empCnt = 1;
			 
			foreach($attendees as $attendee): ?>
			<?php if($attendee['is_primary']): ?> 
			<tr> 
				<td style="font-weight:bold">Employee <?php echo $empCnt?></td> 
				<td><?php echo $attendee['last_name']?></td>
				<td><?php echo $attendee['first_name']?></td> 
			</tr> 
			<?php
				$compCount = 0;  
				if(!empty($attendee['companions'])): 
				foreach($attendee['companions'] as $comp): ?>
					<tr> 
						<td style="font-weight:bold">&nbsp;&nbsp;&nbsp;Companion <?php echo $alphabet[$compCount]?></td> 
						<td><?php echo $comp['last_name']?></td>
						<td><?php echo $comp['first_name']?></td> 
					</tr>
			<?php 
				$compCount++; 	
				endforeach; 
				endif; 	
			?>
			<?php 
				$empCnt++; 
				endif; 
			?>
			<?php endforeach; ?>
			
			</tbody>
		</table>
	</div>	
</div>		
</body>
</html>