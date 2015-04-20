jQuery('.widget-control-save').on('click', function () {
    console.log("HACK HACK");
    var saveId = jQuery(this).attr('id');
    var id = saveId.replace( /-savewidget/, '' );
    var randNum = jQuery('#' + id + '-rand').val();
    var textTab = id + '-hbgtexteditor_' + randNum + '-html';
    jQuery('#' + textTab).trigger('click');
});