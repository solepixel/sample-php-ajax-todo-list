<?php error_reporting( E_ALL ); ?>
<?php include( 'application.php' ); ?>
<!DOCTYPE html>
<html>
<head>
	<!-- application title -->
	<title>My TO DO Application</title>
	<!-- import Open Sans font from Google -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
	<!-- primary stylesheet -->
	<link href="styles.css" rel="stylesheet" type="text/css">
	<!-- import jQuery from Google -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<!-- load javascript -->
	<script src="script.js"></script>
</head>
<body>

<div class="wrap">
	<header>
		<h1>My AJAX TO DO List</h1>
	</header>

	<!-- the to do list -->
	<?php $empty_class = 'empty';
	if( get_todo_items() ):
		$empty_class .= ' hidden';
		output_todo_list();
	endif; ?>

	<!-- instructions when list is empty -->
	<p class="<?php echo $empty_class; ?>">Your To Do list is empty.</p>

	<!-- form to create a new item -->
	<form method="post" action="" id="new-todo">
		<label>
			<span>Add a new item:</span>
			<input type="text" name="new-item" />
		</label>
		<button type="submit">Add Item</button>
	</form><!-- #new-todo -->
</div><!-- .wrap -->

</body>
</html>