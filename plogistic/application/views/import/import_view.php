<div id="loader_overlay" class="loader_overlay_BG"></div>
<div id="loader" class="loading">Загрузка</div>

<div align="center">
<fieldset style="width: 400px;">
<legend>Загрузка файла в директорию</legend>
<div id="upload-div">
    <form method="post" action="#">
	<label for="dir_selector">Выбрать директорию:</label>
	<select name="dir_selector" id="dir_selector">
	    <option value="0"> - Выбрать - </option>
	    <option value="warehouse">Склады</option>
	    <option value="groupsystem">Группы продуктов</option>
	    <option value="client">Клиенты</option>
	    <option value="art_nr">Артикулы</option>
	    <option value="element">Элементы</option>
	</select>
    </form>
    <a href="#" id="upload-button">Загрузить файл</a>
    <p class="text"></p>
</div>
</fieldset>
</div>

<script type="text/javascript">
    $(document).ready(
    function() {
	$('#loader_overlay').hide();
	$('#loader').hide();
    }
);
</script>