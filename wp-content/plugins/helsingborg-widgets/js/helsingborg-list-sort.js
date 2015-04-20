jQuery(document).ready(function($) {
  if (!$('body').hasClass('widgets_access')) {
    helsingborgSetupList($);
    $('.hbgllw-edit-item').addClass('toggled-off');
    helsingborgSetupHandlers($);
  }

  $(document).ajaxSuccess(function() {
    helsingborgSetupList($);
    $('.hbgllw-edit-item').addClass('toggled-off');
  });
});

function helsingborgSetupList($) {
  $(".helsingborg-link-list").sortable({
    items: '.list-item',
    opacity: 0.6,
    cursor: 'n-resize',
    axis: 'y',
    handle: '.moving-handle',
    placeholder: 'sortable-placeholder',
    start: function(event, ui) {
      ui.placeholder.height(ui.helper.height());
    },
    update: function() {
      updateOrder($(this));
    }
  });

  $(".helsingborg-link-list .moving-handle").disableSelection();
}

// All Event handlers
function helsingborgSetupHandlers($) {
  $("body").on('click.hbgllw', '.hbgllw-delete', function() {
    $(this).parent().parent().fadeOut(500, function() {
      var hbgllw = $(this).parents(".widget-content");
      $(this).remove();
      hbgllw.find('.order').val(hbgllw.find('.helsingborg-link-list')
        .sortable('toArray'));
      var num = hbgllw.find(".helsingborg-link-list .list-item").length;
      var amount = hbgllw.find(".amount");
      amount.val(num);
    });
  });

  $("body").on('click.hbgllw', '.hbgllw-add', function() {
    var hbgllw = $(this).parent().parent();
    var num = hbgllw.find('.helsingborg-link-list .list-item').length + 1;

    hbgllw.find('.amount').val(num);

    var item = hbgllw.find('.helsingborg-link-list .list-item:last-child')
      .clone();
    var item_id = item.attr('id');
    item.attr('id', increment_last_num(item_id));

    $('.toggled-off', item).removeClass('toggled-off');
    $('.number', item).html(num);
    $('.item-title', item).html('');

    $('div', item).each(function() {
      var id_val = $(this).attr('id');
      if (id_val !== undefined) {
        $(this).attr('id', increment_last_num(id_val));
      }
    });

    $('label', item).each(function() {
      var for_val = $(this).attr('for');
      $(this).attr('for', increment_last_num(for_val));
    });

    $('button', item).each(function() {
      var id_val = $(this).attr('id');
      var name_val = $(this).attr('name');
      var onclick_val = $(this).attr('onclick');
      $(this).attr('id', increment_last_num(id_val));
      $(this).attr('name', increment_last_num(name_val));
      $(this).attr('onclick', increment_last_num(onclick_val));
    });

    $('input', item).each(function() {
      var id_val = $(this).attr('id');
      var name_val = $(this).attr('name');

      $(this).attr('id', increment_last_num(id_val));

      if (name_val !== undefined) {
        $(this).attr('name', increment_last_num(name_val));
      }

      var value = $(this).attr('onclick');
      if (value !== undefined) {
        value = value.replace(/\d\u0027+/g, num + "'");
        $(this).attr('onclick', value);
      }

      if ($(':checked', this)) {
        $(this).removeAttr('checked');
      }

      if (!$(this).is(':checkbox') && !$(this).is(':radio')) {
        if (value === undefined) {
          $(this).val('');
        } else {
          $(this).val('Välj bild');
        }
      }
    });

    $('textarea', item).each(function () {
      var id_val = $(this).attr('id');
      var name_val = $(this).attr('name');

      $(this).attr('id', increment_last_num(id_val));

      if (name_val !== undefined) {
        $(this).attr('name', increment_last_num(name_val));
      }

      $(this).val('');
    });

    $('select', item).each(function() {
      $(this).remove();
    });

    $('img', item).each(function() {
      $(this).remove();
    });

    hbgllw.find('.helsingborg-link-list').append(item);
    hbgllw.find('.order').val(hbgllw.find('.helsingborg-link-list').sortable(
      'toArray'));
  });

  $('body').on('click.hbgllw', '.moving-handle', function() {
    $(this).parent().find('.hbgllw-edit-item').slideToggle(200);
  });
}

function increment_last_num(v) {
  return v.replace(/[0-9]+(?!.*[0-9])/, function(match) {
    return parseInt(match, 10) + 1;
  });
}

function updateOrder(self) {
  var hbgllw = self.parents(".widget-content");
  hbgllw.find('.order').val(hbgllw.find('.helsingborg-link-list').sortable(
    'toArray'));
}

function update_list_item_cells(id, num) {
  var selected = document.getElementById('select_' + id + num).value;
  jQuery.post(ajaxurl, {
    action: 'load_page_with_id',
    id: selected
  }, function(response) {
    if (response != '') {
      var values = response.split('|');
      if (document.getElementById(id + num) !== null) {
        document.getElementById(id + num).value = values[0];
      }

      if (document.getElementById(id + '_link' + num) !== null) {
        document.getElementById(id + '_link' + num).value = values[1];
      }

      if (document.getElementById(id + '_id' + num) !== null) {
        document.getElementById(id + '_id' + num).value = selected;
      }
    }
  });
};

function load_pages_with_update(id, num, update) {
  document.getElementById(id + '_select' + num).style.display = "block";
  document.getElementById(id + '_select' + num).innerHTML = "";
  var data = {
    action: 'load_pages_with_update',
    id: id,
    num: num,
    update: update,
    title: document.getElementById(id + '_search' + num).value
  };

  jQuery.post(ajaxurl, data, function(response) {
    document.getElementById(id + '_select' + num).innerHTML = response;
  });
};

function load_page_containing(from, name) {
  var id = from.replace('button_', '');
  document.getElementById('select_' + id).style.display = "block";
  document.getElementById('select_' + id).innerHTML = "";

  var data = {
    action: 'load_pages',
    id: id,
    name: name,
    title: document.getElementById('input_' + id).value
  };

  jQuery.post(ajaxurl, data, function(response) {
    document.getElementById('select_' + id).innerHTML = response;
  });

};
