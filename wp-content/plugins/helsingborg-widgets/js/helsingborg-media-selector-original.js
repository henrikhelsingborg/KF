jQuery(document).ready(function() {

	helsingborgMediaSelector = {

		create: function(widget_id, widget_id_string, num) {

			_orig_send_attachment = wp.media.editor.send.attachment;
			_orig_send_to_editor = window.send_to_editor;

			var _custom_media = true;
			var button_id = '#' + widget_id_string + num;
			var button = jQuery(button_id);

			// Override send_to_editor so we may hijack NextGEN image selection
			window.send_to_editor = function(html) {
				if (_custom_media) {

					if (!html) {
						// Normal media image was chosen
						return _orig_send_to_editor;
					}

					// Get appropriate values from NextGEN image
					var title = jQuery(html).attr('data-title');
					var imageurl = jQuery(html).attr('data-src');
					var alt = jQuery(html).attr('data-description');

					// Add to our area
					jQuery("#" + widget_id_string + 'preview' + num).html('<img src="' +
						imageurl +
						'" style="max-width: 80%;display: table;margin:auto;" />');
					jQuery("#" + widget_id_string + 'title' + num).val(title);
					jQuery("#" + widget_id_string + 'imageurl' + num).val(imageurl);
					jQuery("#" + widget_id_string + 'alt' + num).val(alt);

					try {
						// Close popup
						tb_remove();
					} catch (err) {
						// This is if NextGEN image was chosen -> close popup
						this.parent.tb_remove();
					}
				} else {
					// Use original send_to_editor
					return _orig_send_to_editor;
				}
			};

			// This is where we end up if normal media image was chosen
			wp.media.editor.send.attachment = function(props, attachment) {
				if (_custom_media) {

					var title = attachment.title;
					var imageurl = attachment.url;
					var alt = attachment.alt;

					jQuery("#" + widget_id_string + 'preview' + num).html('<img src="' +
						imageurl +
						'" style="max-width: 80%;display: table;margin:auto;" />');
					jQuery("#" + widget_id_string + 'title' + num).val(title);
					jQuery("#" + widget_id_string + 'imageurl' + num).val(imageurl);
					jQuery("#" + widget_id_string + 'alt' + num).val(alt);

				} else {
					return _orig_send_attachment.apply(button_id, [props, attachment]);
				}
			}

			wp.media.editor.open(button);
			return false;
		}
	}
});
