<?php
$listTable   = new Helsingborg_Event_List_Table();
$listTable->prepare_items();
?>

<div class="wrap">
  <div id="icon-users" class="icon32"><br/></div>
    <h2>Evenemangshantering</h2>
    <form id="events-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
        $listTable->display();
        ?>
    </form>
</div>
