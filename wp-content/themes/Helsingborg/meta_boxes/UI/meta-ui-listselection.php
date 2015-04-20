<p>

<style>
  ul.position {width:20px;display:inline-block;vertical-align:top}
  ul.chosen {width:300px;display:inline-block;vertical-align:top;}
  ul.chosen li {background-color:#f1f1f1;margin:2px;padding-left:10px;margin-bottom:6px;}
  li.selected {background-color:#f1f1f1}
</style>

<input type="hidden" id="_helsingborg_meta[list_options]" name="_helsingborg_meta[list_options]" />

<label for="helsingborg_meta_box_select_options">Välj kolumner att lista: </label>
<select multiple="multiple" id="helsingborg_meta_box_select_options">
  <?php
  // List has been loaded in meta-functions.php
  foreach ($list as $key => $item) {
    echo('<option value="' . $key . '">' . $item . '</option>');
  } ?>
</select>

<?php
  //
  $selected_values = explode(',', $selected);
  $selected_texts  = array();
  foreach($selected_values as $value) {
    $selected_texts[] = $list[$value];
  }
  $selected_texts = implode(",",$selected_texts);
?>

<ul class="position"></ul>
<ul class="chosen"></ul>

<script>
jQuery(document).ready( function() {
  var options = $('#helsingborg_meta_box_select_options option');
  var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
  arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
  options.each(function(i, o) {
    o.value = arr[i].v;
    $(o).text(arr[i].t);
  });
});
</script>
<script>
        var $ =jQuery.noConflict();
        var selectedValues = [<?php echo $selected; ?>];
        $(function() {
          $("#helsingborg_meta_box_select_options").multipleSelect({
              selectAll: false,
              multiple: true,
              multipleWidth: 250,
              width: '100%',
              onOpen: function() {
                $("#helsingborg_meta_box_select_options").multipleSelect("setSelects", selectedValues);
              },
              onClose: function() {
                document.getElementById('_helsingborg_meta[list_options]').value = $("#helsingborg_meta_box_select_options").multipleSelect("getSelects");
              },
              onClick: function() {

                selectedValues = $("#helsingborg_meta_box_select_options").multipleSelect("getSelects");
                var selectedTexts = $("#helsingborg_meta_box_select_options").multipleSelect("getSelects", "text");
                $('.chosen').empty();
                $('.position').empty();
                for(var i=0;i<selectedValues.length;i++) {
                  var item = '<li id="' + selectedValues[i] + '">' + selectedTexts[i] + '</li>';
                  var position = '<li>' + (i+1) + ':</li>';
                  $(position).appendTo($('.position'));
                  $(item).appendTo($('.chosen'));
                }

                document.getElementById('_helsingborg_meta[list_options]').value = $("#helsingborg_meta_box_select_options").multipleSelect("getSelects");
              }
          });

          $(".chosen").sortable({
              connectWith: "ul",
              start: function(e, info) {
                  info.item.siblings(".selected").appendTo(info.item);
              },
              stop: function(e, info) {
                  info.item.after(info.item.find("li"));

                  var values = $('.chosen li').map(function(i,n) {
                    return $(n).attr('id');
                  }).get().join(',');

                  document.getElementById('_helsingborg_meta[list_options]').value = values;
              }
          });

          var selectedValuesRaw = <?php echo json_encode($selected); ?>;
          var selectedTextsRaw = <?php echo json_encode($selected_texts); ?>;
          var selectedValues = selectedValuesRaw.split(",");
          var selectedTexts = selectedTextsRaw.split(",");
          for(var i=0;i<selectedValues.length;i++) {
            var item = '<li id="' + selectedValues[i] + '">' + selectedTexts[i] + '</li>';
            var position = '<li>' + (i+1) + ':</li>';
            $(position).appendTo($('.position'));
            $(item).appendTo($('.chosen'));
          }

          document.getElementById('_helsingborg_meta[list_options]').value = "<?php echo $selected; ?>";
        });
</script>
</p>
