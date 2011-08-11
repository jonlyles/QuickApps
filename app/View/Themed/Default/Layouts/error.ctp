<html>
<head>
    <title><?php __t('Error'); ?></title>
    <?php echo $this->Html->css('reset.css'); ?>
    <?php echo $this->Html->css('styles.css'); ?>
    <style>
        div#center { width:600px; margin:50px auto 0 auto; background:#fff; padding:20px;}
    </style>
</head>

<body>
    <div id="center">
		<?php echo $content_for_layout; ?>
	</div>
</body>
</html>
