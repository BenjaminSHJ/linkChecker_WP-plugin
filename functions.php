<?php

/**
 * Aatra child theme Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Aatra child theme
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_AATRA_CHILD_THEME_VERSION', '1.0.0');

/**
 * Enqueue styles
 */
function child_enqueue_styles()
{

    wp_enqueue_style('aatra-child-theme-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_AATRA_CHILD_THEME_VERSION, 'all');
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);



add_filter('astra_single_post_navigation_enabled', '__return_false');


define('MY_PLUGIN_DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
add_filter('acf/settings/save_json', 'my_acf_json_save_point');

function my_acf_json_save_point($path)
{

    // Update path
    $path = MY_PLUGIN_DIR_PATH . '/acf-json';

    // Return path
    return $path;
}

function register_custom_taxonomy()
{


    $labels = array(
        'name'              => _x('Roundup category', 'taxonomy general name', 'taxonomy-widget'),
        'singular_name'     => _x('roundupcategory', 'taxonomy singular name', 'taxonomy-widget'),
        'menu_name'         => __('Roundup category', 'taxonomy-widget'),
    );
    $args = array(
        'labels' => $labels,
        'description' => __('types of categorys', 'taxonomy-widget'),
        'hierarchical' => true,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => false,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'roundupcategory',
        'graphql_plural_name' => 'roundupcategories',

    );
    register_taxonomy('roundupcategory', 'roundup', $args);
}

add_action('init', 'register_custom_taxonomy');

add_filter('request', 'rudr_change_term_request', 1, 1);

function rudr_change_term_request($query)
{

    $tax_name = 'roundupcategory'; // specify you taxonomy name here, it can be also 'category' or 'post_tag'

    // Request for child terms differs, we should make an additional check
    if ($query['attachment']) :
        $include_children = true;
        $name = $query['attachment'];
    else :
        $include_children = false;
        $name = $query['name'];
    endif;

    $term = get_term_by('slug', $name, $tax_name); // get the current term to make sure it exists

    if (isset($name) && $term && !is_wp_error($term)) : // check it here

        if ($include_children) {
            unset($query['attachment']);
            $parent = $term->parent;
            while ($parent) {
                $parent_term = get_term($parent, $tax_name);
                $name = $parent_term->slug . '/' . $name;
                $parent = $parent_term->parent;
            }
        } else {
            unset($query['name']);
        }

        switch ($tax_name):
            case 'category': {
                    $query['category_name'] = $name; // for categories
                    break;
                }
            case 'post_tag': {
                    $query['tag'] = $name; // for post tags
                    break;
                }
            default: {
                    $query[$tax_name] = $name; // for another taxonomies
                    break;
                }
        endswitch;

    endif;

    return $query;
}

add_filter('term_link', 'rudr_term_permalink', 10, 3);

function rudr_term_permalink($url, $term, $taxonomy)
{

    $taxonomy_name = 'roundupcategory'; // your taxonomy name here
    $taxonomy_slug = 'roundupcategory'; // the taxonomy slug can be different with the taxonomy name (like 'post_tag' and 'tag' )

    // exit the function if taxonomy slug is not in URL
    if (strpos($url, $taxonomy_slug) === FALSE || $taxonomy != $taxonomy_name) return $url;

    $url = str_replace('/' . $taxonomy_slug, '', $url);

    return $url;
}

add_action('init', 'create_post_type');


function create_post_type()
{
    // You'll want to replace the values below with your own.
    register_post_type(
        'singleproduct', // change the name
        array(
            'labels' => array(
                'name' => __('Single product'), // change the name
                'singular_name' => __('singleproduct'), // change the name
            ),

            'public' => true,
            'supports' => array('title', 'editor', 'custom-fields', 'roundupcategory', 'page-attributes', 'thumbnail', 'author'), // do you need all of these options? // do you need categories and tags?
            'hierarchical' => false,
            'taxonomies' => array('roundupcategory', 'post_tag'),
            'menu_icon'   => 'dashicons-products',
            'has_archive' => true,
            'publicly_queryable'  => true,
            'rewrite' => array('slug' => '/', 'with_front' => false),
            'show_in_graphql' => true,
            'graphql_single_name' => 'product',
            'graphql_plural_name' => 'products',
        )
    );
    register_post_type(
        'roundup', // change the name
        array(
            'labels' => array(
                'name' => __('Roundup'), // change the name
                'singular_name' => __('roundup'), // change the name
            ),
            'public' => true,
            'taxonomies' => array('roundupcategory', 'post_tag'),
            'supports' => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'author'), // do you need all of these options?// do you need categories and tags?
            'hierarchical' => false,
            'publicly_queryable'  => true,
            'menu_icon'   => 'dashicons-products',
            'has_archive' => true,
            'rewrite' => array('slug' => '/', 'with_front' => false),
            'show_in_graphql' => true,
            'graphql_single_name' => 'roundup',
            'graphql_plural_name' => 'roundups',
        )
    );
}

function remove_cpt_slug($post_link, $post, $leavename)
{
    $post_types = array(
        'roundup',
        'singleproduct'
    );

    if (in_array($post->post_type, $post_types) && 'publish' === $post->post_status) {
        $post_link = str_replace('/' . $post->post_type . '/', '/', $post_link);
    }

    return $post_link;
}
add_filter('post_type_link', 'remove_cpt_slug', 10, 3);

function change_slug_struct($query)
{
    if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
        return;
    }
    if (
        !empty($query->query['name'])
    ) {
        $query->set('post_type', array(
            'post',
            'singleproduct',
            'roundup',
            'page'
        ));
    }
}
add_action('pre_get_posts', 'change_slug_struct');

//display custom posttypes on category archive pages
function wpse_category_set_post_types($query)
{
    if ($query->is_category) :
        $query->set('post_type', array('post', 'roundupreviews', 'roundupreviews'));
        $query->set('post_type', array('post', 'singleproduct', 'singleproduct'));
    endif;
    return $query;
}
add_action('pre_get_posts', 'wpse_category_set_post_types');

function prefix_category_title($title)
{
    if (is_tax()) {
        $title = single_cat_title('', false);
    }
    return $title;
}
add_filter('get_the_archive_title', 'prefix_category_title');

function my_acf_add_local_field_groups()
{
    remove_filter('acf_the_content', 'wpautop');
}
add_action('acf/init', 'my_acf_add_local_field_groups');

function my_acf_block_render_callback($block)
{
    // convert name ("acf/testimonial") into path friendly slug ("testimonial")
    $slug = str_replace('acf/', '', $block['name']);

    // include a template part from within the "template-parts/block" folder
    if (file_exists(get_theme_file_path("/template-parts/block/content-{$slug}.php"))) {
        include(get_theme_file_path("/template-parts/block/content-{$slug}.php"));
    }
}
/**
 * Filter Select Choices from Blog Categories
 */
add_filter('acf/load_field/name=vaelg_shop', 'acf_load_complete_field_choices');

function acf_load_complete_field_choices($field)
{

    $field['choices'] = array();
    //Get the repeater field values
    $choices = get_field('shop_links', 626);
    $keys = array_keys($choices);
    for ($i = 0; $i < count($choices); $i++) {

        foreach ($choices[$keys[$i]] as $key => $value) {

            if ($key == 'name') {
                $title = $value;

                //echo $value . "<br>";
                $field['choices'][$title] = $title;
            }
        }
    }
    return $field;
}

// function enqueue_scripts_back_end(){
// 	wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my_query.js', array('jquery'));

// 	wp_localize_script( 'ajax-script', 'ajax_object',
//             array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );

// }
// add_action('admin_enqueue_scripts','enqueue_scripts_back_end');

//Allow Contributors to Add Media
if (current_user_can('contributor') && !current_user_can('upload_files'))
    add_action('admin_init', 'allow_contributor_uploads');

function allow_contributor_uploads()
{
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}

/**
 * Get ID of the first ACF block on the page
 */
function sg_get_first_block_id()
{
    $post = get_post();

    if (has_blocks($post->post_content)) {
        $blocks = parse_blocks($post->post_content);
        $first_block_attrs = $blocks[0]['attrs'];

        if (array_key_exists('id', $first_block_attrs)) {
            return $first_block_attrs['id'];
        }
    }
}

function my_acf_admin_head()
{
?>
    <script type="text/javascript">
        let wp_nonce = '<?= wp_create_nonce('updating-field-nonce'); ?>';
        let ajax_url = '<?= admin_url('admin-ajax.php'); ?>';

        jQuery(document).ready(function($) {
            if (typeof(acf) == 'undefined') {
                return;
            }

            let postID = acf.get('post_id');


            function htmlDecode(value) {
                return $('<div/>').html(value).text();
            }

            $(document).on('change', '[data-key="field_6144c3e80231d"] .acf-input select', function(e) {

                let value = $(this).find(":selected").val();
                let id = $(this).attr('id');

                let row = $(this).closest('.layout');

                let dId = row.attr("data-id");

                jQuery.ajax({
                    type: "POST",
                    dataType: 'text',
                    url: ajax_url,
                    data: {
                        action: 'call_update',
                        postId: value,
                        nonce: wp_nonce
                    },
                    success: function(response) {
                        data = JSON.parse(response);

                        const d = data.image.reverse();
                        // row.find('[data-key="field_6144c465a849c"] .acf-input input').val(htmlDecode(data.title));
                        row.find('[data-key="field_6144c4c6a849f"] .acf-input input').val(data.design);
                        row.find('[data-key="field_6144c4daa84a0"] .acf-input input').val(data.ease);
                        row.find('[data-key="field_6144c4f2a84a1"] .acf-input input').val(data.quality);
                        row.find('[data-key="field_6144c50ca84a2"] .acf-input input').val(data.price);
                        row.find('[data-key="field_6144c520a84a3"] .acf-input input').val(data.bedbiblerating);



                        // array_push($userrating, array('vaelg' => $vaelgshop, 'link' => $link,'review' => $review, 'number_reviews' => $number_reviews));

                        var d_json = JSON.parse(data.reviews);

                        // alert(d_json);

                        let vaelg = row.find('[data-key="field_6144c556a84a5"] select');
                        let link = row.find('[data-key="field_6144c578a84a6"] .acf-input input');
                        let review = row.find('[data-key="field_6144c586a84a7"] .acf-input input');
                        let number = row.find('[data-key="field_6144c59ba84a8"] .acf-input input');

                        // let rep = row.find('[data-key="field_6144c53fa84a4"] .acf-row');

                        // var arrText = new Array();
                        // $('input[type=text]').each(function() {
                        //     arrText.push($(this).val());
                        // })
                        // console.log(arrText);

                        console.log(d_json);


                        var array = [];
                        row.find('[data-key="field_6144c53fa84a4"] .acf-row').each(function(index, domEle) {

                            let l = $(this).find('[data-key="field_6144c578a84a6"] .acf-input input[type=text]').val();
                            let r = $(this).find('[data-key="field_6144c586a84a7"] .acf-input input[type=number]').val();
                            let n = $(this).find('[data-key="field_6144c59ba84a8"] .acf-input input[type=number]').val();
                            // var val = $(':eq(1)', this).val();  

                            array.push({
                                'vaelg': l,
                                'link': r,
                                'number': n,
                            });

            
                        })


                        console.log(array);

                    
                        // var total = rep.length;
                        // console.log(total);
                        $.each(d_json, function(i, value) {

                            vaelg.val(value.vaelg).attr("selected", "selected");
                            link.val(JSON.stringify(value.link).replace(/\"/g, ''));
                            review.val(parseFloat(value.review));
                            number.val(parseFloat(value.number_reviews));


                            row.find('[data-key="field_6144c53fa84a4"] .acf-actions a[data-event="add-row"]').trigger('click');
                        });
                        // });
                        // row.find('[data-key="field_6144c556a84a5"] select input').val(data.bedbiblerating);
                        for (let i = 0; i < data.image.length; i++) {
                            row.find('.acf-gallery-main .acf-gallery-attachments').prepend("<div class='acf-gallery-attachment' data-id='" + data.image[i].id + "'><input type='hidden' name='acf[field_6144c42ea849b][" + dId + "][field_6144c49fa849e][]' value='" + data.image[i].id + "'><div class='margin'><div class='thumbnail'><img src='" + data.image[i].url + "' alt=''/></div></div><div class='actions'><a class='acf-icon -cancel dark acf-gallery-remove' href='#' data-id='" + data.image[i].id + "' title='Remove'></a></div></div>");
                        }

                    },
                    error: function(jqxhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });
        });
    </script>
<?php
}

add_action('acf/input/admin_head', 'my_acf_admin_head');


function UpdateCallback()
{


    $postId    = isset($_POST['postId']) ? trim($_POST['postId']) : "";
    $title = get_the_title($postId);
    $design = get_field('design_rating', $postId);
    $ease = get_field('ease_of_use_rating', $postId);
    $quality = get_field('quality_rating', $postId);
    $price = get_field('price_rating', $postId);
    $bedbiblerating = get_field('rating_pro_single', $postId);


    $imgs = array();
    $i = 0;
    $post_objects = get_field('image_gallery_personal_images', $postId);
    if ($post_objects) :
        $images = get_field('image_gallery_personal_images', $postId);
        if ($images) :
            $image = false;
            foreach ($images as $image) :

                $imgs[$i]["id"] = $image['id'];
                $imgs[$i]["url"] = $image['url'];
                $i++;
            //array_push($imgs, $image['url'], $image['id']);
            endforeach;
        endif;
    endif;

    $userrating = array();
    if (have_rows('opret_shop_bar_single', $postId)) {
        while (have_rows('opret_shop_bar_single', $postId)) {
            the_row();
            $vaelgshop = get_sub_field('vaelg_shop');
            $link = get_sub_field('link');
            $review = get_sub_field('review');
            $number_reviews = get_sub_field('number_reviews');

            array_push($userrating, array('vaelg' => $vaelgshop, 'link' => $link, 'review' => $review, 'number_reviews' => $number_reviews));
        }
    }

    $reviewss = json_encode($userrating);

    $response  = array(
        'status'   => 'success',
        'title'       => $title,
        'design'       => $design,
        'ease'       => $ease,
        'quality'       => $quality,
        'price'       => $price,
        'image' => $imgs,
        'reviews' => $reviewss,
        'bedbiblerating' => $bedbiblerating,
    );

    echo json_encode($response);
    exit;
}


add_action('wp_ajax_call_update', 'UpdateCallback');
add_action('wp_ajax_nopriv_call_update', 'UpdateCallback'); // nopriv for unauthenticated users
add_filter('acf/load_field/name=select_feature', 'acf_load_complete_select_feature');

function acf_load_complete_select_feature($fieldfeature)
{

    $fieldfeature['choices'] = array();

    //Get the repeater field values
    $choices = get_field('features', 626);

    $keys = array_keys($choices);
    for ($i = 0; $i < count($choices); $i++) {

        foreach ($choices[$keys[$i]] as $key => $value) {

            if ($key == 'name') {
                $title = $value;
                //echo $value . "<br>";
                $fieldfeature['choices'][$title] = $title;
            }
        }
    }

    return $fieldfeature;
}

function wpstyles()
{
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('roundupcss', get_stylesheet_directory_uri() . '/assets/css/style.css', '', '1.0.0', 'all');
    wp_enqueue_style('singlecss', get_stylesheet_directory_uri() . '/assets/css/single-review.css', '', '1.0.0', 'all');
    wp_enqueue_style('rateit', get_stylesheet_directory_uri() . '/assets/css/rateit.css', '', '1.0.0', 'all');
    // wp_enqueue_style('lightslidercss', get_stylesheet_directory_uri() . '/assets/css/lightslider.css', array(), '1.0.0', 'all');
}

add_action('enqueue_block_assets', 'wpstyles');

function my_scripts()
{
    wp_enqueue_script('bt', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', '', false);
    wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/aa4f6f8791.js');
    wp_enqueue_script('scripts-init', get_stylesheet_directory_uri() . '/assets/js/scripts-init.js', array(), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'my_scripts');



/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
function cf_search_join($join)
{
    global $wpdb;

    if (is_search()) {
        $join .= ' LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'cf_search_join');

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
function cf_search_where($where)
{
    global $pagenow, $wpdb;

    if (is_search()) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)",
            $where
        );
    }

    return $where;
}
add_filter('posts_where', 'cf_search_where');

/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
function cf_search_distinct($where)
{
    global $wpdb;

    if (is_search()) {
        return "DISTINCT";
    }

    return $where;
}
add_filter('posts_distinct', 'cf_search_distinct');
