jQuery(document).ready(function($) {
    var $page_template = $('#page_template')
        ,$metabox = $('#samling-page-node-select'); // For example

    $page_template.change(function() {
	   
        if ($(this).val() == 'templates/page-samling.php') {
            $metabox.show();
        } else {
            $metabox.hide();
        }
    }).change();

});