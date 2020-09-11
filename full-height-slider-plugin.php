<?php
/**
 * Plugin Name: Full Height Slider
 * Version: 1.0.1
 * Author: Marta Torre
 * Author URI: https://martatorre.dev
 */

defined('ABSPATH') or die('No script kiddies please!');

// Plugin path and url
define('FULL_HEIGHT_SLIDER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FULL_HEIGHT_SLIDER_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!class_exists('acf_pro')) {
  // Include ACF PRO
  include_once(FULL_HEIGHT_SLIDER_PLUGIN_PATH . 'includes/acf/acf.php');


  // Customize the url setting to fix incorrect asset URLs.
  function full_height_slider_plugin_acf_settings_url( $url ) {
      return FULL_HEIGHT_SLIDER_PLUGIN_URL . 'includes/acf/';
  }

  add_filter('acf/settings/url', 'full_height_slider_plugin_acf_settings_url');


  // Hide the ACF admin menu item.
  function full_height_slider_plugin_acf_show_admin($show_admin) {
      return false;
  }

  add_filter('acf/settings/show_admin', 'full_height_slider_plugin_acf_show_admin');
}




// Register CPT
function full_height_slider_plugin_register_cpt() {
  $labels = array(
    'name'               => 'Proyectos',
    'singular_name'      => 'Proyecto',
    'menu_name'          => 'Proyectos',
    'name_admin_bar'     => 'Proyectos',
    'all_items'          => 'Todos los proyectos',
    'add_new_item'       => 'Añadir nuevo proyecto',
    'add_new'            => 'Nuevo proyecto',
    'edit_item'          => 'Editar proyecto',
    'update_item'        => 'Actualizar proyecto',
    'view_item'          => 'Ver proyecto',
    'view_items'         => 'Ver proyectos',
    'search_items'       => 'Buscar proyectos',
    'not_found'          => 'No se han encontrado proyectos',
    'not_found_in_trash' => 'No se han encontrado proyectos en la papelera'
  );

  $args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => false, 
		'menu_icon'          => 'dashicons-portfolio', 
		'rewrite'            => array('slug' => 'slider_project'),
		'query_var'          => true,
		'menu_position'      => 5,
		'capability_type'    => 'post',
    'publicly_queryable' => false,
    'supports'           => array('custom-fields', 'title'),
		'hierarchical'       => false
	);

  register_post_type('slider_project', $args);
}
  
add_action('init', 'full_height_slider_plugin_register_cpt');


function full_height_slider_plugin_create_taxonomy() {
  $labels = array(
    'name'              => 'Categorías',
    'singular_name'     => 'Categoría',
    'search_items'      => 'Buscar por categoría',
    'all_items'         => 'Todas las categorías',
    'parent_item'       => 'Categoría padre',
    'parent_item_colon' => 'Categoría padre:',
    'edit_item'         => 'Editar categoría',
    'update_item'       => 'Actualizar categoría',
    'add_new_item'      => 'Añadir nueva categoría',
    'new_item_name'     => 'Nombre de la nueva categoría',
  );
  
  register_taxonomy('project_category', 'slider_project', array(
    'hierarchical' => true,
    'labels'       => $labels,
    'show_ui'      => true,
    'query_var'    => true,
    'rewrite'      => array('slug' => 'project_category'),
    'default_term' => 'General'
  ));  
}

add_action('init', 'full_height_slider_plugin_create_taxonomy');  


// Register ACF fields
function full_height_slider_plugin_register_custom_acf_fields() {
	if (function_exists('acf_add_local_field_group')) {
		// ACF Group: Project details
		acf_add_local_field_group(
      array(
        'key'      => 'group_project_details',
        'title'    => 'Detalles del proyecto',
        'location' => array(
          array(
            array(
              'param'    => 'post_type',
              'operator' => '==',
              'value'    => 'slider_project'
            )
          )
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => ''
      )
    );

		// Project desctription
		acf_add_local_field(
      array(
        'key'         => 'field_project_description',
        'label'       => 'Descripcion del proyecto',
        'name'        => 'project_description',
        'type'        => 'textarea',
        'parent'      => 'group_project_details',
        'required'    => 1,
        'placeholder' => 'Describe el proyecto...'
      )
    );

		// Project images
		acf_add_local_field(
      array(
        'key'      => 'field_project_gallery',
        'label'    => 'Imagenes del proyecto',
        'name'     => 'project_gallery',
        'type'     => 'gallery',
        'parent'   => 'group_project_details',
        'required' => 1
      )
    );
  }
}

add_action('acf/init', 'full_height_slider_plugin_register_custom_acf_fields');


// Register and enqueue styles
function full_height_slider_plugin_register_styles() {
  wp_register_style(
    'full-height-slider-swiper-styles',
    'https://unpkg.com/swiper/swiper-bundle.min.css'
  );

  wp_register_style(
    'full-height-slider-plugin-styles',
    FULL_HEIGHT_SLIDER_PLUGIN_URL . 'assets/css/main.min.css'
  );
}

add_action('wp_enqueue_scripts', 'full_height_slider_plugin_register_styles');


// Register and enqueue scripts
function full_height_slider_plugin_register_scripts() {
  wp_register_script(
    'full-height-slider-swiper-script',
    'https://unpkg.com/swiper/swiper-bundle.min.js',
    array(),
    '1.0.0', 
    true
  );

  wp_register_script(
    'full-height-slider-plugin-script',
    FULL_HEIGHT_SLIDER_PLUGIN_URL . 'assets/js/main-es5.min.js',
    array('full-height-slider-swiper-script'),
    '1.0.0', 
    true
  );
}

add_action('wp_enqueue_scripts', 'full_height_slider_plugin_register_scripts');


// Create the shortcode
function full_height_slider_plugin_shortcode($atts) {
  extract(
    shortcode_atts(
      array(
        'category_slug' => ''
      ),
      $atts
    )
  );

  $args = array(
		'post_type'      => 'slider_project',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'tax_query' => array(
      'relation' => 'OR',
      array(
        'taxonomy' => 'project_category',
        'field'    => 'slug',
        'terms'    => explode(',', $category_slug)
      ),
      array(
        'taxonomy' => 'project_category',
        'operator' => 'EXISTS'
      )
    )    
  );

  $projects = get_posts($args);

  // Load the scripts/styles only if the shortcode is called
  wp_enqueue_script('full-height-slider-swiper-script');
  wp_enqueue_script('full-height-slider-plugin-script');
  
  wp_enqueue_style('full-height-slider-swiper-styles');
  wp_enqueue_style('full-height-slider-plugin-styles'); 

  ?>
  <div class="projects-slider-container"><div class="swiper-container swiper-container-v"><div class="swiper-wrapper swiper-wrapper-v">
  <?php  
  foreach($projects as $project) {
    // Project data
    $ID = $project -> ID;
    $title = $project -> post_title;
    $slug = $project -> post_name;
    $description = get_field('project_description', $ID);
    $image_gallery = get_field('project_gallery', $ID);

    ?>      
    <div class="swiper-slide swiper-slide-v">

      <div class="swiper-container swiper-container-h">
        <div class="swiper-wrapper swiper-wrapper-h">
          <?php 
          if ($image_gallery) {
            foreach($image_gallery as $index => $image) {
              $image_src = esc_url($image['url']);
              $image_alt = $image['alt'];
      
              ?>
              <div class="swiper-slide swiper-slide-h">
                <div class="slide-img-container">
                  <img class="slide-img" src="<?php echo $image_src ?>" alt="<?php echo $image_alt ? $image_alt : $title . ' - Imagen #' . $index ?>">
                </div>
              </div>
              <?php              
            }
          }                
          ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>

      <div class="slide-info">

        <div class="slide-info__description">
          <p class="slide-info__description-content">
            <?php echo $description ?>
          </p>
        </div>

        <span class="slide-info__title">
          <?php echo $title ?><i class="slide-info__mobile-pagination"></i>
        </span>

        <img class="slide-info__mobile-icon" src="<?php echo esc_url(FULL_HEIGHT_SLIDER_PLUGIN_URL . 'assets/img/plus_icon.png') ?>">

      </div>

    </div>
    <?php  
  }
  ?>
  </div></div></div>
  <?php
}

add_shortcode('projects_slider', 'full_height_slider_plugin_shortcode');
