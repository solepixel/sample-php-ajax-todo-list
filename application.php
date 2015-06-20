<?php

# Start a new session if one doesn't already exist
if( ! headers_sent() && ! isset( $_SESSION ) ){
	session_start();
}

/**
 * Create a new PHP Data Object in the global space
 */
global $db;
$db = new PDO(
    'mysql:host=localhost;dbname=ags_todo;charset=utf8', // host, dbname, and charset
    'root', // username
    'root'  // password
);

/**
 * Fetch To Do items from todo_items table
 * @return mixed Array of Item Objects or false
 */
function get_todo_items(){
	global $db;

	$items = false;

	try {
		$query = $db->query( 'SELECT * FROM todo_items WHERE `deleted` IS NULL ORDER BY `added` ASC' );
		if( $query ){
			$items = $query->fetchAll( PDO::FETCH_OBJ );
		}
	} catch( PDOException $e ){
		echo $e->getMessage();
		exit();
	}

	if( $items )
		return $items;

	return false;
}

/**
 * Display an ordered list of the To Do items
 * @return void
 */
function output_todo_list(){
	echo '<ol id="todo-list">';
		foreach( get_todo_items() as $item ): ?>
			<li<?php if( $item->done ) echo ' class="done"'; ?>>
				<input class="tick" type="checkbox" value="<?php echo $item->id; ?>" <?php if( $item->done ) echo ' checked="checked"'; ?> />
				<span><?php echo $item->item; ?></span>
				<a href="#remove" class="remove">x</a></li>
		<?php endforeach;
	echo '</ol>';
}
