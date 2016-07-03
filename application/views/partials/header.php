<div class="nav-bar">
	<a href='http://localhost/Connect4/arcade/index'>
		<h1 id="title" class="nav-bar-menu-item">
			Connect<span>4</span>
		</h1>
	</a>

	<div class="nav-bar-menu-item">
		Hello, <span><?= $user->firstName()?></span> !
	</div>
	<a class="nav-bar-menu-item">
		<?= anchor('account/updatePasswordForm','Change Password','class="nav-bar-menu-item"') ?>
	</a>
	<a class="nav-bar-menu-item">
		<?= anchor('account/logout','Logout', 'class="nav-bar-menu-item"') ?>
	</a>
</div>

<div class="push_down">
</div>