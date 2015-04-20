<p>
    <label for="helsingborg_meta_box_select">Välj nod att hämta listans data från: </label>
    <select name="_helsingborg_meta[list_select]" id="helsingborg_meta_box_select">
      <?php foreach($pages as $page) : ?>
        <option value="<?php echo $page->ID; ?>" <?php selected($selected, $page->ID); ?>><?php echo $page->post_title; ?></option>
      <?php endforeach; ?>
    </select>
</p>
