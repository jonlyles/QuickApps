<!-- default error layout -->
<html>
<head>
    <title><?php __t('Error'); ?></title>
    <?php echo $this->Html->css('reset.css'); ?>
    <?php echo $this->Html->css('error.css'); ?>
</head>

<body>
    <div id="error-container">
		<?php echo $content_for_layout; ?>
	</div>
</body>
</html>
