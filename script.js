(function($) {

	function display_error( err ){
		var $error = $('#new-todo .error');
		if( ! $error.length ){
			$error = $('<div class="error" />');
			$('#new-todo').prepend( $error );
		} else {
			$error.hide();
		}

		$error.html( err ).fadeIn( 'fast' );
	}

	function hide_error(){
		var $error = $('#new-todo .error');
		if( $error.length )
			$error.hide();
	}

	function bind_actions(){
		$('a.remove').off('click').on('click', function(e){
			e.preventDefault();
			var $item = $(this).parents('li:first'),
				id = $item.find('input:checkbox').val();

			$.ajax({
				url: 'ajax.php',
				data: { remove: id },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						$item.fadeOut('fast', function(){
							$item.remove();
							if( ! $('#todo-list li').length ){
								$('#todo-list').remove();
								$('.empty').removeClass('hidden');
							}
						});
					} else {
						display_error( response.error );
					}
					$('input[name="new-item"]').focus();
				}
			});
		});

		$('input.tick').off('click').on('click', function(e){
			var $item = $(this).parents('li:first'),
				id = $(this).val(),
				action = $(this).is(':checked') ? 'done' : 'undone';

			$.ajax({
				url: 'ajax.php',
				data: { tick: id, action: action },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						if( action == 'done' ){
							$item.addClass('done');
						} else {
							$item.removeClass('done');
						}
					} else {
						display_error( response.error );
					}
					$('input[name="new-item"]').focus();
				}
			});
		});
	}

	// Document Ready
	$(function(){
		$('input[name="new-item"]').focus();

		bind_actions();

		$('#new-todo').on('submit', function(e){
			e.preventDefault();
			var $form = $(this),
				$new_item = $form.find('input[name="new-item"]');

			if( ! $new_item.val() ){
				display_error( 'Please enter a value.' );
				$new_item.focus();
				return false;
			}

			hide_error();

			$.ajax({
				url: 'ajax.php',
				data: { item: $new_item.val() },
				type: 'post',
				dataType: 'json',
				success: function( response ){
					if( response.success ){
						var $item = $('<li />'),
							$span = $('<span />').html( response.item ),
							$list = $('#todo-list'),
							$cbx = $('<input class="tick" type="checkbox" value="' + response.item_id + '" />'),
							$remove = $('<a href="#remove" class="remove" />').text('x');

						if( ! $list.length ){
							$list = $('<ol id="todo-list" />');
							$('.empty').before( $list ).addClass('hidden');
						}

						$item.append( $cbx )
							.append( $span )
							.append( $remove );

						$list.append( $item );
						$new_item.val('').blur().focus();

						bind_actions();
					} else {
						display_error( response.error );
					}
				}
			});
		});
	});
})(jQuery);
