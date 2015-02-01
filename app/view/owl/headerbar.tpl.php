<nav class='headerbarnav'>
		
	<?php if(isset($_SESSION['user'])) : ?>	
		<a href='<?=$this->url->create('users/logout')?>' class='right'>Logga ut</a>
		<a href='<?=$this->url->create('users/edit/' . $_SESSION['user']->id)?>' class='right'>Redigera profil</a>
		<a href='<?=$this->url->create('users/id/' . $_SESSION['user']->id)?>' class='left nopaddingleft'>Visa profil</a>
	<?php else : ?>
		<a href='<?=$this->url->create('users/login')?>' class='right'>Logga in</a>
		<a href='<?=$this->url->create('users/add')?>' class='right'>Registrera</a>
	<?php endif; ?>

</nav>
	
