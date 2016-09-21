<?php
/*
Plugin Name: CODESIGN2 Content Section Plugin
Plugin URI: http://www.codesign2.co.uk
Description: Bring your content authoring and tooling into the 21st century with this tool.
Author: CODESIGN2
Version: 2.6.2
Author URI: http://www.codesign2.co.uk/
License: AGPL
*/

new cd2_content_sectionCPTClass();

/** 
 * The Class.
 */
class cd2_content_sectionCPTClass {

    const POST_TYPE = 'content_section';

    public function __construct() {
        add_action( 'init', [ $this, 'init' ] );
    }

    public function init() {
        $this->register_post_type();
        $this->register_shortcode();
        add_action( 'wp_ajax_get_content_section', [ $this, 'ajax_content_section' ] );
        add_action( 'wp_ajax_get_content_sections', [ $this, 'ajax_content_sections' ] );
    }
    
    public function ajax_content_section() {
        $name = isset( $_GET['name'] ) ? urldecode( $_GET['name'] ) : 'invalid';
        die(
            json_encode( 
                [ 
                    'content' => do_shortcode( 
                        '[' . self::POST_TYPE . ' name="' . esc_attr( $name ) . '"]' 
                    ) 
                ]
            )
        );
    }

    public function ajax_content_sections() {

        $out = [];
        $query = new WP_Query( 
            [
                'post_type' => self::POST_TYPE,
                'posts_per_page' => -1, //show all results
            ]
        );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $out[] = [
                    'ID' => get_the_ID(),
                    'title' => get_the_title(),
                    'author' => get_the_author(),
                    'content' => apply_filters( 'the_content', get_the_content() ),
                    'thumbnail' => wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ),
                ];
            }
        }
        die(
            json_encode(
                array_merge(
                    $_POST,
                    [
                        'wpurl' => get_option( 'siteurl' ),
                        'results' => $out,
                    ]
                )
            )
        );
    }
	
    public function register_shortcode() {
        add_shortcode( self::POST_TYPE, [ $this, 'render_sc' ] );
    }
    
    public function render_sc( $atts, $content='', $tag=self::POST_TYPE ) {
        $atts_named = shortcode_atts(
            [
                'name' => 'invalid',
            ], 
            $atts
        ); // Ensure that attributes exist
        $slug = sanitize_title( $atts_named[ 'name' ] );
        $out = '';
        $args = [
            'post_type' => self::POST_TYPE,
            'name' => $slug
        ];
        $the_query = new WP_Query( $args );
        $user = wp_get_current_user();
        // The Loop
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $out .= do_shortcode( apply_filters( 'the_content', get_the_content() ) );
            }
        }
        wp_reset_query();
        return $out;
    }

    public function register_post_type() {
        register_post_type( 
            self::POST_TYPE, 
            array(
                'label' => __( 'Content Sections', 'cd2_content_section' ),
                'labels'=> [
                    'name' => __( 'Content Sections', 'cd2_content_section' ),
                    'singular_name' => __( 'Content Section', 'cd2_content_section' ),
                    'add_new_item' => __( 'Add New Content Section', 'cd2_content_section' ),
                    'edit_item' => __( 'Edit Content Section', 'cd2_content_section' ),
                    'view_item' => __( 'View Content Section', 'cd2_content_section' ),
                    'search_items' => __( 'Search Content Sections', 'cd2_content_section' ),
                    'not_found' => __( 'No Matching Content Section Found', 'cd2_content_section' ),
                    'not_found_in_trash' => __( 'No Content Sections Found In Trash', 'cd2_content_section' ),
                ],
                'description' => __( 'Re-usable content, layouts etc, for maintaining re-usable blocks of content, style libraries, etc', 'cd2_content_section' ),
                'public' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_nav_menus' => false,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'menu_position' => 20,
                'menu_icon' => 'dashicons-exerpt-view',
                'capability_type' => 'page', 
                'capabilities' => [
                    "edit_post",
                    "read_post",
                    "delete_post",
                    "edit_posts",
                    "edit_others_posts",
                    "publish_posts",
                    "read_private_posts",
                    "delete_posts",
                    "delete_private_posts",
                    "delete_published_posts",
                    "delete_others_posts",
                    "edit_private_posts",
                    "edit_published_posts",
                ],
                'parent_item_colon' => true,
                'map_meta_cap' => true,
                'hierarchical' => false,
                'supports' => [
                    "title",
                    "editor",
                    "thumbnail"
                ],
                'register_meta_box_cb' => null,
                'taxonomies' => [],
                'has_archive' => false,
                'can_export' => true
            )
        );
    }
}
