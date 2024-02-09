<h2>Your account</h2>
<form method=post action="<?= ($BASE) ?>/logout">
	<input type="submit" name="submit" value="Logout">
</form>
<div class="account-page">
	<h3>Your notes</h3>
	<ul class="notes-container">
		<?php foreach (($notes?:[]) as $note): ?>
			<li class="note-container">
				<div class="note-title">
					<p>Location: <?= ($note['thirdplace_name']) ?></p>
				</div>
				<div class="note-box">
					<p><?= ($note['reason']) ?></p>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>