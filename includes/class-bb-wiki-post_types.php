<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://baasbox.com
 * @since      1.0.0
 *
 * @package    Bb_Wiki
 * @subpackage Bb_Wiki/includes
 */

class Bb_Wiki_Post_Types {

    /**
     * Register custom post type
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     */
    private function register_single_post_type( $fields ) {

        /**
		 * Labels used when displaying the posts in the admin and sometimes on the front end.  These
		 * labels do not cover post updated, error, and related messages.  You'll need to filter the
		 * 'post_updated_messages' hook to customize those.
		 */
        $labels = array(
            'name'                  => $fields['plural'],
            'singular_name'         => $fields['singular'],
            'menu_name'             => $fields['menu_name'],
            'new_item'              => sprintf( __( 'Nuovo articolo %s', 'plugin-name' ), $fields['singular'] ),
            'add_new_item'          => sprintf( __( 'Aggiungi nuovo articolo %s', 'plugin-name' ), $fields['singular'] ),
            'edit_item'             => sprintf( __( 'Modifica articolo %s', 'plugin-name' ), $fields['singular'] ),
            'view_item'             => sprintf( __( 'Visualizza articolo %s', 'plugin-name' ), $fields['singular'] ),
            'view_items'            => sprintf( __( 'Visualizza articoli %s', 'plugin-name' ), $fields['plural'] ),
            'search_items'          => sprintf( __( 'Cerca articoli %s', 'plugin-name' ), $fields['plural'] ),
            'not_found'             => sprintf( __( 'Nessun articolo %s trovato', 'plugin-name' ), strtolower( $fields['plural'] ) ),
            'not_found_in_trash'    => sprintf( __( 'Nessun articolo %s trovato nel cestino', 'plugin-name' ), strtolower( $fields['plural'] ) ),
            'all_items'             => sprintf( __( 'Tutti gli articoli %s', 'plugin-name' ), $fields['plural'] ),
            'archives'              => sprintf( __( 'Archivi articoli %s', 'plugin-name' ), $fields['singular'] ),
            'attributes'            => sprintf( __( 'Attributi articoli %s', 'plugin-name' ), $fields['singular'] ),
            'insert_into_item'      => sprintf( __( 'Inserisci dentro articolo %s', 'plugin-name' ), strtolower( $fields['singular'] ) ),
            'uploaded_to_this_item' => sprintf( __( 'Caricato a questo articolo %s', 'plugin-name' ), strtolower( $fields['singular'] ) ),

            /* Labels for hierarchical post types only. */
            'parent_item'           => sprintf( __( 'Padre %s', 'plugin-name' ), $fields['singular'] ),
            'parent_item_colon'     => sprintf( __( 'Padre %s:', 'plugin-name' ), $fields['singular'] ),

            /* Custom archive label.  Must filter 'post_type_archive_title' to use. */
			'archive_title'        => $fields['plural'],
        );

        $args = array(
            'labels'             => $labels,
            'description'        => ( isset( $fields['description'] ) ) ? $fields['description'] : '',
            'public'             => ( isset( $fields['public'] ) ) ? $fields['public'] : true,
            'publicly_queryable' => ( isset( $fields['publicly_queryable'] ) ) ? $fields['publicly_queryable'] : true,
            'exclude_from_search'=> ( isset( $fields['exclude_from_search'] ) ) ? $fields['exclude_from_search'] : false,
            'show_ui'            => ( isset( $fields['show_ui'] ) ) ? $fields['show_ui'] : true,
            'show_in_menu'       => ( isset( $fields['show_in_menu'] ) ) ? $fields['show_in_menu'] : true,
            'query_var'          => ( isset( $fields['query_var'] ) ) ? $fields['query_var'] : true,
            'show_in_admin_bar'  => ( isset( $fields['show_in_admin_bar'] ) ) ? $fields['show_in_admin_bar'] : true,
            'capability_type'    => ( isset( $fields['capability_type'] ) ) ? $fields['capability_type'] : 'post',
            'has_archive'        => ( isset( $fields['has_archive'] ) ) ? $fields['has_archive'] : true,
            'hierarchical'       => ( isset( $fields['hierarchical'] ) ) ? $fields['hierarchical'] : true,
            'supports'           => ( isset( $fields['supports'] ) ) ? $fields['supports'] : array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
            ),
            'menu_position'      => ( isset( $fields['menu_position'] ) ) ? $fields['menu_position'] : 21,
            'menu_icon'          => ( isset( $fields['menu_icon'] ) ) ? $fields['menu_icon']: 'dashicons-admin-generic',
            'show_in_nav_menus'  => ( isset( $fields['show_in_nav_menus'] ) ) ? $fields['show_in_nav_menus'] : true,
            'show_in_rest'       => ( isset( $fields['show_in_rest'] ) ) ? $fields['show_in_rest'] : true,
            'taxonomies'         => ( isset( $fields['taxonomies'] ) ) ? $fields['taxonomies'] : [],
        );

        if ( isset( $fields['rewrite'] ) ) {

            /**
             *  Add $this->plugin_name as translatable in the permalink structure,
             *  to avoid conflicts with other plugins which may use customers as well.
             */
            $args['rewrite'] = $fields['rewrite'];
        }

        if ( $fields['custom_caps'] ) {

            /**
             * Provides more precise control over the capabilities than the defaults.  By default, WordPress
             * will use the 'capability_type' argument to build these capabilities.  More often than not,
             * this results in many extra capabilities that you probably don't need.  The following is how
             * I set up capabilities for many post types, which only uses three basic capabilities you need
             * to assign to roles: 'manage_examples', 'edit_examples', 'create_examples'.  Each post type
             * is unique though, so you'll want to adjust it to fit your needs.
             *
             * @link https://gist.github.com/creativembers/6577149
             * @link http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
             */
            $args['capabilities'] = array(

                // Meta capabilities
                'edit_post'                 => 'edit_' . strtolower( $fields['singular'] ),
                'read_post'                 => 'read_' . strtolower( $fields['singular'] ),
                'delete_post'               => 'delete_' . strtolower( $fields['singular'] ),

                // Primitive capabilities used outside of map_meta_cap():
                'edit_posts'                => 'edit_' . strtolower( $fields['plural'] ),
                'edit_others_posts'         => 'edit_others_' . strtolower( $fields['plural'] ),
                'publish_posts'             => 'publish_' . strtolower( $fields['plural'] ),
                'read_private_posts'        => 'read_private_' . strtolower( $fields['plural'] ),

                // Primitive capabilities used within map_meta_cap():
                'delete_posts'              => 'delete_' . strtolower( $fields['plural'] ),
                'delete_private_posts'      => 'delete_private_' . strtolower( $fields['plural'] ),
                'delete_published_posts'    => 'delete_published_' . strtolower( $fields['plural'] ),
                'delete_others_posts'       => 'delete_others_' . strtolower( $fields['plural'] ),
                'edit_private_posts'        => 'edit_private_' . strtolower( $fields['plural'] ),
                'edit_published_posts'      => 'edit_published_' . strtolower( $fields['plural'] ),
                'create_posts'              => 'edit_' . strtolower( $fields['plural'] )

            );

            /**
             * Adding map_meta_cap will map the meta correctly.
             * @link https://wordpress.stackexchange.com/questions/108338/capabilities-and-custom-post-types/108375#108375
             */
            $args['map_meta_cap'] = true;

            /**
             * Assign capabilities to users
             * Without this, users - also admins - can not see post type.
             */
            $this->assign_capabilities( $args['capabilities'], $fields['custom_caps_users'] );
        }


        /**
         * Register Taxnonmies if any
         * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        if ( isset( $fields['custom_taxonomies'] ) && is_array( $fields['custom_taxonomies'] ) ) {

            foreach ( $fields['custom_taxonomies'] as $taxonomy ) {

                $this->register_single_post_type_taxnonomy( $taxonomy );

            }

        }
	    
	    register_post_type( $fields['slug'], $args );

    }

    private function register_single_post_type_taxnonomy( $tax_fields ) {

        $labels = array(
            'name'                       => $tax_fields['plural'],
            'singular_name'              => $tax_fields['single'],
            'menu_name'                  => $tax_fields['plural'],
            'all_items'                  => sprintf( __( 'All %s' , 'plugin-name' ), $tax_fields['plural'] ),
            'edit_item'                  => sprintf( __( 'Edit %s' , 'plugin-name' ), $tax_fields['single'] ),
            'view_item'                  => sprintf( __( 'View %s' , 'plugin-name' ), $tax_fields['single'] ),
            'update_item'                => sprintf( __( 'Update %s' , 'plugin-name' ), $tax_fields['single'] ),
            'add_new_item'               => sprintf( __( 'Add New %s' , 'plugin-name' ), $tax_fields['single'] ),
            'new_item_name'              => sprintf( __( 'New %s Name' , 'plugin-name' ), $tax_fields['single'] ),
            'parent_item'                => sprintf( __( 'Parent %s' , 'plugin-name' ), $tax_fields['single'] ),
            'parent_item_colon'          => sprintf( __( 'Parent %s:' , 'plugin-name' ), $tax_fields['single'] ),
            'search_items'               => sprintf( __( 'Search %s' , 'plugin-name' ), $tax_fields['plural'] ),
            'popular_items'              => sprintf( __( 'Popular %s' , 'plugin-name' ), $tax_fields['plural'] ),
            'separate_items_with_commas' => sprintf( __( 'Separate %s with commas' , 'plugin-name' ), $tax_fields['plural'] ),
            'add_or_remove_items'        => sprintf( __( 'Add or remove %s' , 'plugin-name' ), $tax_fields['plural'] ),
            'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s' , 'plugin-name' ), $tax_fields['plural'] ),
            'not_found'                  => sprintf( __( 'No %s found' , 'plugin-name' ), $tax_fields['plural'] ),
        );

        $args = array(
        	'label'                 => $tax_fields['plural'],
        	'labels'                => $labels,
        	'hierarchical'          => ( isset( $tax_fields['hierarchical'] ) )          ? $tax_fields['hierarchical']          : true,
        	'public'                => ( isset( $tax_fields['public'] ) )                ? $tax_fields['public']                : true,
        	'show_ui'               => ( isset( $tax_fields['show_ui'] ) )               ? $tax_fields['show_ui']               : true,
        	'show_in_nav_menus'     => ( isset( $tax_fields['show_in_nav_menus'] ) )     ? $tax_fields['show_in_nav_menus']     : true,
        	'show_tagcloud'         => ( isset( $tax_fields['show_tagcloud'] ) )         ? $tax_fields['show_tagcloud']         : true,
        	'meta_box_cb'           => ( isset( $tax_fields['meta_box_cb'] ) )           ? $tax_fields['meta_box_cb']           : null,
        	'show_admin_column'     => ( isset( $tax_fields['show_admin_column'] ) )     ? $tax_fields['show_admin_column']     : true,
        	'show_in_quick_edit'    => ( isset( $tax_fields['show_in_quick_edit'] ) )    ? $tax_fields['show_in_quick_edit']    : true,
        	'update_count_callback' => ( isset( $tax_fields['update_count_callback'] ) ) ? $tax_fields['update_count_callback'] : '',
        	'show_in_rest'          => ( isset( $tax_fields['show_in_rest'] ) )          ? $tax_fields['show_in_rest']          : true,
        	'rest_base'             => $tax_fields['taxonomy'],
        	'rest_controller_class' => ( isset( $tax_fields['rest_controller_class'] ) ) ? $tax_fields['rest_controller_class'] : 'WP_REST_Terms_Controller',
        	'query_var'             => $tax_fields['taxonomy'],
        	'rewrite'               => ( isset( $tax_fields['rewrite'] ) )               ? $tax_fields['rewrite']               : true,
        	'sort'                  => ( isset( $tax_fields['sort'] ) )                  ? $tax_fields['sort']                  : '',
        );

        $args = apply_filters( $tax_fields['taxonomy'] . '_args', $args );

        register_taxonomy( $tax_fields['taxonomy'], $tax_fields['post_types'], $args );

    }

    /**
     * Assign capabilities to users
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     * @link https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     */
    public function assign_capabilities( $caps_map, $users  ) {

        foreach ( $users as $user ) {

            $user_role = get_role( $user );

            foreach ( $caps_map as $cap_map_key => $capability ) {

                $user_role->add_cap( $capability );

            }

        }

    }

    /**
     * Create post types
     */
    public function create_custom_post_type() {

        /**
         * This is not all the fields, only what I find important. Feel free to change this function ;)
         *
         * @link https://codex.wordpress.org/Function_Reference/register_post_type
         */
        $post_types_fields = array(

            array(
                /**
                 * Post type name/slug. Max of 20 characters! Uppercase and spaces not allowed.
                 */
                'slug'                  => 'wiki',
                'singular'              => 'Wiki',
                'plural'                => 'Wiki',
                'menu_name'             => 'Duir',
                'description'           => 'Pagina enciclopedica che spiega la parola contenuta nel titolo.',
                'has_archive'           => false,
                'hierarchical'          => false,

                /**
                 * The URI to the icon to use for the admin menu item. There is no header icon argument, so
                 * you'll need to use CSS to add one.
                 *
                 * Dashicons:
                 * @link https://developer.wordpress.org/resource/dashicons/
                 */
                'menu_icon'             => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iODBweCIgaGVpZ2h0PSI4MHB4IiB2aWV3Qm94PSIwIDAgODAgODAiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8dGl0bGU+Q29zbyBwZXIgZW1haWxfbmVybzwvdGl0bGU+CiAgICA8ZGVmcz4KICAgICAgICA8cG9seWdvbiBpZD0icGF0aC0xIiBwb2ludHM9IjAgMCA4MCAwIDgwIDgwLjAwMDM0ODMgMCA4MC4wMDAzNDgzIj48L3BvbHlnb24+CiAgICA8L2RlZnM+CiAgICA8ZyBpZD0iQ29zby1wZXItZW1haWxfbmVybyIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Ikdyb3VwLTMiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLCAtMC4wMDAwMDApIj4KICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPgogICAgICAgICAgICAgICAgPHVzZSB4bGluazpocmVmPSIjcGF0aC0xIj48L3VzZT4KICAgICAgICAgICAgPC9tYXNrPgogICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik03MS43MjIzNTI0LDU0LjM1NTczMjQgTDYzLjY2MjUxMzEsNTQuMzU1NzMyNCBMNzIuOTkyNTE0Myw0OC4yNjAzNDg0IEM3My42NzQwMzYzLDQ3LjgxNTY3NTYgNzQuMDQ0NDAzNCw0Ny4wNzM3ODAzIDc0LjA0NDQwMzQsNDYuMzE1NjMwNiBDNzQuMDQ0NDAzNCw0NS44ODAyNDYgNzMuOTIzNjU2OCw0NS40MzkwNTYzIDczLjY2NzA3MDEsNDUuMDQ2NjI5NyBDNzIuOTY0NjQ5Nyw0My45NzM4NDIxIDcxLjUyNDk3OCw0My42Njk2NTM0IDcwLjQ1MjE5MDQsNDQuMzczMjM0OSBMNDIuMzIyODYzOCw2Mi43NDg3ODYgTDQyLjMyMjg2MzgsNDcuNTc5OTg3NCBMNDguMDQyMDc1Niw0My44Nzk3OTkgTDQ4LjA0MjA3NTYsNDYuMzE2NzkxNiBDNDguMDQyMDc1Niw0Ny41OTk3MjQ4IDQ5LjA4MjM1NDQsNDguNjM4ODQyNyA1MC4zNjQxMjY2LDQ4LjYzODg0MjcgQzUxLjY0NzA1OTgsNDguNjM4ODQyNyA1Mi42ODYxNzc3LDQ3LjU5OTcyNDggNTIuNjg2MTc3Nyw0Ni4zMTY3OTE2IEw1Mi42ODYxNzc3LDQwLjg3NTA2NDkgTDcyLjk4NDM4NzEsMjcuNzQyNzA1MSBDNzMuMDM2NjMzMywyNy43MDc4NzQ0IDczLjA4ODg3OTQsMjcuNjcxODgyNiA3My4xMzc2NDI1LDI3LjYzMzU2ODcgQzc0LjU5MTI0NjQsMzEuNTMyMjkyNSA3NS4zNTYzNjIzLDM1LjcwNzM0MDMgNzUuMzU2MzYyMywzOS45OTk2NTE3IEM3NS4zNTYzNjIzLDQ1LjEzNDg2NzYgNzQuMjU2ODcxMSw1MC4wMTU4MTkgNzIuMjc4NDgzNiw1NC40MjMwNzE5IEM3Mi4xMDA4NDY3LDU0LjM3ODk1MjkgNzEuOTEzOTIxNiw1NC4zNTU3MzI0IDcxLjcyMjM1MjQsNTQuMzU1NzMyNCBMNzEuNzIyMzUyNCw1NC4zNTU3MzI0IFogTTQyLjMyMjg2MzgsNzUuMjgwODk1NiBMNDIuMzIyODYzOCw2OC4yOTYxNjYgTDU2LjU1MzU1MzgsNTguOTk5ODM0NiBMNjkuODA2NjYwMiw1OC45OTk4MzQ2IEM2My45MTA5NzI2LDY4LjIxNDg5NDIgNTMuODYyMjk2Niw3NC41Mjg1NTExIDQyLjMyMjg2MzgsNzUuMjgwODk1NiBMNDIuMzIyODYzOCw3NS4yODA4OTU2IFogTTM4Ljc1NTAzMjMsMjMuNDMwNjU2MyBDMzguMDgzOTU5NiwyMy44NTU1OTE2IDM3LjY3ODc2MTcsMjQuNTk2MzI1OSAzNy42Nzg3NjE3LDI1LjM5MDQ2NzQgTDM3LjY3ODc2MTcsNDIuMDE2MzUzIEwzMS44NDgwOTE0LDM4LjE5MTkzNDkgTDMxLjg0ODA5MTQsMjUuMzk1MTExNSBDMzEuODQ4MDkxNCwyNC4xMTMzMzkzIDMwLjgwODk3MzYsMjMuMDczMDYwNCAyOS41MjYwNDA0LDIzLjA3MzA2MDQgQzI4LjI0NDI2ODIsMjMuMDczMDYwNCAyNy4yMDM5ODkzLDI0LjExMzMzOTMgMjcuMjAzOTg5MywyNS4zOTUxMTE1IEwyNy4yMDM5ODkzLDM1LjE0NTQwMzkgTDIwLjI1NjQxMjUsMzAuNTg2MDU2NyBDMjAuMjMwODY5OSwzMC41Njg2NDEzIDIwLjIwNTMyNzQsMzAuNTUyMzg2OSAyMC4xNzc0NjI4LDMwLjUzNDk3MTUgTDkuOTkwNjI0NzIsMjMuODUwOTQ3NSBDOS42MDc0ODYyOSwyMy42MDAxNjYgOS4xNzc5MDY4NSwyMy40NzU5MzYzIDguNzUwNjQ5NDUsMjMuNDcwMTMxMiBDMTIuOTA1OTU5OCwxNS42NDgzMDIxIDE5LjkxMDQyNjksOS41NjIyMDYzIDI4LjM3MzE0Miw2LjYwNzM5NjMxIEwzNC40MTE2MzU4LDEyLjY3MTQzMjcgTDI5LjUyNjA0MDQsMTIuNjcxNDMyNyBDMjguMjQ0MjY4MiwxMi42NzE0MzI3IDI3LjIwMzk4OTMsMTMuNzEwNTUwNSAyNy4yMDM5ODkzLDE0Ljk5MzQ4MzcgQzI3LjIwMzk4OTMsMTYuMjc1MjU1OSAyOC4yNDQyNjgyLDE3LjMxNTUzNDggMjkuNTI2MDQwNCwxNy4zMTU1MzQ4IEw0MC4wMDA4MTI3LDE3LjMxNTUzNDggQzQwLjkzODkyMTMsMTcuMzE1NTM0OCA0MS43ODUzMDksMTYuNzUwMTE1NCA0Mi4xNDQwNjU5LDE1Ljg4Mzk5MDMgQzQyLjI2NDgxMjUsMTUuNTk2MDU2IDQyLjMyMjg2MzgsMTUuMjk0MTg5NCA0Mi4zMjI4NjM4LDE0Ljk5NDY0NDggQzQyLjMyMjg2MzgsMTQuMzkyMDcyNSA0Mi4wODgzMzY2LDEzLjc5ODc4ODUgNDEuNjQ1OTg1OSwxMy4zNTUyNzY3IEwzMy41NTgyODIsNS4yMzE1ODEwNiBDMzguMjU1NzkxMyw0LjM2NDI5NDk4IDQzLjM1MDM3MTQsNC40Njc2MjYyNSA0OC4wNDIwNzU2LDUuNTU0MzQ2MTUgTDQ4LjA0MjA3NTYsMTcuNTI5MTYzNSBMMzguNzU1MDMyMywyMy40MzA2NTYzIFogTTM3LjY3ODc2MTcsNjIuNzE2Mjc3MyBMMjUuNjQxMjQ4OSw1NC43NDIzNTM5IEMyNS42NDAwODc5LDU0Ljc0MTE5MjkgMjUuNjM4OTI2OSw1NC43NDExOTI5IDI1LjYzNzc2NTgsNTQuNzQwMDMxOSBMOS45OTk5MTI5Miw0NC4zODEzNjIxIEM4LjkyOTQ0NzM4LDQzLjY3MDgxNDQgNy40ODk3NzU3Miw0My45NjU3MTQ5IDYuNzgxNTUwMTQsNDUuMDM1MDE5NCBDNi41MjAzMTk0LDQ1LjQyOTc2ODEgNi4zOTQ5Mjg2NCw0NS44NzQ0NDA5IDYuMzk0OTI4NjQsNDYuMzE1NjMwNiBDNi4zOTQ5Mjg2NCw0Ny4wNjc5NzUxIDYuNzYwNjUxNjgsNDcuODA2Mzg3NCA3LjQzNTIwNzUyLDQ4LjI1MzM4MjIgTDE2LjY0Nzk0NTEsNTQuMzU1NzMyNCBMOC43MTY5Nzk3MSw1NC4zNTU3MzI0IEM4LjM4MjYwNDM1LDU0LjM1NTczMjQgOC4wNjY4MDU0MSw1NC40MjY1NTUgNy43Nzg4NzEwOCw1NC41NTMxMDY4IEM1Ljc2NTY1MjgsNTAuMTEyMTg0MSA0LjY0NDEwMjE0LDQ1LjE4NDc5MTcgNC42NDQxMDIxNCwzOS45OTk2NTE3IEM0LjY0NDEwMjE0LDM1LjU0MTMxMzYgNS40NzMwNzQzNywzMS4yNzMzODM4IDYuOTg1ODkwNjQsMjcuMzQwOTkwMyBDNy4xMTcwODY1MiwyNy40ODg0NDA1IDcuMjcwMzQxODksMjcuNjIwNzk3NSA3LjQ0NDQ5NTcyLDI3LjczNDU3OCBMMTYuNjIzNTYzNiwzMy43NTY4MTc0IEwxNi42MjM1NjM2LDQ2LjMxNjc5MTYgQzE2LjYyMzU2MzYsNDcuNTk5NzI0OCAxNy42NjI2ODE0LDQ4LjYzODg0MjcgMTguOTQ1NjE0Nyw0OC42Mzg4NDI3IEMyMC4yMjczODY5LDQ4LjYzODg0MjcgMjEuMjY3NjY1Nyw0Ny41OTk3MjQ4IDIxLjI2NzY2NTcsNDYuMzE2NzkxNiBMMjEuMjY3NjY1NywzNi44MDQ1MDk0IEwyNy42NjgzOTk1LDQxLjAwMzkzODggQzI3Ljk4ODg0MjUsNDEuNDMxMTk2MiAyOC40NTMyNTI4LDQxLjc0MzUxMiAyOC45ODk2NDY2LDQxLjg3MTIyNDkgTDM3LjY3ODc2MTcsNDcuNTcwNjk5MiBMMzcuNjc4NzYxNyw2Mi43MTYyNzczIFogTTM3LjY3ODc2MTcsNzUuMjgwODk1NiBDMjYuMTM4MTY3OCw3NC41Mjg1NTExIDE2LjA4ODMzMDgsNjguMjE0ODk0MiAxMC4xOTM4MDQyLDU4Ljk5OTgzNDYgTDIzLjY1OTM3ODMsNTguOTk5ODM0NiBMMzcuNjc4NzYxNyw2OC4yODY4Nzc4IEwzNy42Nzg3NjE3LDc1LjI4MDg5NTYgWiBNNTEuNjA2NDI0LDIwLjc2NjEwMjcgTDYzLjI0NTcwNDksMTMuMzcxNTMxMSBDNjYuNTczMjA0MSwxNi4yNzQwOTQ5IDY5LjI4MzAzNzcsMTkuNzEzMDUyNSA3MS4yODkyODk4LDIzLjUxMDc2NzEgQzcxLjAwMjUxNjUsMjMuNTY0MTc0MiA3MC43MjE1NDgzLDIzLjY3NDQ3MTcgNzAuNDYwMzE3NiwyMy44NDI4MjA0IEw2My4yNTAzNDksMjguNTA3ODIxIEw2My4yNTAzNDksMjUuMzk1MTExNSBDNjMuMjUwMzQ5LDI0LjExMzMzOTMgNjIuMjExMjMxMiwyMy4wNzMwNjA0IDYwLjkyODI5OCwyMy4wNzMwNjA0IEM1OS42NDY1MjU4LDIzLjA3MzA2MDQgNTguNjA2MjQ2OSwyNC4xMTMzMzkzIDU4LjYwNjI0NjksMjUuMzk1MTExNSBMNTguNjA2MjQ2OSwzMS41MTM3MTYxIEw0OS4xNDM4ODg4LDM3LjYzNTgwMzcgQzQ5LjExNzE4NTIsMzcuNjUyMDU4MSA0OS4wOTE2NDI2LDM3LjY2ODMxMjQgNDkuMDY2MTAwMSwzNy42ODU3Mjc4IEw0Mi4zMjI4NjM4LDQyLjA0ODg2MTggTDQyLjMyMjg2MzgsMjYuNjY1MjczNCBMNTEuNjA2NDI0LDIwLjc2NzI2MzcgTDUxLjYwNjQyNCwyMC43NjYxMDI3IFogTTUyLjY4NjE3NzcsNi45Nzg5MjQ0OCBDNTQuOTkwODEzNCw3Ljg2NzEwOTAyIDU3LjIwMjU2Nyw5LjAwMjU5MTk5IDU5LjI5ODIxODEsMTAuMzc3MjQ2MiBMNTIuNjg2MTc3NywxNC41Nzc4MzY2IEw1Mi42ODYxNzc3LDYuOTc4OTI0NDggWiBNNjQuODc1Nzg0OCw4LjY4NTYzMjAyIEM1Ny43MzA4MzM2LDMuMDAzNTczMDYgNDkuMTI4Nzk1NSwwIDQwLjAwMDgxMjcsMCBDMTcuOTQ0ODEwNywwIDAsMTcuOTQzNjQ5NiAwLDM5Ljk5OTY1MTcgQzAsNjIuMDU1NjUzOCAxNy45NDQ4MTA3LDgwLjAwMDQ2NDQgNDAuMDAwODEyNyw4MC4wMDA0NjQ0IEM2Mi4wNTY4MTQ4LDgwLjAwMDQ2NDQgODAuMDAwNDY0NCw2Mi4wNTU2NTM4IDgwLjAwMDQ2NDQsMzkuOTk5NjUxNyBDODAuMDAwNDY0NCwyNy43NDUwMjcyIDc0LjQ4NzkxNTIsMTYuMzMyMTQ2MiA2NC44NzU3ODQ4LDguNjg1NjMyMDIgTDY0Ljg3NTc4NDgsOC42ODU2MzIwMiBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMTcxNzE3IiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==',

                /**
                 * How the URL structure should be handled with this post type.  You can set this to an
                 * array of specific arguments or true|false.  If set to FALSE, it will prevent rewrite
                 * rules from being created.
                 *
                 * Remove if not needed.
                 */
                'rewrite' => array(
                    /* The slug to use for individual posts of this type. */
                    'slug'       => 'wiki', // string (defaults to the post type name)
                    /* Whether to show the $wp_rewrite->front slug in the permalink. */
                    'with_front' => true, // bool (defaults to TRUE)
                    /* Whether to allow single post pagination via the <!--nextpage--> quicktag. */
                    'pages'      => true, // bool (defaults to TRUE)
                    /* Whether to create feeds for this post type. */
                    'feeds'      => true, // bool (defaults to the 'has_archive' argument)
                    /* Assign an endpoint mask to this permalink. */
                    'ep_mask'    => EP_PERMALINK, // const (defaults to EP_PERMALINK)
                ),

                /**
                 * The position in the menu order the post type should appear. 'show_in_menu' must be true
                 * for this to work.
                 *
                 * 2 Dashboard
                 * 4 Separator
                 * 5 Posts
                 * 10 Media
                 * 15 Links
                 * 20 Pages
                 * 25 Comments
                 * 59 Separator
                 * 60 Appearance
                 * 65 Plugins
                 * 70 Users
                 * 75 Tools
                 * 80 Settings
                 * 99 Separator
                 *
                 * @link https://wordpress.stackexchange.com/questions/8779/placing-a-custom-post-type-menu-above-the-posts-menu-using-menu-position/65823#65823
                 */
                'menu_position'         => 3,

                /**
                 * Whether the post type should be used publicly via the admin or by front-end users.  This
                 * argument is sort of a catchall for many of the following arguments.  I would focus more
                 * on adjusting them to your liking than this argument.
                 */
                'public'                => true,

                /**
                 * Whether queries can be performed on the front end as part of parse_request().
                 */
                'publicly_queryable'    => true,

                /**
                 * Whether to exclude posts with this post type from front end search results.
                 */
                'exclude_from_search'   => false,
                /**
                 * Whether to generate a default UI for managing this post type in the admin. You'll have
                 * more control over what's shown in the admin with the other arguments.  To build your
                 * own UI, set this to FALSE.
                 *
                 * You can hide menu with caps too:
                 */
                // 'show_ui'               => ( current_user_can( 'read_test' ) ) ? true : false,
                'show_ui'               => true,

                /**
                 * Whether to show post type in the admin menu. 'show_ui' must be true for this to work.
                 */
                'show_in_menu'          => true,

                /**
                 * Sets the query_var key for this post type. If set to TRUE, the post type name will be used.
                 * You can also set this to a custom string to control the exact key.
                 */
                'query_var'             => true,

                /**
                 * Whether to make this post type available in the WordPress admin bar. The admin bar adds
                 * a link to add a new post type item.
                 */
                'show_in_admin_bar'     => true,

                /**
                 * Whether individual post type items are available for selection in navigation menus.
                 */
                'show_in_nav_menus'     => true,

                /**
                 * What WordPress features the post type supports.  Many arguments are strictly useful on
                 * the edit post screen in the admin.  However, this will help other themes and plugins
                 * decide what to do in certain situations.  You can pass an array of specific features or
                 * set it to FALSE to prevent any features from being added.  You can use
                 * add_post_type_support() to add features or remove_post_type_support() to remove features
                 * later.  The default features are 'title' and 'editor'.
                 *
                 * https://codex.wordpress.org/Function_Reference/post_type_supports
                 */
                'supports'              => array(
                    'title',
                    'editor',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats'
                ),

                'capability_type'       => 'post',

                /**
                 * If you want to add custom capabilities to the post type.
                 * TODO: Messo su true genera un sacco di notice e warning e fa scomparire il post type dal wp-admin
                 */
                'custom_caps'           => false,

                /**
                 * Assign capabilities for roles.
                 */
                /*
                'custom_caps_users'     => array(
                    'administrator',
                    'editor',
                    'author',
                    'contributor',
                    'subscriber',
                ),
                */

                'taxonomies'            => [
                    'post_tag'
                ],
            ),

            /*
            array(
                'slug'                  => 'test',
                'singular'              => 'Test',
                'plural'                => 'Tests',
                'menu_name'             => 'Tests',
                'description'           => 'Tests',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_icon'             => 'dashicons-tag',
                'rewrite' => array(
                    'slug'                  => 'tests',
                    'with_front'            => true,
                    'pages'                 => true,
                    'feeds'                 => true,
                    'ep_mask'               => EP_PERMALINK,
                ),
                'menu_position'         => 21,
                'public'                => true,
                'publicly_queryable'    => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'supports'              => array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
                ),
                'custom_caps'           => true,
                'custom_caps_users'     => array(
                    'administrator',
                ),
                'taxonomies'            => array(
                    array(
                        'taxonomy'          => 'test_category',
                        'plural'            => 'Test Categories',
                        'single'            => 'Test Category',
                        'post_types'        => array( 'test' ),
                    ),
                ),
            ),
            */

        );

        foreach ( $post_types_fields as $fields ) {

            $this->register_single_post_type( $fields );

        }

    }

    function pagination_rewrite() {
        add_rewrite_rule('wiki/page/?([0-9]{1,})/?$', 'index.php?post_type=wiki&paged=$matches[1]', 'top');
    }

}