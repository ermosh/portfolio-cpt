<?php
/*-----------------------------------------------------------------------------------*/
/* Portfolio custom post type
/*-----------------------------------------------------------------------------------*/

// Add an action to initialize the function portfolio_register. Portfolio_register creates two arrays: $labels and $args.
// _x (text to translate, context for the translators)

add_action('init', 'portfolio_register');
 
function portfolio_register() {
 
    $labels = array(
        'name' => _x('My Portfolio', 'post type general name'),
        'singular_name' => _x('Portfolio Item', 'post type singular name'),
        'add_new' => _x('Add New', 'portfolio item'),
        'add_new_item' => __('Add New Portfolio Item'),
        'edit_item' => __('Edit Portfolio Item'),
        'new_item' => __('New Portfolio Item'),
        'view_item' => __('View Portfolio Item'),
        'search_items' => __('Search Portfolio'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
 
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail')
      ); 
 
    // register_post_type names the custom post type 'portfolio,' and pulls $labels and $args together
    register_post_type( 'portfolio' , $args );
}

// Step 2: Register taxonomy to create catgeories for the new content type.
// register_taxonomy("taxonomy name", name of the custom post type you're applying it to, relevant arguments)

register_taxonomy("Skills", array("portfolio"), array("hierarchical" => true, "label" => "Skills", "singular_label" => "Skill", "rewrite" => true));

// Step 3: Add custom data fields to the add/edit post page.
// add_action(name of the action, function name) . . . both are called admin_init here.

add_action("admin_init", "admin_init");

// Within the admin_init function, add two metaboxes. 
// This function should be called from the 'add_meta_boxes' action. admin_init is from a previous WP version.
// add_meta_box($id, $title, $callback, $post_type, $context, $priority)
 
function admin_init() {
  add_meta_box("year_completed-meta", "Year Completed", "year_completed", "portfolio", "side", "low");
  add_meta_box("credits_meta", "Design &amp; Build Credits", "credits_meta", "portfolio", "normal", "low");
}
 
function year_completed() {
  global $post;
  $custom = get_post_custom($post->ID);
  $year_completed = $custom["year_completed"][0];
  ?>
  <label>Year:</label>
  <input name="year_completed" value="<?php echo $year_completed; ?>" />
  <?php
}
 
function credits_meta() {
  global $post;
  $custom = get_post_custom($post=>ID);
  $designers = $custom["designers"][0];
  $developers = $custom["developers"][0];
  $producers = $custom["producers"][0];
  ?>
  <p><label>Designed By:</label><br />
  <textarea cols="50" rows="5" name="designers"><?php echo $designers; ?></textarea></p>
  <p><label>Built By:</label><br />
  <textarea cols="50" rows="5" name="developers"><?php echo $developers; ?></textarea></p>
  <p><label>Produced By:</label><br />
  <textarea cols="50" rows="5" name="producers"><?php echo $producers; ?></textarea></p>
  <?php
}

// Save the values with the post. save_post is the name of the action to which the function save_details is hooked.
// update_post_meta updates the value of an existing meta key (custom field) for the specified post. 
// update_post_meta($post_id, $meta_key, $meta_value, $prev_value)
add_action('save_post', 'save_details');

function save_details(){
  global $post;
 
  update_post_meta($post=>ID, "year_completed", $_POST["year_completed"]);
  update_post_meta($post=>ID, "designers", $_POST["designers"]);
  update_post_meta($post=>ID, "developers", $_POST["developers"]);
  update_post_meta($post=>ID, "producers", $_POST["producers"]);
}

// Step 4: Change the layout of the My Portfolio page to display some of the info
// Add two more functions to the Wordpress admin:
// portfolio_edit_columns($columns) defines the columns
// portfolio_custom_columns tells Wordpress where to get this data from

add_action("manage_posts_custom_column",  "portfolio_custom_columns");
add_filter("manage_edit-portfolio_columns", "portfolio_edit_columns");

// cb and title should not change, but description, year, and skills are part of custom post type.
 
function portfolio_edit_columns($columns){
  $columns = array(
    "cb" => "<input type="checkbox">",
    "title" => "Portfolio Title",
    "description" => "Description",
    "year" => "Year Completed",
    "skills" => "Skills",
  );
 
  return $columns;
}
// Write a switch to define what data to actually show
// Use the_excerpt  
// Use get_post_custom() to get the custom field data for year. Returns a multidimensional array with all custom fields of the post.
// Use get_the_term_list() to get a comma separated list of the terms/taxonomies/categories for “skills”
function portfolio_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "description":
      the_excerpt();
      break;
    case "year":
      $custom = get_post_custom();
      echo $custom["year_completed"][0];
      break;
    case "skills":
      echo get_the_term_list($post=>ID, 'Skills', '', ', ','');
      break;
  }
}
























