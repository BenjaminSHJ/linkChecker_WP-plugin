<?php
/*
Plugin Name:  API scraper
Description: A  plugin to demonstrate wordpress functionality
Author: Martin Lauritsen - Cleverdeal ApS
Version: 0.1
*/
add_action('admin_menu', 'scraper_options_panel');

function scraper_options_panel()
{
    add_menu_page('Webscraping', 'Webscraping', 'manage_options', 'scraper-options');
    add_submenu_page('scraper-options', 'Url scraper', 'Url scraper', 'manage_options', 'api-scraper', 'wps_func_scraper');
    add_submenu_page('scraper-options', 'Image scraper', 'Image scraper', 'manage_options', 'api-images', 'wps_func_imgscraper');
}



function wps_func_scraper()
{
    echo "<div class='ifWrap'><iframe class='responsive-iframe' src='https://frontend-2e3x8.ondigitalocean.app/'></iframe></div>";

    p_add_file();
}
function wps_func_imgscraper()
{
    echo "<div class='ifWrap'><iframe class='responsive-iframe' src='https://frontend-2e3x8.ondigitalocean.app/img'></iframe></div>";
       
}

require_once plugin_dir_path(__FILE__) . 'includes/class/class-download-remote-image.php';




// create a scheduled event (if it does not exist already)
function cronstarter_activation() {
	if( !wp_next_scheduled( 'mycronjob' ) ) {  
	   wp_schedule_event( time(), 'daily', 'mycronjob' );  
	}
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'cronstarter_activation');



// here's the function we'd like to call with our cron job
function my_repeat_function() {
	
	// do here what needs to be done automatically as per your schedule
	// in this example we're sending an email
	
	// components for our email
	$recepients = 'm.lauritsen86@gmail.com';
	$subject = 'Cron Job complete';
	$message = 'This is a test mail sent by WordPress automatically as per your schedule.';
	
	// let's send it 
	mail($recepients, $subject, $message);

    download_images_func();
}

// hook that function onto our scheduled event:
add_action ('mycronjob', 'my_repeat_function'); 



// unschedule event upon plugin deactivation
function cronstarter_deactivate() {	

	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('mycronjob');
	
	// unschedule previous event if any
	wp_unschedule_event ($timestamp, 'mycronjob');
} 
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');




// The WP Cron event callback function
function download_images_func() {
    // do something
    class convertToString
	{
		public $str;
		public function __construct($str)
		{
			$this->str = $str;
		}
		public function __toString()
		{
			return (string) $this->str;
		}
	}

	$shops = array("www.adameve.com", );

	//$images = array();
	function call_download($value, $key)
	{
		$url = $value['Image'];
		$download_remote_image = new KM_Download_Remote_Image($url);
		$attachment_id         = $download_remote_image->download();

		if (!$attachment_id) {
			return false;
		}

		echo "downloaded";
	}

	// $get_data = wp_remote_get('https://imgscrape.cleverdeal.org/files/www.lovehoney.com');
	$JSON =  json_decode('[{"key":0,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager0.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager0.jpg","selectable":true},{"key":1,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager1.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager1.jpg","selectable":true},{"key":2,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager10.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager10.jpg","selectable":true},{"key":3,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager11.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager11.jpg","selectable":true},{"key":4,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager12.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager12.jpg","selectable":true},{"key":5,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager13.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager13.jpg","selectable":true},{"key":6,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager2.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager2.jpg","selectable":true},{"key":7,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager3.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager3.jpg","selectable":true},{"key":8,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager4.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager4.jpg","selectable":true},{"key":9,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager5.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager5.jpg","selectable":true},{"key":10,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager6.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager6.jpg","selectable":true},{"key":11,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager7.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager7.jpg","selectable":true},{"key":12,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager8.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager8.jpg","selectable":true},{"key":13,"title":"Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager9.jpg","Image":"https://imgscrape.cleverdeal.org//images/www.adameve.com/Lelo_Sona_2_Cruise_Sonic_Clitoral_Massager9.jpg","selectable":true}]', true);

	if (is_wp_error($get_data)) {
		return false; // Bail early
	} else {
		array_walk($JSON, "call_download", new convertToString($JSON['Image']));
	}

	// //include( 'wp-load.php' );
	////include_once( ABSPATH . '/wp-admin/includes/image.php' );


}











function wlt_enqueue_admin_script($hook)
{

    wp_enqueue_style('wlt-backend-css', plugins_url('includes/css/style-backend.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'wlt_enqueue_admin_script');


function p_add_file()
{


    $args = [
        // 'post_type'         => 'roundupreviews',
        'post_type'         => 'roundup',
        'post_status'       => 'publish',
        'posts_per_page'    => -1,
        'fields'            => 'ids'
    ];
    $my_posts = get_posts($args);

    $data_array = [];
    if ($my_posts) {


        foreach ($my_posts as $post_id) {
            $author_id = get_post_field('post_author', $post_id);
            $author_name = get_the_author_meta('display_name', $author_id);
            $content_post = get_post($post_id);
            $content = $content_post->post_content;
            $post_name = get_the_title($post_id);

            $posts = [];
            $links = [];
            $linksA = [];


            if (have_rows('products', $post_id)) :

                $i = 0;
                // loop through rows (parent repeater)
                while (have_rows('products', $post_id)) : the_row();
                    $i++;
                    if ($i > 3)
                        break;
                    // check for rows (sub repeater)
                    if (have_rows('opret_shop_bar')) :

                        // loop through rows (sub repeater)
                        while (have_rows('opret_shop_bar')) : the_row();

                            $link = get_sub_field('link');

                            if (!empty($link)) {
                                array_push($links, '<a target="_blank" href="' . $link . '" style="display: block;">' . $link . '</a></br>');
                                array_push($linksA, $link);
                            }
                        endwhile;

                    endif;

                endwhile;

            endif;

            // $data_array[] = [
            // 	'wlt_id'                => '<a data-post-name="' . $post_name . '" data-post-id="' . $post_id . '" href="' . get_edit_post_link($post_id) . '"> ' . $post_id . ' </a>',
            // 	'wlt_title'             => '<a href="' . get_edit_post_link($post_id) . '"> ' . get_the_title($post_id) . ' </a>',
            // 	'wlt_publish_data'      => get_the_date('l F j, Y', $post_id),
            // 	'wlt_post_type'         => get_post_type($post_id),
            // 	'wlt_post_author'       => '<a href="' . get_edit_profile_url($author_id) . '"> ' . $author_name . ' </a>',
            // 	'wlt_post_links'    =>  implode(" ", $links),
            // ];

            // str_replace(['"',"'"], "", $linksA);
            $data_json[] = [
                'post_id'                => $post_id,
                'post_author'                => $author_name,
                'post_title'                => '<a target="_blank" href="' . get_edit_post_link($post_id) . '"> ' . get_the_title($post_id) . ' </a>',
                'post_published'                => get_the_date('l F j, Y', $post_id),
                'post_links'    =>  implode(" ", $linksA),
            ];
        }



        $posts  = [
            'posts'    =>  $data_json,
        ];
        //print_r($posts);
        $json = json_encode($posts);
    }

    $host = $_SERVER['HTTP_HOST'];
    if (file_put_contents('../wp-content/uploads/files/out/' . $host . '.json', $json)) {
        echo "JSON file created successfully...";
    } else {
        echo "Oops! Error creating json file...";
    }

    return $data_array;
}

