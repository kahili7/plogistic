<?= doctype('html4-trans') ?>
<html>
    <head>
	<title>PeriLogistic - Импорт</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?= link_tag('public/css/blueprint/src/reset.css'); ?>
	<?= link_tag('public/css/blueprint/src/forms.css'); ?>
	<?= link_tag('public/css/blueprint/src/liquid.css'); ?>
	<?= link_tag('public/css/blueprint/src/typography.css'); ?>
	<?= link_tag('public/css/modules/common.css'); ?>
	<?= link_tag('public/css/modules/upload.css'); ?>
	<?= link_tag('public/css/modules/paging.css'); ?>
	<?= link_tag('public/css/modules/findform.css'); ?>
	<?= link_tag('public/css/modules/editform.css'); ?>
	<?= link_tag('public/css/modules/jquery.jdMenu.css') ?>
	<?= link_tag('public/js/jquery.datapick.package-3.6.1/jquery.datepick.css') ?>

	<!--[if IE]>
	<?= link_tag('public/css/blueprint/ie.css'); ?>
	<![endif]-->

	<script type="text/javascript" src="<?= base_url() ?>public/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>public/js/jquery-ui-1.7.2.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>public/js/ajaxupload.js"></script>
	<script src="<?= base_url() ?>public/js/jquery.dimensions.js" type="text/javascript"></script>
	<script src="<?= base_url() ?>public/js/jquery.positionBy.js" type="text/javascript"></script>
	<script src="<?= base_url() ?>public/js/jquery.bgiframe.js" type="text/javascript"></script>
	<script src="<?= base_url() ?>public/js/jquery.jdMenu.js" type="text/javascript"></script>
	<script src="<?= base_url() ?>public/js/jquery.datapick.package-3.6.1/jquery.datepick.pack.js" type="text/javascript"></script>
	<script src="<?= base_url() ?>public/js/jquery.datapick.package-3.6.1/jquery.datepick-ru.js" type="text/javascript"></script>

	<script type="text/javascript">/*<![CDATA[*/
	    $(document).ready(function(){
		new AjaxUpload('upload-button', {
		    action: 'import/upload',
		    responseType: 'json',
		    onChange : function(file , ext) {
			if ( $('select').val() == 0 )
			{
			    alert("Пожайлуста выберете директорию для загрузки");
			    return false;
			}
		    },
		    onSubmit : function(file , ext) {
			if (ext && /^(<?= $this->config->item('acceptable_files'); ?>)$/i.test(ext))
			{
			    $('#upload-div .text').text('Загрузка... ' + file);
			    directory = $('select').val();
			    this.setData({'directory': directory});
			    $('#loader_overlay').show();
			    $('#loader').show();
			}
			else
			{
			    $('#upload-div .text').text('Ошибка: только текстовые файлы');
			    return false;
			}
		    },
		    onComplete : function(file, response) {
			$('#loader').hide();
			$('#loader_overlay').hide();

			if (response && /^(success)/i.test(response.status))
			{
			    $('#upload-div .text').html('Загружен ' + file);
			    $('#upload-button').html('Загрузка другого файла');
			    directory = $('select').val();
			    window.location = "import/load/"+directory;
			}
			else
			{
			    alert('Ошибка загрузки файла ('+file+')! \n'+ response.issue);
			    $('#upload-div .text').html('Загрузка не завершена');
			}
		    }
		});
	    });/*]]>*/
	</script>
    </head>
    <body>
	<?=$this->load->view('common/logoheader', null, true);?>
	<?=get_menu()?>
	<div class="container">
		<?=$content?>
	</div>
	<?=$this->load->view('common/footer', null, true);?>
    </body>
</html>