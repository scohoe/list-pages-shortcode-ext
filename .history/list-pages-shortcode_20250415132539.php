<?php

	/**
	 * Plugin Name: List Pages Shortcode Extended
	 * Plugin URI: http://wordpress.org/extend/plugins/list-pages-shortcode/
	 * Description: Extended version with dropdown functionality. Introduces the [list-pages], [sibling-pages] and [child-pages] <a href="http://codex.wordpress.org/Shortcode_API">shortcodes</a> for easily displaying a list of pages within a post or page. Both shortcodes accept all parameters that you can pass to the <a href="http://codex.wordpress.org/Template_Tags/wp_list_pages">wp_list_pages()</a> function.
	 * Author: Scott Hoenes
	 * Version: 1.0.0
	 * License: GPLv2 or later
	 * Author URI: http://www.scohoe.com
	 */

	add_shortcode( 'child-pages', array( 'List_Pages_Shortcode', 'shortcode_list_pages' ) );
	add_shortcode( 'sibling-pages', array( 'List_Pages_Shortcode', 'shortcode_list_pages' ) );
	add_shortcode( 'list-pages', array( 'List_Pages_Shortcode', 'shortcode_list_pages' ) );
	add_filter( 'list_pages_shortcode_excerpt', array( 'List_Pages_Shortcode', 'excerpt_filter' ) );

	class List_Pages_Shortcode {

		public function __construct() {
			// Initialize assets for dropdown functionality
			add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
			
			// Add admin menu and settings
			add_action('admin_menu', array($this, 'add_admin_menu'));
			add_action('admin_init', array($this, 'init_settings'));
		}

		public function List_Pages_Shortcode() {
			// @todo  Deprecate use of PHP4 constructor
			$this->__construct();
		}

		public function add_admin_menu() {
			add_options_page(
				'List Pages Shortcode CSS',
				'List Pages Shortcode CSS',
				'manage_options',
				'list-pages-shortcode-css',
				'list_pages_shortcode_settings_page'
			);
		}

		public function init_settings() {
			require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
			list_pages_shortcode_register_settings();
		}
		
		public function enqueue_assets() {
			wp_enqueue_style(
				'list-pages-shortcode-dropdown',
				plugins_url('css/dropdown.css', __FILE__)
			);
			wp_enqueue_script(
				'list-pages-shortcode-dropdown',
				plugins_url('js/dropdown.js', __FILE__),
				array('jquery'),
				'1.0',
				true
			);

			// Enqueue custom CSS if it exists
			$custom_css = get_option('list_pages_shortcode_custom_css');
			if (!empty($custom_css)) {
				wp_add_inline_style('list-pages-shortcode-dropdown', $custom_css);
			}
		}

		public static function shortcode_list_pages( $atts, $content, $tag ) {

			global $post;

			$atts = self::parse_shortcode_atts( $atts, $tag );
			$atts = apply_filters( 'shortcode_list_pages_attributes', $atts, $content, $tag );

			do_action( 'shortcode_list_pages_before', $atts, $content, $tag );

			// Catch <ul> tags in wp_list_pages().
			if ( 'ul' !== $atts['list_type'] ) {
				add_filter( 'wp_list_pages', array( 'List_Pages_Shortcode', 'ul2list_type' ), 10, 2 );
			}

			// Create output.
			$atts = self::prepare_list_pages_atts( $atts, $tag );
			$out  = wp_list_pages( $atts );
			remove_filter( 'wp_list_pages', array( 'List_Pages_Shortcode', 'ul2list_type' ), 10 );
			if ( ! empty( $out ) && ! empty( $atts['list_type'] ) ) {
				$out = '<' . sanitize_key( $atts['list_type'] ) . ' class="' . esc_attr( $atts['class'] ) . '">' . $out . '</' . sanitize_key( $atts['list_type'] ) . '>';
			}
			$out = apply_filters( 'shortcode_list_pages', $out, $atts, $content, $tag );

			do_action( 'shortcode_list_pages_after', $atts, $content, $tag );

			return $out;

		}

		protected static function parse_shortcode_atts( $atts, $tag ) {

			// Allowed shortcode attributes.
			$defaults = array(
				'class'                => 'list-pages-shortcode ' . $tag,
				'depth'                => 0,
				'show_date'            => '',
				'date_format'          => get_option( 'date_format' ),
				'exclude'              => '',
				'include'              => '',
				'child_of'             => self::get_default_child_of( $tag ),
				'list_type'            => 'ul',
				'title_li'             => '',
				'authors'              => '',
				'sort_column'          => 'menu_order, post_title',
				'sort_order'           => '',
				'link_before'          => '',
				'link_after'           => '',
				'exclude_tree'         => '',
				'meta_key'             => '',
				'meta_value'           => '',
				'post_type'            => 'page',
				'offset'               => '',
				'exclude_current_page' => 0,
				'excerpt'              => 0,
			);

			$atts = shortcode_atts( $defaults, $atts );

			// Validate/sanitize attributes.
			$atts['class']                = self::sanitize_html_classes( $atts['class'] );
			$atts['depth']                = absint( $atts['depth'] );
			$atts['show_date']            = sanitize_text_field( $atts['show_date'] );
			$atts['date_format']          = sanitize_text_field( $atts['date_format'] );
			$atts['exclude']              = '' !== $atts['exclude'] ? self::sanitize_absints_string( $atts['exclude'] ) : '';
			$atts['include']              = '' !== $atts['include'] ? self::sanitize_absints_string( $atts['include'] ) : '';
			$atts['child_of']             = absint( $atts['child_of'] );
			$atts['list_type']            = self::validate_list_type( $atts['list_type'] );
			$atts['title_li']             = '' !== $atts['title_li'] ? wp_kses_post( $atts['title_li'] ) : '';
			$atts['authors']              = '' !== $atts['authors'] ? self::sanitize_absints_string( $atts['authors'] ) : '';
			$atts['sort_column']          = self::validate_sort_column( $atts['sort_column'] );
			$atts['sort_order']           = 'DESC' === strtoupper( $atts['sort_order'] ) ? 'DESC' : 'ASC';
			$atts['link_before']          = '' !== $atts['link_before'] ? wp_kses_post( $atts['link_before'] ) : '';
			$atts['link_after']           = '' !== $atts['link_after'] ? wp_kses_post( $atts['link_after'] ) : '';
			$atts['exclude_tree']         = '' !== $atts['exclude_tree'] ? self::sanitize_absints_string( $atts['exclude_tree'] ) : '';
			$atts['meta_key']             = sanitize_key( $atts['meta_key'] );
			$atts['meta_value']           = sanitize_text_field( $atts['meta_value'] );
			$atts['post_type']            = self::validate_post_type( $atts['post_type'] );
			$atts['offset']               = absint( $atts['offset'] );
			$atts['exclude_current_page'] = absint( $atts['exclude_current_page'] );
			$atts['excerpt']              = absint( $atts['excerpt'] );

			// Extra attributes.
			$atts['walker'] = new List_Pages_Shortcode_Walker_Page();

			return $atts;

		}

		protected static function prepare_list_pages_atts( $atts, $tag ) {

			global $post;

			// Don't echo list pages output.
			$atts['echo'] = 0;

			// Exclude current page.
			if ( $atts['exclude_current_page'] && absint( $post->ID ) ) {
				if ( ! empty( $atts['exclude'] ) ) {
					$atts['exclude'] .= ',';
				}
				$atts['exclude'] .= absint( $post->ID );
			}

			return $atts;

		}

		protected static function sanitize_html_classes( $class ) {

			$class = array_map( 'sanitize_html_class', explode( ' ', $class ) );

			return implode( ' ', $class );

		}

		protected static function sanitize_absints_string( $value ) {

			if ( '' !== $value ) {
				$values = explode( ',', $value );
				$values = array_map( 'absint', array_map( 'trim', $values ) );
				return implode( ',', array_filter( $values ) );
			}

			return $value;

		}

		protected static function validate_sort_column( $sort_column ) {

			$valid_columns = array(
				'post_author',
				'post_date',
				'post_title',
				'post_name',
				'post_modified',
				'post_modified_gmt',
				'menu_order',
				'post_parent',
				'ID',
				'rand',
				'comment_count',
				'post_title',
			);

			$sort_columns = explode( ',', $sort_column );
			$sort_columns = array_map( 'trim', $sort_columns );

			$validated_columns = array_intersect( $sort_columns, $valid_columns );

			if ( ! empty( $validated_columns ) ) {
				return implode( ',', $validated_columns );
			}

			return 'post_title';

		}

		protected static function validate_post_type( $post_type ) {

			$post_type = sanitize_key( $post_type );

			if ( post_type_exists( $post_type ) && is_post_type_viewable( $post_type ) ) {
				return $post_type;
			}

			return '';

		}

		protected static function get_default_child_of( $tag ) {

			$post = get_post();

			if ( !$post ) {
				return 0;
			}

			if ( 'child-pages' === $tag ) {
				return $post->ID;
			} elseif ( 'sibling-pages' === $tag ) {
				// Only return post_parent if it exists, otherwise return 0
				return isset($post->post_parent) && $post->post_parent > 0 ? $post->post_parent : 0;
			}

			return 0;

		}

		/**
		 * UL 2 List Type
		 * Replaces all <ul> tags with <{list_type}> tags.
		 *
		 * @param  string $output Output of wp_list_pages().
		 * @param  array  $args shortcode_list_pages() args.
		 * @return string HTML output.
		 */
		public static function ul2list_type( $output, $args = null ) {

			$list_type = self::validate_list_type( $args['list_type'] );

			if ( 'ul' !== $list_type ) {

				// <ul>
				$output = str_replace( '<ul>', '<' . $list_type . '>', $output );
				$output = str_replace( '<ul ', '<' . $list_type . ' ', $output );
				$output = str_replace( '</ul> ', '</' . $list_type . '>', $output );

				// <li>
				if ( 'ol' !== $list_type ) {
					$list_type = 'span' === $list_type ? 'span' : 'div';
					$output    = str_replace( '<li>', '<' . $list_type . '>', $output );
					$output    = str_replace( '<li ', '<' . $list_type . ' ', $output );
					$output    = str_replace( '</li> ', '</' . $list_type . '>', $output );
				}

			}

			return $output;

		}

		/**
		 * Excerpt Filter
		 * Add a <div> around the excerpt by default.
		 *
		 * @param string $text Excerpt.
		 * @return string Filtered excerpt.
		 */
		public static function excerpt_filter( $text ) {
			if ( ! empty( $text ) ) {
				return ' <div class="excerpt">' . wp_kses( $text, 'post' ) . '</div>';
			}
			return $text;
		}

		/**
		 * Validate List Type
		 *
		 * @param  string $list_type  List type tag.
		 * @return string              Valid tag.
		 */
		public static function validate_list_type( $list_type ) {

			if ( ! empty( $list_type ) && in_array( $list_type, array( 'ul', 'div', 'span', 'article', 'aside', 'section' ), true ) ) {
				return $list_type;
			}

			return 'ul';

		}

	}

	/**
	 * Create HTML list of pages.
	 * A copy of the WordPress Walker_Page class which adds an excerpt.
	 */
	class List_Pages_Shortcode_Walker_Page extends Walker_Page {

		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent    = str_repeat( "\t", $depth );
			$list_type = List_Pages_Shortcode::validate_list_type( $args['list_type'] );
			$output   .= "\n$indent<" . $list_type . " class='children dropdown-content' role='menu' aria-hidden='true'>\n";
		}

		public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
			if ( $depth ) {
				$indent = str_repeat( "\t", $depth );
			} else {
				$indent = '';
			}

			$css_class = array( 'page_item', 'page-item-' . $page->ID );

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'page_item_has_children';
			}

			if ( ! empty( $current_page ) ) {
				$_current_page = get_page( $current_page );
				if ( in_array( $page->ID, $_current_page->ancestors, true ) ) {
					$css_class[] = 'current_page_ancestor';
				}
				if ( $page->ID === $current_page ) {
					$css_class[] = 'current_page_item';
				} elseif ( $_current_page && $page->ID === $_current_page->post_parent ) {
					$css_class[] = 'current_page_parent';
				}
			} elseif ( absint( get_option( 'page_for_posts' ) ) === $page->ID ) {
				$css_class[] = 'current_page_parent';
			}

			$filtered_css_class = apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page );
			$css_class = is_array($filtered_css_class) ? implode( ' ', $filtered_css_class ) : (string)$filtered_css_class;

			if ( '' === $page->post_title ) {
				$page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );
			}

			$args['link_before'] = empty( $args['link_before'] ) ? '' : wp_kses_post( $args['link_before'] );
			$args['link_after']  = empty( $args['link_after'] ) ? '' : wp_kses_post( $args['link_after'] );

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$item = '<a href="' . get_permalink( $page->ID ) . '" class="dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0">' . 
						$args['link_before'] . 
						apply_filters( 'the_title', $page->post_title, $page->ID ) . 
						$args['link_after'] . 
						'</a>';
				$output .= $indent . '<li class="' . esc_attr( $css_class ) . '">' . apply_filters( 'list_pages_shortcode_item', $item, $page, $depth, $args, $current_page );
			} else {
				$item = '<a href="' . get_permalink( $page->ID ) . '" role="menuitem" tabindex="-1">' . 
						$args['link_before'] . 
						apply_filters( 'the_title', $page->post_title, $page->ID ) . 
						$args['link_after'] . 
						'</a>';
			}

			if ( ! empty( $args['show_date'] ) ) {
				if ( 'modified' === $args['show_date'] ) {
					$time = $page->post_modified;
				} else {
					$time = $page->post_date;
				}

				$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
				$item       .= ' ' . mysql2date( $date_format, $time );
			}

			// Excerpt.
			if ( $args['excerpt'] ) {
				$item .= apply_filters( 'list_pages_shortcode_excerpt', $page->post_excerpt, $page, $depth, $args, $current_page );
			}

			$output .= $indent . '<li class="' . esc_attr( $css_class ) . '">' . apply_filters( 'list_pages_shortcode_item', $item, $page, $depth, $args, $current_page );
		}

	}

	// @todo  Deprecate instance
	global $List_Pages_Shortcode;
	$List_Pages_Shortcode = new List_Pages_Shortcode();
