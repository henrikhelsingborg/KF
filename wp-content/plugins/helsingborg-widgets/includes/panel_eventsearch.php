<?php
$table = new Helsingborg_Event_Search_Table();
$table->prepare_items();
?>
<div class="wrap">
  <div id="icon-users" class="icon32"><br/></div>
    <h2>Evenemangssök</h2><br>
    <form id="search-field" method="get">
      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
      Sök evenemang: <br><input style="width: 60%;" name="searchterm" id="event_search" type="text" class="input-text" />
      <input style="width: 15%;" type="submit" value="Sök" class="button-secondary"/><br>
    </form>

    <form id="search-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
          $table->display();
        ?>
    </form>
</div>
