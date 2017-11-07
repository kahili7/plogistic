<?=doctype('html4-trans')?>
<html>
<head>
    <title>PeriLogistic - <?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?=link_tag('public/css/blueprint/src/reset.css');?>
	<?=link_tag('public/css/blueprint/src/forms.css');?>
	<?=link_tag('public/css/blueprint/src/liquid.css');?>
	<?=link_tag('public/css/blueprint/src/typography.css');?>
	<?=link_tag('public/css/modules/common.css');?>
	<?=link_tag('public/css/modules/grid.css');?>
	<?=link_tag('public/css/modules/paging.css');?>
	<?=link_tag('public/css/modules/findform.css');?>
	<?=link_tag('public/css/modules/editform.css');?>
	<?=link_tag('public/css/modules/jquery.jdMenu.css')?>
	<?=link_tag('public/js/jquery.datapick.package-3.6.1/jquery.datepick.css')?>
	
	<!--[if IE]>
		<?=link_tag('public/css/blueprint/ie.css');?>
	<![endif]-->
	
	<script type="text/javascript" src="<?=base_url()?>public/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>public/js/jquery-ui-1.7.2.js"></script>
	<script src="<?=base_url()?>public/js/jquery.dimensions.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.positionBy.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.bgiframe.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.jdMenu.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.datapick.package-3.6.1/jquery.datepick.pack.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.datapick.package-3.6.1/jquery.datepick-ru.js" type="text/javascript"></script>
	
</head>
<body>
	<?=$this->load->view('common/logoheader', null, true);?>
	<?=get_menu()?>
	<div class="container">
		<div class="column span-19 last">
			<?=$content?>
		</div>
	</div>
	<?=$this->load->view('common/footer', null, true);?>
</body>
</html>