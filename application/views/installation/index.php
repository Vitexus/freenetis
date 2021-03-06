<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo  $title ?> | <?php echo $this->settings->get('title') ?></title>
<?php echo html::link('media/images/favicon.ico', 'shorcut icon', 'image/x-icon', FALSE); ?>
<?php echo html::stylesheet('media/css/installation.css') ?>
<?php echo html::stylesheet('media/css/jquery-ui.css') ?>
<?php echo html::stylesheet('media/css/jquery.autocomplete.css') ?>
<?php echo html::stylesheet('media/css/jquery.validate.password.css') ?>
<?php echo html::script('media/js/jquery.min', FALSE) ?>
<?php echo html::script('media/js/jquery-ui.min', FALSE) ?>
<?php echo html::script('media/js/jquery.min', FALSE) ?>
<?php echo html::script('media/js/jquery-ui.min', FALSE) ?>
<?php echo html::script('media/js/jquery.autocomplete.min', FALSE) ?>
<?php echo html::script('media/js/jquery.validate.min', FALSE) ?>
<script type="text/javascript"><!--
	// settings for jquery.validate.password
	var security_password_level = <?php echo Settings::get('security_password_level') ?>;
	var security_password_length = <?php echo Settings::get('security_password_length') ?>;
//--></script>
<?php echo html::script('media/js/jquery.validate.password', FALSE) ?>
<?php echo html::script('media/js/messages_cs', FALSE) ?>
<?php echo html::script('media/js/php.min', FALSE) ?>
<script type="text/javascript"><!--
	
    $(document).ready(function()
	{
		$.validator.passwordRating.messages = {
			"too-short": "<?php echo __('Too short') ?>",
			"very-weak": "<?php echo __('Very weak') ?>",
			"weak": "<?php echo __('Weak') ?>",
			"good": "<?php echo __('Good') ?>",
			"strong": "<?php echo __('Strong') ?>"
		}

		$('form').validate();
		$('#inst_done').hide();

		$('form').submit(function ()
		{
			if ($(this).valid())
			{
				$('#inst_done').show();
				$(this).hide();
				$('p.info_text').hide();
			}
		});

	});
	
//--></script>
</head>

<body>

<div id="main">
	<h1><span><?php echo $this->settings->get('title') ?></span><i><?php echo Version::get_version() ?></i></h1>
	<div id="loading-overlay" style="display: none"></div>
	<div class="flags"><?php echo  special::create_language_flags(array('cs' => 'Česky', 'en' => 'English')) ?></div>
	<div id="main-padd">
	    <div id="content">
		<h2><?php echo $title ?></h2><br />
		<p class="info_text"><?php echo __('Welcome to FreenetIS installation.'); ?>
			<?php echo __('Please fill in the form with information about your association.'); ?>
		</p>
		<br />
		<?php echo  $form ?>
		<img src="<?php echo url::base() ?>media/images/icons/animations/ajax-loader-big.gif" alt="Installed" id="inst_done" />
	    </div>
		<div class="clear"></div>
	</div>
</div><br />

</body>
</html>
