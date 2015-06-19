<?php include( 'application.php' ); ?>
<!DOCTYPE html>
<html>
<head>
	<title>My TO DO Application</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="script.js"></script>
</head>
<body>

<div class="wrap">
	<h1>My AJAX TO DO List</h1>

	<?php $empty_class = 'empty';
	if( get_todo_items() ):
		$empty_class .= ' hidden';
		output_todo_list();
	endif; ?>

	<p class="<?php echo $empty_class; ?>">Your To Do list is empty.</p>

	<form method="post" action="" id="new-todo">
		<label>
			<span>Add a new item:</span>
			<input type="text" name="new-item" />
		</label>
		<button type="submit">Add Item</button>
	</form>
</div>

</body>
</html>