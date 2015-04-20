<?php
/**
 * HbgTextWidget
 * This is a custom text widget with WYSIWYG-editor
 */

if (!class_exists('HbgTextWidget')) {
    class HbgTextWidget extends WP_Widget {

        protected $_viewsPath;

        /**
         * Constructor
         */
        public function __construct() {
            // Sets the views directory path
            $this->_viewsPath = plugin_dir_path(plugin_dir_path(__FILE__)) . 'views/';

            // Register the widget on widgets_init
            add_action('widgets_init', array($this, 'registerWidget'));

            // Enqueue js
            add_action('widgets_init', array($this, 'addJs'));

            // Widget arguments
            parent::__construct(
                'hbgtextwidget',
                '* Text',
                array(
                    "description" => __('Textwidget med WYSIWYG-editor')
                )
            );
        }

        /**
        * Registers the widget
        */
        public function registerWidget()
        {
            register_widget('hbgtextwidget');
        }

        /**
         * Enqueue js
         */
        public function addJs() {
            global $pagenow;
            wp_enqueue_script('helsingborg-text-widget', plugins_url('helsingborg-widgets/js/helsingborg-text-widget.js'), array('jquery'), false, true);
        }

        /**
         * Renders the text widget form
         * @param  object $instance The current widget instance
         * @return void
         */
        public function form($instance) {
            /**
             * Default settings for the widget
             * @var array
             */
            $defaults = array(
                'content' => ''
            );

            /**
             * Merge $instance with $defaults
             * This will make sure none of the default arguments is missing in the $instance
             * @var array
             */
            $instance = array_merge($defaults, $instance);

            /**
             * Require the widget form markup
             */
            require($this->_viewsPath . 'hbgtextwidget-form.php');
        }

        /**
        * Prepare widget options for save
        * @param  array $newInstance The new widget options
        * @param  array $oldInstance The old widget options
        * @return array              The merged instande of new and old to be saved
        */
        public function update($newInstance, $oldInstance)
        {
            /**
             * Put the content in corret array key
             */
            $rand = $newInstance['rand'];
            $editor_content = $newInstance['hbgtexteditor_' . $rand];
            $newInstance['content'] = $editor_content;
            unset($newInstance['hbgtexteditor_' . $rand]);

            /**
             * Merges $oldInstance and $newInstance
             * @var array
             */
            $instance = array_merge($oldInstance, $newInstance);

            return $instance;
        }

        /**
         * Display the widget markup
         * @param  array $args     The widget arguments
         * @param  array $instance The widget instance
         * @return void            The widget markup
         */
        public function widget($args, $instance) {
            extract($args);
            require($this->_viewsPath . 'hbgtextwidget-widget.php');
        }

    }
}