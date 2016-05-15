<?php
/**
 * Plugin Name: Vales SEO
 * Description: Silence is golden.
 * Version: 1.0.0
 * Author: Vales, Inc.
 * Author URI: https://valesdigital.com/
 */


class Vales_Seo {

	const PLUGIN_NAME = 'Vales SEO';
	const PLUGIN_SLUG = 'vales_seo';

	private static $separators = array(
		'dash'   => '-',
		'ndash'  => '&ndash;',
		'mdash'  => '&mdash;',
		'middot' => '&middot;',
		'bull'   => '&bull;',
		'star'   => '*',
		'smstar' => '&#8902;',
		'pipe'   => '|',
		'tilde'  => '~',
		'laquo'  => '&laquo;',
		'raquo'  => '&raquo;',
		'lt'     => '&lt;',
		'gt'     => '&gt;',
	);

	private static $separators_selector = array(
		'dash'   => '<code>-</code>',
		'ndash'  => '<code>&ndash;</code>',
		'mdash'  => '<code>&mdash;</code>',
		'middot' => '<code>&middot;</code>',
		'bull'   => '<code>&bull;</code>',
		'star'   => '<code>*</code>',
		'smstar' => '<code>&#8902;</code>',
		'pipe'   => '<code>|</code>',
		'tilde'  => '<code>~</code>',
		'laquo'  => '<code>&laquo;</code>',
		'raquo'  => '<code>&raquo;</code>',
		'lt'     => '<code>&lt;</code>',
		'gt'     => '<code>&gt;</code>',
	);

	public static function init() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'all_admin_notices', function() {
				echo '<div class="notice"><p>' . sprintf( __( 'Please install and activate %s plugin.', static::PLUGIN_SLUG ), '<a href="http://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields Pro</a>' ) . '</p></div>';
			} );
			return;
		}
		add_action( 'plugins_loaded'                , array( get_class(), 'init_l10n'                       ) );
		add_action( 'init'                          , array( get_class(), 'register_advanced_custom_fields' ), 30 );
		add_action( 'init'                          , array( get_class(), 'remove_original_title_tag' )  );
		add_action( 'wp_head'                       , array( get_class(), 'output_global_head_code' ) );
		add_action( 'wp_footer'                     , array( get_class(), 'output_global_foot_code' ) );
		add_action( 'wp_head'                       , array( get_class(), 'output_page_title' ), 1 );
		add_action( 'wp_head'                       , array( get_class(), 'output_page_description' ), 1 );
		/*
		add_action( 'wp_head'                       , array( get_class(), 'output_page_keywords' ), 1 );
		*/
		add_filter( 'wp_title'                      , array( get_class(), 'filter_page_title' ), 100 );
		add_action( 'admin_footer'                  , array( get_class(), 'admin_scripts' ) );
		add_action( 'wp_ajax_' . static::PLUGIN_SLUG, array( get_class(), 'ajax_handler' ) );
	}

	public static function init_l10n() {
		return load_plugin_textdomain( static::PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	public static function register_advanced_custom_fields() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		acf_add_options_page( array(
			'page_title'  => static::PLUGIN_NAME,
			'menu_title'  => static::PLUGIN_NAME,
			'menu_slug'   => static::PLUGIN_SLUG . '_settings',
			'parent_slug' => 'options-general.php',
		) );
		$fields = array();
		$fields[] = array(
			'key'   => 'field_wla6g9e8qlnpmukx',
			'label' => 'General',
			'name'  => static::PLUGIN_SLUG . '_general_tab',
			'type'  => 'tab',
		);
		$fields[] = array(
			'key'           => 'field_ngofp6kcqbnbulxx',
			'label'         => 'Global Separator',
			'name'          => static::PLUGIN_SLUG . '_global_separator',
			'type'          => 'radio',
			'required'      => true,
			'choices'       => static::$separators_selector,
			'default_value' => 'fuck',
			'layout'        => 'horizontal',
		);
		$fields[] = array(
			'key'   => 'field_clenwyt2mcuatqus',
			'label' => 'Global Additional HTML Head Code',
			'name'  => static::PLUGIN_SLUG . '_global_head_code',
			'type'  => 'textarea',
			'rows'  => '5',
		);
		$fields[] = array(
			'key'   => 'field_nwbt3v1ftw8gdbua',
			'label' => 'Global Additional HTML Foot Code',
			'name'  => static::PLUGIN_SLUG . '_global_foot_code',
			'type'  => 'textarea',
			'rows'  => '5',
		);
		$fields[] = array(
			'key'   => 'field_wgbcmhobapadp45s',
			'label' => 'Homepage',
			'name'  => static::PLUGIN_SLUG . '_home_tab',
			'type'  => 'tab',
		);
		$fields[] = array(
			'key'           => 'field_nusjqkhrg2x9ly7q',
			'label'         => 'Page Title',
			'name'          => static::PLUGIN_SLUG . '_home_title',
			'type'          => 'text',
			'default_value' => '{{sitename}} {{sep}} {{sitedesc}}',
		);
		$fields[] = array(
			'key'           => 'field_y6uxr6juyemgb9fa',
			'label'         => 'Page Description',
			'name'          => static::PLUGIN_SLUG . '_home_description',
			'type'          => 'textarea',
			'rows'          => '3',
			'default_value' => '{{sitedesc}}',
		);
		/*
		$fields[] = array(
			'key'   => 'field_tguhv4v1wdb2aceb',
			'label' => 'Page Keywords',
			'name'  => static::PLUGIN_SLUG . '_home_keywords',
			'type'  => 'text',
		);
		*/
		$fields[] = array(
			'key'   => 'field_dnizrxtvaamqsvmj',
			'label' => 'Search',
			'name'  => static::PLUGIN_SLUG . '_search_tab',
			'type'  => 'tab',
		);
		$fields[] = array(
			'key'   => 'field_bev7eetqzxloimnn',
			'label' => 'Page Title',
			'name'  => static::PLUGIN_SLUG . '_search_title',
			'type'  => 'text',
		);
		$fields[] = array(
			'key'   => 'field_e7dccv8eerqultvc',
			'label' => 'Page Description',
			'name'  => static::PLUGIN_SLUG . '_search_description',
			'type'  => 'textarea',
			'rows'  => '3',
		);
		/*
		$fields[] = array(
			'key'   => 'field_jgijy5gk1d302wlf',
			'label' => 'Page Keywords',
			'name'  => static::PLUGIN_SLUG . '_search_keywords',
			'type'  => 'text',
		);
		*/
		$fields[] = array(
			'key'   => 'field_k1nguftvrlhigfvz',
			'label' => '404 Page',
			'name'  => static::PLUGIN_SLUG . '_404_tab',
			'type'  => 'tab',
		);
		$fields[] = array(
			'key'   => 'field_lprfcd04gzzlmpdc',
			'label' => 'Page Title',
			'name'  => static::PLUGIN_SLUG . '_404_title',
			'type'  => 'text',
		);
		$fields[] = array(
			'key'   => 'field_gv5e4wkl8njceb9m',
			'label' => 'Page Description',
			'name'  => static::PLUGIN_SLUG . '_404_description',
			'type'  => 'textarea',
			'rows'  => '3',
		);
		/*
		$fields[] = array(
			'key'   => 'field_hu48dygia2yrt5rv',
			'label' => 'Page Keywords',
			'name'  => static::PLUGIN_SLUG . '_404_keywords',
			'type'  => 'text',
		);
		*/
		foreach ( $post_types as $post_type_object ) {
			$fields[] = array(
				'key'   => static::generate_acf_field_id( $post_type_object->name ),
				'label' => $post_type_object->labels->name,
				'name'  => static::PLUGIN_SLUG . '_post_type_tab_' . $post_type_object->name,
				'type'  => 'tab',
			);
			$fields[] = array(
				'key'           => static::generate_acf_field_id( $post_type_object->name . '_archive_title' ),
				'label'         => 'Archive Page: Page Title',
				'name'          => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_archive_title',
				'type'          => 'text',
				'default_value' => '{{posttype}} {{sep}} {{sitename}}',
			);
			$fields[] = array(
				'key'   => static::generate_acf_field_id( $post_type_object->name . '_archive_description' ),
				'label' => 'Archive Page: Page Description',
				'name'  => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_archive_description',
				'type'  => 'textarea',
				'rows'  => '3',
			);
			/*
			$fields[] = array(
				'key'   => static::generate_acf_field_id( $post_type_object->name . '_archive_keywords' ),
				'label' => 'Archive Page: Page Keywords',
				'name'  => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_archive_keywords',
				'type'  => 'text',
			);
			*/
			$fields[] = array(
				'key'           => static::generate_acf_field_id( $post_type_object->name . '_single_pattern_title' ),
				'label'         => 'Single Object: Page Title Pattern',
				'name'          => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_single_pattern_title',
				'type'          => 'text',
				'default_value' => '{{title}} {{sep}} {{posttype}} {{sep}} {{sitename}}',
			);
			$fields[] = array(
				'key'   => static::generate_acf_field_id( $post_type_object->name . '_single_pattern_description' ),
				'label' => 'Single Object: Page Description Pattern',
				'name'  => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_single_pattern_description',
				'type'  => 'textarea',
				'rows'  => '3',
			);
			/*
			$fields[] = array(
				'key'   => static::generate_acf_field_id( $post_type_object->name . '_single_keywords' ),
				'label' => 'Single Object: Page Keywords Pattern',
				'name'  => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_single_keywords',
				'type'  => 'text',
			);
			*/
			$fields[] = array(
				'key'           => static::generate_acf_field_id( $post_type_object->name . '_enabled_singular_meta' ),
				'label'         => 'Single Object Meta Editor',
				'name'          => static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_enabled_singular_meta',
				'type'          => 'true_false',
				'message'       => 'Enabled',
				'default_value' => 1,
			);
		}
		$fields[] = array(
			'key'   => 'field_eclzbibamucaj80r',
			'label' => 'Help',
			'name'  => static::PLUGIN_SLUG . '_help_tab',
			'type'  => 'tab',
		);
		$fields[] = array(
			'key'     => 'field_3lbz9wwie5981brr',
			'label'   => 'Pattern Variables',
			'name'    => '',
			'type'    => 'message',
			'message' => (
<<< 'EOF'
<table>
	<tbody>
		<tr>
			<td><code>{{sitename}}</code></td>
			<td>The name of website</td>
		</tr>
		<tr>
			<td><code>{{sitedesc}}</code></td>
			<td>The description of website</td>
		</tr>
		<tr>
			<td><code>{{title}}</code></td>
			<td>The title of current object</td>
		</tr>
		<tr>
			<td><code>{{posttype}}</code></td>
			<td>The name of current post type</td>
		</tr>
		<tr>
			<td><code>{{searchquery}}</code></td>
			<td>The query word to search</td>
		</tr>
		<tr>
			<td><code>{{sep}}</code></td>
			<td>The separator for title</td>
		</tr>
	</tbody>
</table>
EOF
			),
		);
		$fields[] = array(
			'key'     => 'field_v5lzyh6m6gtohblx',
			'label'   => 'Restore Default Settings',
			'name'    => '',
			'type'    => 'message',
			'message' => (
<<< 'EOF'
<p>The restoration will permanently delete all user settings in Vales SEO plugin. This operation cannot be undone.</p>
<a class="button-secondary" href="javascript:void(0);" id="vales_seo_js_restore_settings">Yes, please reset all settings for me</a>
EOF
			),
		);
		acf_add_local_field_group( array(
			'key'    => 'group_embzfgfv41y8mcsc',
			'style'  => 'seamless',
			'title'  => '_',
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => static::PLUGIN_SLUG . '_settings',
					),
				),
			),
		) );
		$locations = array();
		foreach ( $post_types as $post_type_object ) {
			// enabled meta box
			$enabled = get_field( static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_enabled_singular_meta', 'option' );
			if ( ! $enabled ) {
				continue;
			}
			$locations[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type_object->name,
				),
			);
		}
		acf_add_local_field_group( array(
			'key'    => 'group_tjvzubkgk4ve7lc6',
			'title'  => static::PLUGIN_NAME,
			'style'  => 'default',
			'fields' => array(
				array(
					'key'   => 'field_e847ze6rxv4b95r6',
					'label' => 'Custom Page Title',
					'name'  => static::PLUGIN_SLUG . '_singular_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_nbk2qdaxoed1ssp8',
					'label' => 'Custom Page Description',
					'name'  => static::PLUGIN_SLUG . '_singular_description',
					'type'  => 'textarea',
					'rows'  => '3',
				),
				/*
				array(
					'key'   => 'field_cdgkinsjdvkcjnm6',
					'label' => 'Custom Page Keywords',
					'name'  => static::PLUGIN_SLUG . '_singular_keywords',
					'type'  => 'text',
				),
				*/
			),
			'location' => $locations,
		) );
	}

	private static function generate_acf_field_id( $slug ) {
		return 'field_' . substr( md5( $slug ), 0, 16 );
	}

	public static function remove_original_title_tag() {
		if ( current_theme_supports( 'title-tag' ) ) {
			remove_action( 'wp_head', '_wp_render_title_tag', 1 );
		}
	}

	public static function output_global_head_code() {
		$content = get_field( static::PLUGIN_SLUG . '_global_head_code', 'option' );
		if ( ! empty( $content ) ) {
			echo $content;
		}
	}

	public static function output_global_foot_code() {
		$content = get_field( static::PLUGIN_SLUG . '_global_foot_code', 'option' );
		if ( ! empty( $content ) ) {
			echo $content;
		}
	}

	public static function output_page_title() {
		if ( ! current_theme_supports( 'title-tag' ) ) {
			return;
		}
		$content = static::get_current_page_meta( 'title' );
		if ( ! empty( $content ) ) {
			echo '<title>' . static::escape_page_title( $content ) . '</title>' . PHP_EOL;
		} elseif ( function_exists( 'wp_get_document_title' ) ) {
			echo '<title>' . wp_get_document_title() . '</title>' . PHP_EOL;
		}
	}

	public static function filter_page_title( $title ) {
		$content = static::get_current_page_meta( 'title' );
		if ( ! empty( $content ) ) {
			$title = static::escape_page_title( $content );
		}
		return $title;
	}

	private static function escape_page_title( $text ) {
		$text = wptexturize( $text );
		$text = convert_chars( $text );
		$text = esc_html( $text );
		$text = capital_P_dangit( $text );
		return $text;
	}

	public static function output_page_description() {
		$content = static::get_current_page_meta( 'description' );
		if ( ! empty( $content ) ) {
			printf( '<meta name="description" content="%s" />' . PHP_EOL, $content );
		}
	}

	public static function output_page_keywords() {
		$content = static::get_current_page_meta( 'keywords' );
		if ( ! empty( $content ) ) {
			printf( '<meta name="keywords" content="%s" />' . PHP_EOL, $content );
		}
	}

	private static function get_current_page_meta( $meta_name ) {
		$content = null;
		if ( is_404() ) {
			$content = get_field( static::PLUGIN_SLUG . '_404_' . $meta_name, 'option' );
		} elseif ( is_search() ) {
			$content = get_field( static::PLUGIN_SLUG . '_search_' . $meta_name, 'option' );
		} elseif ( is_front_page() || is_home() ) {
			$content = get_field( static::PLUGIN_SLUG . '_home_' . $meta_name, 'option' );
		} elseif ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$post_type_object = get_post_type_object( $post_type );
			$content = get_field( static::PLUGIN_SLUG . '_post_type_' . $post_type_object->name . '_archive_' . $meta_name, 'option' );
		} elseif ( is_tax() ) {
			// TODO
		} elseif ( is_singular() ) {
			// get single meta
			$_post = get_queried_object();
			$content = get_field( static::PLUGIN_SLUG . '_singular_' . $meta_name, $_post->ID );
			if ( empty( $content ) ) {
				// fallback to get pattern
				$content = get_field( static::PLUGIN_SLUG . '_post_type_' . $_post->post_type . '_single_pattern_' . $meta_name, 'option' );
			}
		}
		// pagination
		if ( ! empty( $content ) ) {
			global $page, $paged;
			if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
				$content = sprintf( __( 'Page %s', static::PLUGIN_SLUG ), max( $paged, $page ) ) . ' {{sep}} ' . $content;
			}
		}
		return static::apply_varibles_for_pattern( $content );
	}

	private static function apply_varibles_for_pattern( $pattern ) {
		if ( empty( $pattern ) ) {
			return $pattern;
		}
		// prepare  current post type name
		if ( is_singular() ) {
			$post_type_object = get_post_type_object( get_post_type() );
			$post_type_name = $post_type_object->labels->name;
		} else {
			$post_type_name = post_type_archive_title( '', false );
		}
		// prepare  separator string
		$separator = static::$separators['dash'];
		$separator_user = get_field( static::PLUGIN_SLUG . '_global_separator', 'option' );
		if ( array_key_exists( $separator_user, static::$separators ) ) {
			$separator = static::$separators[ $separator_user ];
		}
		// apply
		$content = str_replace(
			array(
				'{{sitename}}',
				'{{sitedesc}}',
				'{{title}}',
				'{{posttype}}',
				'{{searchquery}}',
				'{{sep}}',
			),
			array(
				get_bloginfo( 'name' ),
				get_bloginfo( 'description' ),
				single_post_title( '', false ),
				$post_type_name,
				get_query_var( 's' ),
				$separator,
			),
			$pattern
		);
		return $content;
	}

	public static function admin_scripts() {
		echo <<< 'EOF'
<script type="text/javascript">
	jQuery(document).ready( function () {
		jQuery(document).on( 'click', '#vales_seo_js_restore_settings', function (e) {
			if ( window.confirm( 'Are you sure?' ) ) {
				jQuery.ajax( {
					'url' : ajaxurl,
					'method' : 'POST',
					'data' : {
						'action' : 'vales_seo',
						'do' : 'restore_settings',
					},
					'success' : function ( res ) {
						alert( 'Done.' );
						window.location.reload();
					},
				} );
			}
		} );
	} )
</script>
EOF;
	}

	public static function ajax_handler() {
		if ( ! isset( $_POST['do'] ) ) {
			return false;
		}
		header( 'Content-Type: application/json; charset=utf-8' );
		switch ( $_POST['do'] ) {
			case 'restore_settings' :
				global $wpdb;
				// delete wp options
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE %s", '%' . $wpdb->esc_like( 'options_' . static::PLUGIN_SLUG . '_' ) . '%' ) );
				// delete post meta
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE `meta_key` LIKE %s", '%' . $wpdb->esc_like( static::PLUGIN_SLUG . '_singular_' ) . '%' ) );
				break;
		}
		wp_die();
	}

}

Vales_Seo::init();

