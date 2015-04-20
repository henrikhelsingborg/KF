<div class="helsingborg_meta_control">
  <?php if (empty($selected_name)) { $selected_name = '-- Ingen sida vald --'; }?>
  <label for="helsingborg_meta_box_current"><b>Vald sida:</b></label>
  <input type="text" name="_helsingborg_meta[rss_select_name]" id="_helsingborg_meta[rss_select_name]" value="<?php echo $selected_name; ?>" style="width: 60%;" readonly>
  <input type="hidden" name="_helsingborg_meta[rss_select_id]" id="_helsingborg_meta[rss_select_id]" value="<?php echo $selected_id; ?>"><br><br>

  <label for="helsingborg_meta_box_select"><b>Sök efter sida att hämta RSS från:</b></label><br>
  <input style="width: 70%;" id="helsingborg_meta_box_search" type="text" class="input-text" />
  <button style="width: 25%;"
          type="button"
          class="button-secondary"
          onclick="load_pages_rss()">
          Sök
  </button>

  <p>
    <div id="helsingborg_meta_box_select" style="display: none;">
    </div>
  </p>

  <script>
  function load_pages_rss() {
    document.getElementById('helsingborg_meta_box_select').style.display = "block";
    var data = {
      action: 'load_pages_rss',
      title: document.getElementById('helsingborg_meta_box_search').value
    };

    jQuery.post(ajaxurl, data, function(response) {
      document.getElementById('helsingborg_meta_box_select').innerHTML = response;
    });
  };

  function updateValues() {
    var id = document.getElementById('rss_select').value;
    if (id > 0) {
      var name = jQuery('#rss_select option:selected').text();
      document.getElementById('_helsingborg_meta[rss_select_name]').value = name;
      document.getElementById('_helsingborg_meta[rss_select_id]').value = id;
    } else {
      document.getElementById('_helsingborg_meta[rss_select_name]').value = '-- Ingen sida vald --';
      document.getElementById('_helsingborg_meta[rss_select_id]').value = '';
    }
  }
  </script>
</div>
