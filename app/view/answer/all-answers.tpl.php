<?php if(!empty($answers)) : ?>
<h3><?=$title?></h3>
<hr />
<?php foreach ($answers as $answer) : ?>	
<article class='articleAnswer overflow'>
<p class='left'><a href='<?=$this->url->create('users/id/' . $answer->user_id)?>' class='profileLink'><?=$answer->answer_author?></a> svarade <?=$answer->created?></p>

<?php if(isset($_SESSION['user']) && $_SESSION['user']->id === $answer->user_id) : ?>	
	<a href='<?=$this->url->create('answers/delete/' . $answer->id)?>' class='deleteButton right'>x</a>
<?php endif; ?>

<hr class='clearBoth' />
<?=$answer->answer_content?>
	
	<?php if(isset($_SESSION['user']) && $_SESSION['user']->id === $answer->user_id) : ?>	
		<p class='questionSubTitle'><a href='<?=$this->url->create('answers/edit/' . $answer->id)?>' class='profileLink'>Redigera inlägg</a></p>
	<?php endif; ?>

	<?php if(!empty($answer->comments)) : ?>

		<h4 class='dashed'>Kommenterer:</h4>
		
		<?php foreach ($answer->comments as $comment) : ?>
		<div class='marginTop'>
			<p class='questionSubTitle'><a href='<?=$this->url->create('users/id/' . $comment->user_id)?>' class='profileLink'><?=$comment->comment_author?></a> | <?=$comment->created?></p> 
			<hr />
			<?=$comment->comment_content?>
		</div>
		<?php endforeach; ?>

	<?php endif; ?>

</article>

<?php if(isset($_SESSION['user'])) : ?>
<div class='commentButtonWrapper overflow'>
	<a href='<?=$this->url->create('comments/apost/' . $answer->id)?>'  class='button green small marginRight right'>Kommentera</a>
</div>
<?php endif; ?>	

<?php endforeach; ?>
<?php endif; ?>	