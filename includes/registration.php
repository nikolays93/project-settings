<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Registration
{
    private function __construct(){}

    private static function sanitize_register_option( $post_type )
    {
        foreach ($post_type as $prop => $value) {
            if( in_array($value, array('1', 'on', 'true')) ) {
                $post_type[ $prop ] = true;
            }
            elseif( $value == "" ){
                switch ( $prop ) {
                    case 'menu_position': $post_type[ $prop ] = 30; break;

                    default: unset($post_type[ $prop ]); break;
                }
            }
        }

        return $post_type;
    }

    private static function get_custom_registred( $context = 'types' ) // || taxes
    {
        $registred = self::get_register_option( $context );
        if( !empty($registred) && is_array($registred) ) {
            foreach ( $registred as &$sanitize ) {
                $sanitize = self::sanitize_register_option( $sanitize );
            }
        }

        return apply_filters('project-settings-registred', $registred);
    }

    private static function set_custom_register( $context = 'types', Array $values ) // || taxes
    {
        $registred = self::get_register_option( $context );
        $key  = ( 'types' === $context ) ? 'post_type' : 'taxonomy';
        $name = ( 'types' === $context ) ? 'post_type_name' : 'taxonomy_name';

        if( ! empty( $values[ $key ][ $name ] ) ) {
            if( isset($values[ $key ]['labels']) && is_array($values[ $key ]['labels']) ) {
                $values[ $key ]['labels'] = array_filter($values[ $key ]['labels']);
            }

            $option = self::get_register_option( $context );
            $option[ strtolower($values[ $key ][ $name ]) ] = $values[ $key ];
            self::update_register_option( $context, $option );
        }
    }

    public static function get_register_option( $context = 'types' ) // || taxes
    {
        $result = get_option(
            apply_filters( 'project-settings-register-option', Utils::REGISTER ), false );

        if( $context ) {
            $result = isset($result[ $context ])
                ? $result[ $context ] : false;
        }

        return $result;
    }

    public static function update_register_option( $context = 'types', $value = false ) // || taxes
    {
        $option = apply_filters( 'project-settings-register-option', Utils::REGISTER );
        $result = get_option( $option, false );

        $result[ $context ] = $value;

        update_option( $option, $result, apply_filters('project-settings-option-autoload', 'yes') );
    }

    public static function get_custom_post_types() {

        return self::get_custom_registred('types');
    }

    public static function get_custom_taxonomies() {

        return self::get_custom_registred('taxes');
    }

    public static function set_custom_post_types( Array $values ) {

        self::set_custom_register('types', $values);
    }

    public static function set_custom_taxonomies( Array $values ) {

        self::set_custom_register('taxes', $values);
    }

    /** Builtins */
    public static function get_builtins( $context = 'types' )
    {
        if( $builtins = wp_cache_get( 'builtin_' . $context, DOMAIN ) ) {
            return $builtins;
        }

        $func = ('taxes' === $context) ? 'get_taxonomies' : 'get_post_types';
        $builtins = $func( array('_builtin' => true), 'names' );
        wp_cache_set( 'builtin_' . $context, $builtins, DOMAIN );

        return $builtins;
    }

    public static function is_built_in( $type_name = false, $context = 'types' )
    {
        $builtins = apply_filters( 'project-settings-builtins', self::get_builtins( $context ) );

        return in_array( $type_name, $builtins );
    }

    /** Register hooks */
    static function custom_post_types() {
        if( $post_types = self::get_custom_post_types() ) {
            foreach ($post_types as $post_type => $args) {
                if( ! self::is_built_in( $post_type ) ) {
                    register_post_type( $post_type, $args );
                }
                else {
                    /** Edit Registred Types */
                    $obj = get_post_type_object( $cpt );
                    if ( ! $obj ) continue;

                    $obj->labels = (object) array_merge(
                        (array) $obj->labels, (array) $args['labels']);
                }
            }
        }
    }

    static function custom_taxonomies() {
        if( $taxonomies = self::get_custom_taxonomies() ) {
            foreach ($taxonomies as $custom_tax => $args) {
                if( empty($args['post_types']) ) continue;
                $post_types = $args['post_types'];
                unset($args['post_types']);
                if( ! self::is_built_in( $custom_tax, 'taxes' ) ) {
                    register_taxonomy( $custom_tax, $post_types, $args );
                }
                // else {
                //     /** Edit Registred Types */
                //     $p_object = get_post_type_object( $custom_tax );
                //     if ( ! $p_object ) continue;

                //     $p_object->labels = (object) array_merge((array) $p_object->labels, (array) $args['labels']);
                // }
            }
        }
    }

    static function register_customs() {
        add_action( 'wp_loaded', array(__CLASS__, 'custom_post_types'), 99 );
        add_action( 'wp_loaded', array(__CLASS__, 'custom_taxonomies'), 99 );
    }
}
