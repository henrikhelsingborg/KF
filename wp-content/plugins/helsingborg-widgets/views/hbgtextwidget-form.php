<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title')?>:</label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
</p>
<div style="margin-bottom:15px;">
<?php
    /**
     * Settings of the WYSIWYG-editor
     * @var array
     */
    $rand = rand(0, 999);
    $editorId = $this->get_field_id('hbgtexteditor_' . $rand);
    $editorName = $this->get_field_name('hbgtexteditor_' . $rand);

    $wysiwygSettings = array(
        'textarea_name' => $editorName,
        'textarea_rows' => 20,
    );

    /**
     * Renders the WYSIWYG-editor
     */
    wp_editor($instance['content'], $editorId, $wysiwygSettings);
?>
</div>
<input type="text" id="<?php echo $this->get_field_id('rand'); ?>" name="<?php echo $this->get_field_name('rand'); ?>" value="<?php echo $rand; ?>">