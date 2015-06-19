<?php
include_once( 'application.php' );

function ajax_insert_item(){
	if( isset( $_POST['item'] ) ):
		global $db;

		$response = array(
			'success' => false
		);

		$response['item'] = $_POST['item'];

		$data = array(
			$response['item'],
			date( 'Y-m-d H:i:s' )
		);

		try {
			$insert = $db->prepare('INSERT INTO todo_items ( `item`, `added` ) values ( ?, ? )');
			$insert->execute( $data );
			$response['success'] = true;
			$response['item_id'] = $db->lastInsertId();
		} catch ( PDOException $e ){
			$response['error'] = $e->getMessage();
			$response['success'] = false;
		}

		echo json_encode( $response );
		exit();

	endif;
}

function ajax_remove_item(){
	if( isset( $_POST['remove'] ) ):
		global $db;

		$response = array(
			'success' => false
		);

		$item_id = (int) $_POST['remove'];

		$data = array(
			date( 'Y-m-d H:i:s' ),
			$item_id
		);

		try {
			$delete = $db->prepare('UPDATE todo_items SET `deleted` = ? WHERE `id` = ?');
			$delete->execute( $data );
			$response['success'] = true;
		} catch ( PDOException $e ){
			$response['error'] = $e->getMessage();
			$response['success'] = false;
		}

		echo json_encode( $response );
		exit();

	endif;
}

function ajax_tick_item(){
	if( isset( $_POST['tick'] ) ):
		global $db;

		$response = array(
			'success' => false
		);

		$item_id = (int) $_POST['tick'];
		$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

		if( ! $action ){
			echo json_encode( $response );
		}

		if( $action == 'done' ){
			$data = array(
				date( 'Y-m-d H:i:s' ),
				$item_id
			);

			try {
				$tick = $db->prepare('UPDATE todo_items SET `done` = ? WHERE `id` = ?');
				$tick->execute( $data );
				$response['success'] = true;
			} catch ( PDOException $e ){
				$response['error'] = $e->getMessage();
				$response['success'] = false;
			}
		} else {
			$data = array(
				$item_id
			);

			try {
				$untick = $db->prepare('UPDATE todo_items SET `done` = NULL WHERE `id` = ?');
				$untick->execute( $data );
				$response['success'] = true;
			} catch ( PDOException $e ){
				$response['error'] = $e->getMessage();
				$response['success'] = false;
			}
		}

		echo json_encode( $response );
		exit();

	endif;
}

ajax_insert_item();
ajax_remove_item();
ajax_tick_item();

die( 'Script ended prematurely.' );
