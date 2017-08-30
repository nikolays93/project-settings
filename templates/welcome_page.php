<p>
  <a href="?page=<?php echo $_REQUEST['page']; ?>&do=add" class="button button-primary alignright">Создать новый тип записей</a>

  Здесь вы можете создать новый тип записи или изменить вид уже зарегистрированного типа <br> и\или скрыть не реализованный функционал CMS WordPress из меню.
</p>

<?php
$types = PSettings\DTSettings::$post_types;
if(sizeof( $types ) < 1)
  return;

$table = new PSettings\Post_Types_List_Table();
foreach ($types as $id => $type) {
  $table->set_type( $id, $type['labels']['singular_name'], $type['labels']['name'] );
}

$table->prepare_items();
$table->display();