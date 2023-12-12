<?php 

add_action( 'after_setup_theme', 'wpdocs_theme_setup' );
function wpdocs_theme_setup() {
    add_image_size( '500x425', 500, 425, false ); // Image de fond des articles carrés sous l'article horizontal
    add_image_size( '500x925', 500, 925, false ); // Image de fond de l'article à la verticale
    add_image_size( '1000x500', 1000, 500, true ); // Image de fond de l'article à l'horizontale
}

function get_video_thumbnail_uri( $video_uri ) {
  $thumbnail_uri = '';
  
  // determine the type of video and the video id
  $video = parse_video_uri( $video_uri );

  // get youtube thumbnail
  if ( $video['type'] == 'youtube' ) {
    $thumbnail_uri = 'http://img.youtube.com/vi/' . $video['id'] . '/hqdefault.jpg';
  }
  // get vimeo thumbnail
  if( $video['type'] == 'vimeo' ) {
    $thumbnail_uri = get_vimeo_thumbnail_uri( $video['id'] );
  }  
  // get wistia thumbnail
  if( $video['type'] == 'wistia' ) {
    $thumbnail_uri = get_wistia_thumbnail_uri( $video_uri );
  }
  // get default/placeholder thumbnail
  if( empty( $thumbnail_uri ) || is_wp_error( $thumbnail_uri ) ) {
    $thumbnail_uri = ''; 
  }

  //return thumbnail uri
  return $thumbnail_uri;
}

// Parse the video uri/url to determine the video type/source and the video id 
function parse_video_uri( $url ) {

  // Parse the url 
  $parse = parse_url( $url );

  // Set blank variables
  $video_type = '';
  $video_id = '';

  // Url is http://youtu.be/xxxx
  if ( $parse['host'] == 'youtu.be' ) {
    $video_type = 'youtube';
    $video_id = ltrim( $parse['path'],'/' );	
  }

  // Url is http://www.youtube.com/watch?v=xxxx 
  // or http://www.youtube.com/watch?feature=player_embedded&v=xxx
  // or http://www.youtube.com/embed/xxxx
  if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {
    $video_type = 'youtube';
    parse_str( $parse['query'] );
    $video_id = $v;	
    if ( !empty( $feature ) ) {
      $video_id = end( explode( 'v=', $parse['query'] ) );
      $vidIdArray = explode( '&', $video_id );
      $video_id = $vidIdArray[0];
    }
    if ( strpos( $parse['path'], 'embed' ) == 1 ) {
      $video_id = end( explode( '/', $parse['path'] ) );
    }
  }

  // Url is http://www.vimeo.com
  if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {
    $video_type = 'vimeo';
    $video_id = ltrim( $parse['path'],'/' );	
  }
  $host_names = explode(".", $parse['host'] );
  $rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');
  // Url is an oembed url wistia.com
  if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {
    $video_type = 'wistia';
    if ( strpos( $parse['path'], 'medias' ) == 1 ) {
      $video_id = end( explode( '/', $parse['path'] ) );
    }
  }

  // If recognised type return video array
  if ( !empty( $video_type ) ) {
    $video_array = array(
      'type' => $video_type,
      'id' => $video_id
    );
    return $video_array;
  } else {
    return false;
  }
}

// Takes a Vimeo video/clip ID and calls the Vimeo API v2 to get the large thumbnail URL.
function get_vimeo_thumbnail_uri( $clip_id ) {
  $vimeo_api_uri = 'http://vimeo.com/api/v2/video/' . $clip_id . '.php';
  $vimeo_response = wp_remote_get( $vimeo_api_uri );
  if( is_wp_error( $vimeo_response ) ) {
    return $vimeo_response;
  } else {
    $vimeo_response = unserialize( $vimeo_response['body'] );
    return $vimeo_response[0]['thumbnail_large'];
  }
}

// Takes a wistia oembed url and gets the video thumbnail url.
function get_wistia_thumbnail_uri( $video_uri ) {
  if ( empty($video_uri) )
    return false;
  $wistia_api_uri = 'http://fast.wistia.com/oembed?url=' . $video_uri;
  $wistia_response = wp_remote_get( $wistia_api_uri );
  if( is_wp_error( $wistia_response ) ) {
    return $wistia_response;
  } else {
    $wistia_response = json_decode( $wistia_response['body'], true );
    return $wistia_response['thumbnail_url'];
  }
}

add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars )
{
	// Add a new toolbar called "Very Simple"
	// - this toolbar has only 1 row of buttons
	$toolbars['Very Simple' ] = array();
	$toolbars['Very Simple' ][1] = array('bold' , 'link' );
	$toolbars['Very Simple + List' ] = array();
	$toolbars['Very Simple + List' ][1] = array('bold' , 'link', 'bullist', 'numlist' );
	$toolbars['Intermediate' ] = array();
	$toolbars['Intermediate' ][1] = array('formatselect', 'bold' , 'link', 'bullist', 'numlist' );

	// return $toolbars - IMPORTANT!
	return $toolbars;
}

//Permet de loader les svg dans wordpress
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

//Enlève les balises p et remplace celle qui sont à l'intérieur par des BR
function strip_p( $string ) {
  $string = str_replace("<p>", "", $string);

  if ( substr_count( $string, "</p>" ) > 1 ):
    $string = substr_replace( $string, "", strrpos( $string , "</p>" ), strlen( "</p>" ) ); 
    $string = str_replace("</p>", "<br><br>", $string);
  else:
    $string = str_replace("</p>", "", $string);
  endif;
  
  return $string;
}

//Enlève tous les caractéres sauf les chiffres dans un numéro de téléphone
function linkify_tel( $numero ) {
	$numero = preg_replace( '/\D/', '', $numero );
	
	return $numero;
}

//Enlève http(s):// en avant d'un url et / à la fin
function strip_url( $site ) {
	$site = trim($site, '/');
    $site = preg_replace('#^https?://#', '', $site);
	
	return $site;
}

//AJAX
function more_post_ajax(){

  $offset = $_POST["offset"];
  $ppp = $_POST["ppp"];
  $category = $_POST["cat"];
  $post_type = $_POST["post_type"];
  header("Content-Type: text/html");

  $custom_args = array(
      'post_type' => $post_type,
      'posts_per_page' => $ppp,
      'offset' => $offset,
      'post_status' => 'publish',
      'order_by' => 'date', 
      'order' => 'DESC',
      'category_name' => $category
  );

  $custom_query = new WP_Query( $custom_args );
  $GLOBALS['custom_query'] = $custom_query;

  while ( $custom_query->have_posts() ) : $custom_query->the_post();
      
      switch ($post_type):
      case 'post':
        include('item-chroniques.php');
      break;
      case 'exercice':
        include('item-exercices.php');
      break;
      endswitch;

  endwhile;
  wp_reset_postdata();

exit;
}

add_action('wp_ajax_nopriv_more_post_ajax', 'more_post_ajax'); 
add_action('wp_ajax_more_post_ajax', 'more_post_ajax');

function my_taxonomy_wp_list_categories( $args, $field ) {
    
    // modify args
    $args['child_of'] = 9;
    // return
    return $args;
    
}
add_filter('acf/fields/taxonomy/wp_list_categories/name=categorie_exercices', 'my_taxonomy_wp_list_categories', 10, 2);
add_filter('acf/fields/taxonomy/query/name=categorie_exercices', 'my_taxonomy_wp_list_categories', 10, 2);

function my_taxonomy_wp_list_categories_blog( $args, $field ) {
    
    // modify args
    $args['child_of'] = 10;
    // return
    return $args;
    
}
add_filter('acf/fields/taxonomy/wp_list_categories/name=categorie_article', 'my_taxonomy_wp_list_categories_blog', 10, 2);
add_filter('acf/fields/taxonomy/query/name=categorie_article', 'my_taxonomy_wp_list_categories_blog', 10, 2);

function select_services ( $tag, $unused ) {  
  
    if ( $tag['name'] != 'services-list' )  
        return $tag;  
  
    $args = array ( 'post_type' => 'service', 
                    'orderby' => 'title',  
                    'order' => 'ASC',
                    'posts_per_page' => -1 );  
    $plugins = get_posts($args);  
  
    if ( ! $plugins )  
        return $tag;  
    
    $tag['raw_values'][0] = 'Je ne suis pas certain(e)';  
    $tag['values'][0] = 'aucune-pref';  
    $tag['labels'][0] = 'Je ne suis pas certain(e)';
    
    foreach ( $plugins as $plugin ) {  
        $tag['raw_values'][] = $plugin->post_title;  
        $tag['values'][] = $plugin->ID;  
        $tag['labels'][] = $plugin->post_title;
    }  
  
    return $tag;  
}  
add_filter( 'wpcf7_form_tag', 'select_services', 10, 2);

function select_professionnels ( $tag, $unused ) {  
  
    if ( $tag['name'] != 'pro-list' )  
        return $tag;  
  
    $args = array ( 'post_type' => 'equipe', 
                    'orderby' => 'title',  
                    'order' => 'ASC',
                    'posts_per_page' => -1 );  
    $plugins = get_posts($args);  
  
    if ( ! $plugins )  
        return $tag;  
    
    $tag['raw_values'][0] = 'Aucune préférence';  
    $tag['values'][0] = 'aucune-pref';  
    $tag['labels'][0] = 'Aucune préférence';
    
    foreach ( $plugins as $plugin ) {  
        $tag['raw_values'][] = $plugin->post_title;  
        $tag['values'][] = get_field('domaine_personne', $plugin->ID)->ID.'-'.$plugin->post_name;  
        $tag['labels'][] = $plugin->post_title;
    }  
  
    return $tag;  
}  
add_filter( 'wpcf7_form_tag', 'select_professionnels', 10, 2);