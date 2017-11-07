<?=doctype('html4-trans')?>
<html>
<head>
    <title>PeriLogistic</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?=link_tag('public/css/blueprint/src/reset.css');?>
	<?=link_tag('public/css/blueprint/src/forms.css');?>
	<?=link_tag('public/css/blueprint/src/liquid.css');?>
	<?=link_tag('public/css/blueprint/src/typography.css');?>
	<?=link_tag('public/css/modules/common.css');?>
	<?=link_tag('public/css/modules/jquery.jdMenu.css')?>
	<!--[if IE]>
		<?=link_tag('public/css/blueprint/ie.css');?>
	<![endif]-->
	<?=link_tag('public/css/modules/fullcalendar.css')?>
	
	<script type="text/javascript" src="<?=base_url()?>public/js/jquery-1.3.2.min.js"></script>
	<script src="<?=base_url()?>public/js/jquery.dimensions.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.positionBy.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.bgiframe.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.jdMenu.js" type="text/javascript"></script>

	<script type='text/javascript' src='<?=base_url()?>public/js/fullcal/ui.core.js'></script>
	<script type='text/javascript' src='<?=base_url()?>public/js/fullcal/ui.draggable.js'></script>
	<script type='text/javascript' src='<?=base_url()?>public/js/fullcal/ui.resizable.js'></script>
	<script type='text/javascript' src='<?=base_url()?>public/js/fullcal/fullcalendar.min.js'></script>
<style type='text/css'>
#calendar {
	width: 900px;
	margin: 0 auto;
}
	
#loading {
	position: absolute;
	top: 5px;
	right: 5px;
}
</style>

</head>
<body>
	<?=$this->load->view('common/logoheader', null, true);?>
	<?=get_menu()?>
	<div id='loading' style='display:none'>Загрузка...</div>
	<div id='calendar'></div>
	<?=$this->load->view('common/footer', null, true);?>
</body>
</html>