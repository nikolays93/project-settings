<?php

namespace CDevelopers\ProjectSettings;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

add_action( 'wp_loaded', __NAMESPACE__ . '\custom_post_types', 99 );
function custom_post_types() {
    if( $post_types = Utils::get_post_types() ) {
        foreach ($post_types as $cpt => $args) {
            if( ! Utils::is_built_in( $cpt ) ) {
                register_post_type( $cpt, $args );
            }
            else {
                /** Edit Registred Types */
                $p_object = get_post_type_object( $cpt );
                if ( ! $p_object ) continue;

                $p_object->labels = (object) array_merge((array) $p_object->labels, (array) $args['labels']);
            }
        }
    }
}

add_action( 'wp_loaded', __NAMESPACE__ . '\custom_taxonomies', 99 );
function custom_taxonomies() {
    if( $taxonomies = Utils::get_taxonomies() ) {
        foreach ($taxonomies as $custom_tax => $args) {
            $post_types = $args['post_types'];
            unset($args['post_types']);
            if( ! Utils::is_built_in( $custom_tax, 'taxes' ) ) {
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

