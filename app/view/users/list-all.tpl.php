<article class='article overflow'>
<h1><?=$title?></h1>
<hr/>
<?php if (!empty($users)) : ?>

<?php foreach ($users as $user) : ?>

<div class="overflow usersummery">
	<a href='<?=$this->url->create('users/id/' . $user->id)?>'><img src='<?=$user->gravatar . '?s=40'?>' class='left'></a> 
	<div class="left marginLeft">
		<p><a href='<?=$this->url->create('users/id/' . $user->id)?>' class='profileLink'><?=$user->name?></a></p>
		<p><?=$user->email?></p>
	</div>	
</div>
 
<?php endforeach; ?>


<?php else: ?>

<p class='alert'>Databasen är tom.</p>	

<?php endif; ?>

</article>