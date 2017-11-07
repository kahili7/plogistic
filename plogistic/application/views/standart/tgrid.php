<div class="grid">
<h3><?=$title?></h3>

<div id="finder"></div>
<script type="text/javascript" charset="utf-8">
	$().ready(function() {
		$('#finder').elfinder({
			url: '<?=base_url()?>application/libraries/finder/connect.php',
			lang: 'ru'
		});
	});
</script>
</div>