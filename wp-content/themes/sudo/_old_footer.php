<footer dkit-grid="col-12" class="Footer">
   <div dkit-grid="grid-wrapper">
       <?php
	   if( get_the_ID() == 127 ): $pageContact = true; else: $pageContact = false; endif;
       $the_query = new WP_Query( 'page_id=127' );

       if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

        <div dkit-grid="col-4" class="footer-items">

          <div class="logo">
             <a href="<?php echo get_home_url(); ?>"><img src="<?php bloginfo('template_directory'); ?>/img/logo_poly_bl.svg" onerror="this.src='<?php bloginfo('template_directory'); ?>/img/logo_poly.png'" alt=""></a>
          </div>
            <?php if( get_field('adresse') ): ?>
            <p><?php if( get_field('lien_adresse') ): ?><a class="lien-adresse" href="<?php the_field('lien_adresse'); ?>" target="_blank"><?php endif; ?><?php echo get_field('adresse'); ?><?php if( get_field('lien_adresse') ): ?></a><?php endif; ?></p>
            <?php endif; ?>

            <?php if( have_rows('telephone', 127) ): ?>
					<div class="footer-tel-spacer">
							<?php $rows = get_field('telephone'); ?>
							<?php foreach( $rows as $row ): ?>
								<?php echo $row['type']; ?>

								<p class="footer-tel"> 
								 <?php if( $row['type'] != 'Télécopieur' ){?>
									 <a href="tel:+1<?php echo linkify_tel( $row['numero'] ) ?>">
								 	<?php echo $row['numero'] ?></a>
									<?php }else{echo '<a style="pointer-events:none">'.$row['numero'].'</a>';} ?> 
								</p>

							<?php endforeach; ?>
						</div>
            <?php endif; ?>
						<a href="<?php echo get_the_permalink(313); ?>" class="bouton-contour">
                <?php echo get_field('bouton_rendez-vous'); ?><span class="icon-arrow-right next"></span>
            </a>
            <?php if( $pageContact != true ): ?>
            <a href="<?php echo get_the_permalink(127); ?>" class="bouton-contour">
                <?php echo get_field('bouton_contact'); ?>
                <span class="icon-arrow-right next"></span>
            </a>
            <?php endif; ?>
            <a></a>
        </div>

        <div dkit-grid="col-5" class="footer-items">
            <h1><?php echo get_field('titre_horaire'); ?></h1>

            <?php if( have_rows('horaire_footer') ): ?>
                    <?php while ( have_rows('horaire_footer') ) : the_row(); ?>

                    <p class="horaire"><strong><?php echo get_sub_field('journee_horaire').': '; ?></strong><?php echo get_sub_field('heures_horaire'); ?></p>

                    <?php endwhile; ?>
            <?php endif; ?>

            <p class="note"><?php echo strip_p( get_field('note') ); ?></p>
        </div>

        <div dkit-grid="col-3" class="footer-items">
            <?php
            $titre = get_field('titre_social');
            $tags = array("<p>", "</p>");
            $titre = str_replace($tags, "", $titre);
            ?>
            <h1><?php echo $titre; ?></h1>

            <?php if( have_rows('reseaux_sociaux') ): ?>
                    <?php while ( have_rows('reseaux_sociaux') ) : the_row(); ?>

                    <a href="<?php echo get_sub_field('url_reseau'); ?>" target="_blank" class="Social">
                       <span class="social-icon">
                           <img src="<?php echo get_sub_field('image_reseau')['url']; ?>" onerror="<?php bloginfo('template_directory'); ?>/img/facebook.png" alt="">
                       </span>
                       <span class="social-text"><?php echo get_sub_field('nom_reseau'); ?></span>
                    <br/>
                    </a>

                    <?php endwhile; ?>
            <?php endif; ?>

        </div>

        <?php endwhile;
        endif;
        wp_reset_postdata();?>
    </div>
</footer>

<?php
$the_query = new WP_Query( 'page_id=241' );

if ( $the_query->have_posts() ) : ?>
<section dkit-grid="col-12" class="Carousel">
   <div class="Partner-wrapper">

        <div dkit-grid="grid-wrapper" class="ligne">
            <h1>Nos partenaires</h1>
        </div>

        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

        <div class="nav-wrap">
            <?php
            $count = count(get_field("partenaires"));
            if($count > 5):
	            echo"
	            <div class='slider_next'>
	                <span class='icon-uniE610 next'></span>
	            </div>
	
	            <div class='slider_prev'>
	                <span class='icon-arrow-left3 prev'></span>
	            </div>";
            endif;
            ?>

            <div id="Partner-slider" dkit-grid="grid-wrapper" class="partenaires-carousel">
                <?php if( have_rows('partenaires') ): ?>
                    <?php while ( have_rows('partenaires') ) : the_row(); ?>

                    <div class="item">
                       <?php if( get_sub_field('lien_partenaires') ) : ?>
                        <a href="<?php echo get_sub_field('lien_partenaires'); ?>" target="_blank">
                            <img src="<?php echo get_sub_field('logo')['url']; ?>" alt="">
                        </a>
                        <?php else : ?>
                        <img src="<?php echo get_sub_field('logo')['url']; ?>" alt="">
                        <?php endif; ?>
                    </div>

                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php endwhile; ?>

    </div>
</section>
<?php endif; wp_reset_postdata(); ?>

<section dkit-grid="col-12" class="Toes">
   <div dkit-grid="col-12" class="border">
        <div class="menu-footer">
            <nav dkit-grid="grid-wrapper">
               <ul>
                    <li><a href="<?php echo get_the_permalink(136); ?>">Services offerts</a></li
                    ><li><a href="<?php echo get_the_permalink(285); ?>">Notre équipe</a></li
                    ><li><a href="<?php echo get_the_permalink(303); ?>">Exercices</a></li
                    ><li><a href="<?php echo get_the_permalink(291); ?>">Chroniques</a></li
                    ><li><a href="<?php echo get_the_permalink(127); ?>">Nous joindre</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div dkit-grid="col-12" class="copyright">
        <div dkit-grid="grid-wrapper">
            <div dkit-grid="col-6" class="copyright-items">
                <p>&copy; <?php echo date("Y") ?> Polyclinique du Lac. Tous droits réservés. &nbsp;|&nbsp; <a href="<?php echo get_the_permalink(354); ?>" class="underline">Politique de confidentialité</a>
                </p>
            </div>
            <div dkit-grid="col-6" class="copyright-items">
               <a href="http://www.agencesudo.ca/" target="_blank">
                    <p class="rea-sudo">Site web réalisé par Sudo &mdash; agence numérique</p>
                   <img src="<?php bloginfo('template_directory'); ?>/img/logo_sudo.svg" alt="" class="logo-sudo">
               </a>

            </div>
        </div>
    </div>
</section>

<?php wp_footer(); ?>
</body>

<script src="<?php bloginfo('template_directory'); ?>/scripts/library.min.js?rel=96c2e9b9b4"></script>
<script src="<?php bloginfo('template_directory'); ?>/lib/js/_module/owl.carousel.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    //Icone hamburger pour le menu mobile
	$('#hamburger').click(function(){
        $(this).stop().toggleClass('open');
	});
    //Ouvrir le menu
    $('#hamburger').click(function(){
        $('.menu-mobile').stop().slideToggle();
	});
    //Ouvrir sous menu services
    $('.Menu li:first-of-type a').mouseover(function(){
        $('.Sousmenu-services').stop().fadeIn(300);
        $('.triangle-services').stop().fadeIn(300);
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
    //Refermer sous menu services
    $('.Sousmenu-services').mouseleave(function(){
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
	});
    //Ouvrir sous menu equipes
    $('.Menu li:nth-child(2) a').mouseover(function(){
        $('.Sousmenu-equipes').stop().fadeIn(300);
        $('.triangle-equipe').stop().fadeIn(300);
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
	});
    //Refermer sous menu equipes
    $('.Sousmenu-equipes').mouseleave(function(){
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
    $('.Menu li:nth-child(3) a').mouseover(function(){
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
    $('.Menu li:nth-child(4) a').mouseover(function(){
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
    $('.Menu li:nth-child(5)').mouseover(function(){
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
	$('.Menu_out').mouseover(function(){
        $('.Sousmenu-services').stop().fadeOut(300);
        $('.triangle-services').stop().fadeOut(300);
        $('.Sousmenu-equipes').stop().fadeOut(300);
        $('.triangle-equipe').stop().fadeOut(300);
	});
});

//Carousel pour les partenaires
$('.partenaires-carousel').owlCarousel({
    loop:false,
    nav:false,
    dots:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
});
//Carousel pour les témoignages
$('.temoignages-carousel').owlCarousel({
    loop:false,
    nav:false,
    dotsContainer:'.Dots',
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
});
//Gestion des boutons suivants et précédents des carousel
$('.next').click(function() {
    $('.partenaires-carousel').trigger('next.owl.carousel', [350]);
    $('.temoignages-carousel').trigger('next.owl.carousel', [350]);
});
$('.prev').click(function() {
    $('.partenaires-carousel').trigger('prev.owl.carousel', [350]);
    $('.temoignages-carousel').trigger('prev.owl.carousel', [350]);
});

//Affichage des réponses quand clic sur + des questions
$(document).ready(function(){

    FOLD = (function(){

        var $btnPlus = $('.Question');

        var unfold = function(){

            if($(this).find('.Plus').hasClass('icon-uni60')){
                $(this).next().slideDown();
                $(this).find('.Plus').removeClass('icon-uni60')
                       .addClass('icon-uni5F');
            } else {
                $(this).next().slideUp();
                $(this).find('.Plus').removeClass('icon-uni5F')
                       .addClass('icon-uni60');
            }
        };

        return{
            init: function(){
                $btnPlus.on('click', unfold);
            }
        }

    })();

    FOLD.init();

});

//Switch de place le aside avec la section service quand la résolution égale 320px
$(document).ready(function(){

    if($(window).width() <= 960){
        $('.container').insertBefore('.Aside');
        $('.Service-container').insertBefore('.Aside');
    }

    $(window).on('resize', function(){
        if($(window).width() <= 960){
            $('.container').insertBefore('.Aside');
            $('.Service-container').insertBefore('.Aside');
        } else {
            $('.container').insertAfter('.Aside');
            $('.Service-container').insertAfter('.Aside');
        }

			if($(window).width() > 900){
				$('.menu-mobile').css( 'display', 'none' );
				$('.menu-mobile').attr('disabled', 'disabled');
				$('#hamburger').removeClass('open');
			}
    });

    if($(window).width() <= 768){
        $('.Article_content').insertBefore('.Photo_pro');
    }

    $(window).on('resize', function(){
        if($(window).width() <= 768){
            $('.Article_content').insertBefore('.Photo_pro');
        } else {
            $('.Article_content').insertAfter('.Photo_pro');
        }
    });

    switchBlog();

    $(window).on('resize', switchBlog);

	function switchBlog(){
		($(window).width() <= 1000 ) ?
			$('.right-large').appendTo('.blog-left') :
			$('.right-large').appendTo('.blog-right')
	}

});
$( window ).load(function() {
    $('.pro-list').attr('disabled', 'disabled');
    $('#select_equipe').attr('disabled', 'disabled');


    <?php if( isset($_GET['service-demande']) && isset($_GET['professionnel']) ): ?>
        <?php if( !empty($_GET['service-demande']) && !empty($_GET['professionnel']) ): ?>
        <?php
        $slug_to_get = $_GET['service-demande'];
        $args=array(
          'name' => $slug_to_get,
          'post_type' => 'service',
          'post_status' => 'publish',
          'showposts' => 1,
          'ignore_sticky_posts'=> 1
        );
        $my_posts = get_posts($args);
        if( $my_posts ): ?>
            var service_demande = '<?php echo $my_posts[0]->post_name; ?>';
            var professionnel = '<?php echo $_GET['professionnel'] ?>';
            var isSelected = false;

            $('#select_services option').each( function() {
                if( $(this).data('service') == service_demande ){
                  	$(this).prop('selected', true);  
										$(this).attr('selected', 'selected');
                    isSelected = true;
                }
            });

            if( isSelected == true ) {
                $('#select_equipe option').each( function() {
                    pro = $(this).data('pro');
                    service = $(this).data('service');
                    serviceadd = $(this).data('serviceadd');
                    console.log(service)

                    if( service == '0' ){
                        $(this).css( "display", "block" );
												$(this).attr('disabled', false);
                    }
                    if( service == service_demande || serviceadd == service_demande ){
                        $(this).css( "display", "block" );
												$(this).attr('disabled', false);
                        isEmpty = false;
                    }
										else {
										$(this).attr('disabled', true);
									}
                    if( pro == professionnel ){
											$(this).prop('selected', true);
                        $(this).attr('selected', 'selected');
												
												console.log(professionnel);
											console.log(pro);
                    }
									
                });
                if( isEmpty == false ) {
                    $('.pro-list').attr('disabled', false);
                    $('#select_equipe').attr('disabled', false);
                } else {
                    $('.pro-list').attr('disabled', 'disabled');
                    $('#select_equipe').attr('disabled', 'disabled');
                }
            }

        <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
});
$('#select_services').change(function() {
		//console.log("TEST");
    var service_val = $('#select_services').val();
    var isSet = false;
    $('#select_services option').each( function() {
	    if( $(this).text() == service_val ) {
		    service_demande = $(this).data('service');
		    isSet = true;
	    }
	});

    var isEmpty = true;
    $('#select_equipe').val('Aucune préférence');
    $('#select_equipe option').css( "display", "none" );
		$('#select_equipe option').attr('disabled', 'disabled');
    $('#select_equipe option').each( function() {
        service = $(this).data('service');
        serviceadd = $(this).data('serviceadd');
        console.log(service_demande);

        if( service == '0' ){
            $(this).css( "display", "block" );
						$(this).attr('disabled', false);
            $(this).attr('selected', 'selected');
						$(this).prop('selected', true)
        }
        if( service == service_demande || serviceadd == service_demande ){
            $(this).css( "display", "block" );
						$(this).attr('disabled', false);
            isEmpty = false;
        }
    });

    if( isEmpty == false ) {
        $('.pro-list').attr('disabled', false);
        $('#select_equipe').attr('disabled', false);
    } else {
        $('.pro-list').attr('disabled', 'disabled');
        $('#select_equipe').attr('disabled', 'disabled');
    }

});


</script>

<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=PT+Serif:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

<script src="http://s7.addthis.com/js/300/addthis_widget.js" type="text/javascript"></script>

</html>
