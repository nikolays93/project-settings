<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

if( ! class_exists( '\WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Registrations_Table extends \WP_List_Table {
    private $columns = array(),
            $values = array(),
            $context = '';

    function __construct($context = 'types', $args = array())
    {
        $this->context = $context;
        parent::__construct($args);
    }
    /**
     * Set: Head Row
     */
    public function set_columns( $columns )
    {
        $this->columns = wp_parse_args( $columns, array(
            'cb'     => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'  => __('Title'),
        ) );

        return $columns;
    }

    public function get_columns() {
        return $this->columns;
    }

    /**
     * Set: Body Row
     */
    public function set_value( $values )
    {
        $this->values[] = wp_parse_args( $values, array(
            'ID'    => '',
            'title' => '',
        ) );
    }

    /**
     * Render: Callbacks checkbox
     */
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="cb[]" value="%s" />',
            esc_attr($item['title']) );
    }

    /**
     * Render: Row Titles
     */
    function column_title($item)
    {
        $first = mb_substr($item['title'], 0, 1, 'UTF-8');
        $last =  mb_substr($item['title'], 1);
        $first = mb_strtoupper($first, 'UTF-8');
        $last =  mb_strtolower($last, 'UTF-8');
        $name = $first . $last;

        $actions = array(
            'edit' => sprintf('<a href="?page=%s&do=edit&context=%s&value=%s">%s</a>',
                Utils::OPTION,
                esc_attr( $this->context ),
                esc_attr( $item['title'] ),
                esc_attr( __('Edit') )
            ),
            'delete' => sprintf('<a href="%s">%s</a>',
                wp_nonce_url( sprintf('?page=%s&do=remove&context=%s&value=%s',
                    esc_attr( $_REQUEST['page'] ),
                    esc_attr( $this->context ),
                    esc_attr( $item['title'] ) ), 'trash-'.$item['title'], '_wpnonce' ),
                __('Delete')
            ),
        );

        return $name . $this->row_actions($actions);
    }

    /**
     * Render: Columns Data
     */
    function column_default($item, $column_name)
    {
        if( isset($item[ $column_name ]) )
            return $item[ $column_name ];

        return false;
    }

    public function single_row( $item )
    {
        printf('<tr class="%s">',
            !empty( $item['classrow'] ) ? $item['classrow'] : '');
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * Table Construct
     */
    function prepare_items()
    {
        // $this->process_bulk_action();
        $this->_column_headers = array( $this->columns );
        $this->items = $this->values;
    }
}
