<?php

if( ! headers_sent() && ! $_SESSION ){
	session_start();
}

global $db;
$db = new PDO(
    'mysql:host=localhost;dbname=ags_todo;charset=utf8',
    'root',
    'root'
);

function get_todo_items(){
	global $db;

	$items = false;

	try {
		$query = $db->query( 'SELECT * FROM todo_items WHERE `deleted` IS NULL ORDER BY `added` ASC' );
		$items = $query->fetchAll( PDO::FETCH_OBJ );
	} catch( PDOException $e ){
		echo $e->getMessage();
		exit();
	}

	if( $items )
		return $items;

	return false;
}

function output_todo_list(){
	echo '<ul id="todo-list">';
		foreach( get_todo_items() as $item ): ?>
			<li<?php if( $item->done ) echo ' class="done"'; ?>>
				<input class="tick" type="checkbox" value="<?php echo $item->id; ?>" <?php if( $item->done ) echo ' checked="checked"'; ?> />
				<span><?php echo $item->item; ?></span>
				<a href="#remove" class="remove">x</a></li>
		<?php endforeach;
	echo '</ul>';
}
