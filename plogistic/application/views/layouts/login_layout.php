<?=doctype('html4-trans')?>
<html>
	<head>
		<title><?=$page_title?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<?=link_tag('public/css/blueprint/reset.css');?>
		<?=link_tag('public/css/blueprint/src/forms.css');?>
		<?=link_tag('public/css/blueprint/src/liquid.css');?>
		<?=link_tag('public/css/blueprint/typography.css');?>		
		<?=link_tag('public/css/modules/login.css');?>
		<?=link_tag('public/css/modules/common.css');?>
		<?=link_tag('public/css/blueprint/screen.css');?>
		<!--[if IE]>
			<?=link_tag('public/css/blueprint/ie.css');?>
		<![endif]-->
	</head>
	<body>
		<?=$this->load->view('common/logoheader_login.php', null, true);?>
		<?=$content;?>
		<?=$this->load->view('common/footer.php', null, true);?>
	</body>
</html>