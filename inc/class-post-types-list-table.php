<?php

namespace PSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

if( ! class_exists( '\WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Post_Types_List_Table extends \WP_List_Table {
    private $types = array();

    public function set_type( $title = false, $single = false, $plural = false ){
        if( !$title || !$single || !$plural )
            return;

        $this->types[] = array(
            'ID' => '',
            'title' => $title,
            'single' => $single,
            'plural' => $plural
            );
    }
    /**
     * Render: Columns Data
     */
    function column_default($item, $column_name){
        switch($column_name){
            case 'single':
            case 'plural':
                return $item[$column_name];
            // Show the whole array for troubleshooting purposes
            // default:
            //     return print_r($item,true); 
        }
    }
    /**
     * Render: Row Titles
     */
    function column_title($item){
        $first = mb_substr($item['title'], 0, 1, 'UTF-8');
        $last =  mb_substr($item['title'], 1);
        $first = mb_strtoupper($first, 'UTF-8');
        $last =  mb_strtolower($last, 'UTF-8');
        $name = $first.$last;

        $remove_url = wp_nonce_url( sprintf('?page=%s&do=%s&cpt=%s', $_REQUEST['page'], 'remove', $item['title'] ),
            'trash-type-'.$item['title'],
            '_wpnonce' );

        $actions = array(
            'edit' => '<a href="?page=project-settings&post-type='.$item['title'].'">'.__('Edit').'</a>',
            'delete' => '<a href="'.$remove_url.'">'.__('Delete').'</a>',
        );

        return $name . $this->row_actions($actions);
    }
    function column_cb($item){
        return '<input type="checkbox" name="cpts[]" value="'.$item['title'].'" />';
    }
    /**
     * Render: Head Row
     */
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => __('Title'),
            'single'    => __('Single'),
            'plural'  => __('Plural'),
        );
        return $columns;
    }
    // /**
    //  * Bulk actions
    //  */
    // function get_bulk_actions() {
    //     $actions = array(
    //         'delete'    => __('Delete')
    //     );
    //     return $actions;
    // }
    // function process_bulk_action() {
    //     if( 'delete'===$this->current_action() ) {
    //         $all_cpts = isset($_POST['cpt']) ? unserialize(str_replace('\"', '"', $_POST['cpt'])) : array();
    //         if( count($all_cpts) >= 1 ){
    //             foreach ($_POST['cpts'] as $cpt) {
    //                 unset($all_cpts[$cpt]);
    //             }
    //         }
    //         update_option( DT_CPT_OPTION, $all_cpts );
    //         wp_redirect( $_POST['_wp_http_referer'] );
    //         exit();
    //     }
    // }
    /**
     * Table Construct
     */
    function prepare_items() {
        // $this->process_bulk_action();
        $this->_column_headers = array( $this->get_columns() );
        $this->items = $this->types;
    }
}