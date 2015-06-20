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

/**
 * Creates a new To Do Item
 * @return JSON
 */
function ajax_insert_item(){
	if( isset( $_POST['item'] ) ):
		global $db;

		// setup response
		$response = array(
			'success' => false
		);

		// setup query vars
		$response['item'] = htmlspecialchars( $_POST['item'] );
		$data = array( $response['item'] );

		// Insert into my database table
		try {
			$insert = $db->prepare( 'INSERT INTO todo_items ( `item`, `added` ) values ( ?, NOW() )' );
			$insert->execute( $data );
			$response['success'] = true;
			$response['item_id'] = $db->lastInsertId();
		} catch ( PDOException $e ){
			$response['error'] = $e->getMessage();
			$response['success'] = false;
		}

		// send the response back
		echo json_encode( $response );
		exit();

	endif;
}

/**
 * Remove a To Do item
 * @return JSON
 */
function ajax_remove_item(){
	if( isset( $_POST['remove'] ) ):
		global $db;

		// setup response
		$response = array(
			'success' => false
		);

		// setup query vars
		$item_id = (int) $_POST['remove'];
		$data = array( $item_id );

		// mark the item as deleted
		try {
			$delete = $db->prepare( 'UPDATE todo_items SET `deleted` = NOW() WHERE `id` = ?' );
			$delete->execute( $data );
			$response['success'] = true;
		} catch ( PDOException $e ){
			$response['error'] = $e->getMessage();
			$response['success'] = false;
		}

		// send the response back
		echo json_encode( $response );
		exit();

	endif;
}

/**
 * Mark a To Do item as done or unmark it
 * @return JSON
 */
function ajax_tick_item(){
	if( isset( $_POST['tick'] ) ):
		global $db;

		// setup response
		$response = array(
			'success' => false
		);

		$action = isset( $_POST['action'] ) ? htmlspecialchars( $_POST['action'] ) : '';

		if( ! $action ){
			// bail early, no action
			echo json_encode( $response );
			exit();
		}

		// setup query vars
		$done = $action == 'done' ? 'NOW()' : 'NULL';
		$item_id = (int) $_POST['tick'];
		$data = array( $item_id );

		try {
			$tick = $db->prepare( 'UPDATE todo_items SET `done` = ' . $done . ' WHERE `id` = ?' );
			$tick->execute( $data );
			$response['success'] = true;
		} catch ( PDOException $e ){
			$response['error'] = $e->getMessage();
			$response['success'] = false;
		}

		// send the response back
		echo json_encode( $response );
		exit();

	endif;
}
