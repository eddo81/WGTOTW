<div class='comment-form'>
	<?=$content?>
</div>

<?php if(isset($message)) : ?>
	<div class='<?=$message['class']?>'>
		<p><?=$message['text']?></p>
	</div>
<?php endif; ?>
