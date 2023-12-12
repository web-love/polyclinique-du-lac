<?php get_header(); 

get_template_part( 'module-header' ); ?>

<?php 
	$ppp = 3;
	$args = array( 
		'post_type' => 'exercice', 
		'order_by' => 'date', 
		'order' => 'DESC', 
		'post_status' => 'publish' 
	);
	
	$blog_total_query = new WP_Query( $args );
	$found_posts = $blog_total_query->found_posts;
?>

<section dkit-grid="col-12" class="Services_section Blog-exercices Chroniques">
    <h1 class="titre"><?php echo get_the_title(); ?></h1>
    <div class="sujets">
	    <p data-categorie=""><?php echo strip_p( get_field('titre_chronique') ); ?></p>
	    <span class="icon-arrow-down"></span>
	</div>    
</section>

<section dkit-grid="col-12" class="Blog Blog-exercices">
    
    <div dkit-grid="col-12" class="types">
        <ul>
            <li class="types-items"><a class="categories" data-categorie="">Tous les exercices</a>
            <?php 
            $subcategories = array('child_of' => 9);
            $categories = get_categories( $subcategories ); 
            foreach ( $categories as $category ) {
                printf( '<li class="types-items"><a class="categories" data-categorie="%1$s">%2$s</a>',
                    esc_attr( $category->category_nicename ),
                    esc_html( $category->cat_name ),
                    esc_html( $category->category_count )
                );
            }
            ?>
        </ul>
    </div>     
       
    <div class="Blog_roll" data-grid="col-12">
        <?php 
        $args = array( 
	        'post_type' => 'exercice', 
	        'order_by' => 'date', 
	        'order' => 'DESC', 
	        'post_status' => 'publish', 
	        'posts_per_page'=>$ppp 
        );
        
        if( isset( $_GET["categorie"] ) ): 
            $args['category_name'] = $_GET["categorie"];
          endif;
        $blog_query = new WP_Query( $args ); ?> 
              
        <?php if( $blog_query->have_posts() ):
        while( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
		
		    <?php get_template_part('item-exercices') ?>

        <?php endwhile; endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    
    <div dkit-grid="col-12" class="Afficher_plus">
        <a id="more_posts">Afficher plus d'exercices <span class="icon-uni60"></span></a>
    </div>
        
</section> 
<?php get_footer(); ?>


<script>
$(document).ready(function(){
//  Devkit.init(
//    newEnv = {
//        //mHeight: true,
//        mMasonry: true,
//        //mVertical: true,
//    }
//  ); 
  
  $('.sujets').click(function(){
      $('.types').stop().slideToggle();
      $('.sujets').find('span').stop().toggleClass('icon-arrow-up');
  });
    
/*  $('.categories').click(function(){
      var $text = $(this).text();
      $('.sujets p').text($text);
  });*/
  
    var ajaxUrl = "<?php echo admin_url('admin-ajax.php', null); ?>";
  var page = 1; // What page we are on.
  var ppp = <?php echo $ppp; ?>; // Post per page
  var post_type = 'exercice';
  var total = 0;
  var cat = '';


    
$('.categories').on('click', function() {
    cat = $(this).attr('data-categorie');
    var $text = $(this).text();
    $('.sujets p').text($text);
    $('.sujets p').attr('data-categorie', cat);
    
    $.post(
        ajaxUrl,
        {
          'action': 'more_post_ajax',
          'offset': 0,
          'ppp': ppp,
          'post_type': post_type,
          'cat': cat
        },
        function(response){
          $('.Blog_roll').empty();
          $('.Blog_roll').append(response);
        }
    ).success(function(){
        

    });
    
});
    
  <?php if( $found_posts > $ppp ): ?>
    
  $('#more_posts').on('click', function() {
        
    $("#more_posts").css({ "pointer-events": "none", "opacity": "0.25" }); // Disable the button, temp.
    $("#more_posts").text('Chargement');

    countBefore = countAfter = 0;
    $( '.left-small' ).each(function(){
      countBefore++
    });

    $.post(
        ajaxUrl,
        {
          'action': 'more_post_ajax',
          'offset': (page * ppp),
          'ppp': ppp,
          'post_type': post_type,
          'cat': cat
        },
        function(response){
          $('.Blog_roll').append(response);
        }
    ).success(function(){
        total = <?php echo $found_posts; ?>;
        page++;

        $( '.left-small' ).each(function(){
          countAfter++
        });

        diffLoad = countAfter - countBefore;

        i = 0;
        $( '.left-small' ).find('img').load(function(){
          i++
          if (i == diffLoad) {
            
          }
        });

        if( total == countAfter ) {
          $("#more_posts").css({ "pointer-events": "none" });
          $("#more_posts").css({"opacity": "1"}).html('Il n\'y a plus d\'exercices à afficher');
          $(".Afficher_plus").addClass('no-more');
        } else {
          $("#more_posts").css({ "display": "block", "pointer-events": "auto", "opacity": "1" });
          $("#more_posts").html('Afficher plus d\'exercices <span class="icon-uni60"></span>');
        }

    });

 });
  
  <?php endif; ?>
  
});
<?php if( $found_posts <= $ppp ): ?>
$("#more_posts").css({"opacity": "1"}).html('Il n\'y a pas d\'autres exercices à afficher');
$(".Afficher_plus").addClass('no-more');
<?php endif; ?>
</script>