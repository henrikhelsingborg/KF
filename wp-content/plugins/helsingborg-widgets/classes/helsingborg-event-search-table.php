<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

 /**
 * To display this on a page, you will first need to instantiate the class,
 * then call $Helsingborg_Event_List_Table->prepare_items() to handle any data manipulation, then
 * finally call $Helsingborg_Event_List_Table->display() to render the table to the page.
 */
class Helsingborg_Event_Search_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'event',     //singular name of the listed records
            'plural'    => 'events',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'EventID':
            case 'Name':
            case 'Date':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_EventID($item){

        $actions = array(
            'edit'      => sprintf('<a href="?page=helsingborg-event-details&id=%s">Ändra</a>',$item['EventID']),
        );

        //Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['EventID'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function get_columns(){
        $columns = array(
            'EventID'     => 'ID',
            'Name'    => 'Namn',
            'Date'  => 'Datum'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'EventID'     => array('EventID',false),     //true means it's already sorted
            'Name'    => array('Name',false),
            'Date'  => array('Date',false)
        );
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb;
        $per_page = 8;
        $hidden = array();
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $searchTerm = $_REQUEST['searchterm'];
        if ($searchTerm){
          $data = HelsingborgEventModel::load_events_with_name($searchTerm);
        } else {
          $data = array();
        }

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'EventID'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page),
            'orderby'   => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'title',
            'order'     => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'
        ) );
    }

}
