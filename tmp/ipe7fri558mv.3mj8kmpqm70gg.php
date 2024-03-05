<h2>Your account</h2>
<form method=post action="<?= ($BASE) ?>/logout">
	<input type="submit" name="submit" value="Logout">
</form>
<div class="account-page">
	<h3>Your notes</h3>
	<ul class="notes-container">
		<?php foreach (($notes?:[]) as $note): ?>
			<li class="note-container" id="note<?= ($note['id']) ?>">
				<div class="note-title">
					<p>Location: <?= ($note['thirdplace_name']) ?></p>
				</div>
				<div class="note-box">
					<p><?= ($note['reason']) ?></p>
					<button class="submit-btn delete-btn" name="<?= ($note['id']) ?>" data-note-id="<?= ($note['id']) ?>">Delete</button>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<script>
	var baseUrl = 'https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject';
	$('.delete-btn').on('click', function()
	{
		console.log("clicked");
		var noteId = $(this).data('noteId');
		$.ajax({
			url: baseUrl + '/notes/' + noteId,
			type: 'DELETE',
			success: function(response) {
				console.log("deleting note:" + noteId);
				console.log(response.success);
				$('#note' + noteId).remove();
			},
			error: function(jqXHR){
				console.log(jqXHR.responseText);
				var error = JSON.parse(jqXHR.responseText);
				alert('Error deleting note: ' + error.error);
			}
		})
	})
</script>