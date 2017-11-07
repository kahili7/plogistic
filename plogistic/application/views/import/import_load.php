<div align="center">
<fieldset style="width: 400px;">
<legend>Загрузка в БД <?=$dir?></legend>
<div id="upload-div">
<?
print form_open("import/db");
print form_hidden("directory", $dir);
print form_label("Выбрать файл: ", "filename");
print form_dropdown("filename", $files);
print form_checkbox("delete", "1", TRUE);
print form_label("Очистить базу", "delete");
?>
    <br>
<?
print form_submit("submit", "Загрузить в БД");
print form_close();
?>
</div>
</fieldset>
</div>