// wrap my js to contain the scope, also using jQuery shorthand
(function($) {

	/**
	 * Focus the new to do input for better user experience
	 * @return {void}
	 */
	function focus_new_todo(){
		$('input[name="new-item"]').focus();
	}

	/**
	 * Display an Error Message
	 * @param  {string} err The Error Message
	 * @return {void}
	 */
	function display_error( err ){
		var $error = $('#new-todo .error');
		// If there is no error element, lets create it
		if( ! $error.length ){
			$error = $('<div class="error" />');
			$('#new-todo').prepend( $error );
		} else {
			// if the element exists, let's make sure it's hidden before we fade it in
			$error.hide();
		}

		// set the html and fade in
		$error.html( err ).fadeIn( 'fast' );
	}

	/**
	 * Hide the Error Message
	 * @return {void}
	 */
	function hide_error(){
		var $error = $('#new-todo .error');
		// If we have an actual error element, hide it.
		if( $error.length )
			$error.hide();
	}

	/**
	 * Bind the Click events needed for each to do item
	 * This will bind the Remove functionality and the "tick" functionality
	 * @return {void}
	 */
	function bind_actions(){
		/**
		 * Bind the click event for the remove element. This will delete an item from the to do list.
		 */
		$('a.remove').off('click').on('click', function(e){
			// do not actually follow the link
			e.preventDefault();

			var $item = $(this).parents('li:first'),
				id = $item.find('input:checkbox').val();

			// call our ajax script
			$.ajax({
				url: 'ajax.php',
				data: { remove: id },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						// lets have it go away with dignity
						$item.fadeOut('fast', function(){
							// once it's gone, remove it for good
							$item.remove();
							// now that it's gone, let's see if there are any more.
							if( ! $('#todo-list li').length ){
								// toss the list if nothing is on it, display the empty message
								$('#todo-list').remove();
								$('.empty').removeClass('hidden');
							}
						});
					} else {
						display_error( response.error );
					}
					// re-focus our input
					focus_new_todo();
				}
			});
		});

		/**
		 * Bind the click event for the checkbox element. This will mark the item off our to do list
		 */
		$('input.tick').off('click').on('click', function(e){
			var $item = $(this).parents('li:first'),
				id = $(this).val(),
				action = $(this).is(':checked') ? 'done' : 'undone';

			// make the ajax call
			$.ajax({
				url: 'ajax.php',
				data: { tick: id, action: action },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						// if we checked it off, mark it done
						if( action == 'done' ){
							$item.addClass('done');
						} else {
							// otherwise unmark the item
							$item.removeClass('done');
						}
					} else {
						display_error( response.error );
					}
					// re-focus our input
					focus_new_todo();
				}
			});
		});
	}

	/**
	 * Bind the submit action for new to do item form
	 * @return {void}
	 */
	function bind_submit(){
		$('#new-todo').on('submit', function(e){
			// don't actually submit the form
			e.preventDefault();

			var $form = $(this),
				$new_item = $form.find('input[name="new-item"]');

			// if they didn't enter any text, give them an error and abort
			if( ! $new_item.val() ){
				display_error( 'Please enter a value.' );
				$new_item.focus();
				return false;
			}

			// make sure the error isn't still displaying
			hide_error();

			// add the item via ajax
			$.ajax({
				url: 'ajax.php',
				data: { item: $new_item.val() },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						// let's create all of our elements needed for the new item
						var $item = $('<li />'),
							$span = $('<span />').html( response.item ),
							$list = $('#todo-list'),
							$cbx = $('<input class="tick" type="checkbox" value="' + response.item_id + '" />'),
							$remove = $('<a href="#remove" class="remove" />').text('x');

						// let's make sure we have an actual list to put it in
						if( ! $list.length ){
							$list = $('<ol id="todo-list" />');
							$('.empty').before( $list ).addClass('hidden');
						}

						// now we build the item.
						$item.append( $cbx )
							.append( $span )
							.append( $remove );

						// and insert the item into the list
						$list.append( $item );

						// bind any click events needed
						bind_actions();

						// re-focus new to do item for better user experience
						$new_item.val('').blur().focus();
					} else {
						display_error( response.error );
					}
				}
			});
		});
	}

	// Document Ready
	$(function(){
		// Start out by focusing the new to do item element
		focus_new_todo();

		// bind any existing items
		bind_actions();

		// bind the form submit
		bind_submit();
	});

})(jQuery);
