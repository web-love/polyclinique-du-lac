<?php

get_header(); ?>

<?php get_template_part( 'module-header' ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php if( get_field('couleur', get_field('domaine_personne')) == 'turquoise' ): $couleur = '#00f1f1'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'vert' ): $couleur = '#05761d'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'orange' ): $couleur = '#ff9000'; // done
elseif( get_field('couleur', get_field('domaine_personne')) == 'jaune-vert' ): $couleur = '#b3de3e'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'rouge' ): $couleur = '#fc2b2c'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'violet' ): $couleur = '#9649ff'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'mauve' ): $couleur = '#a419a0'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'vert-pomme' ): $couleur = '#20d40d'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'bleu' ): $couleur = '#345fff'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'bleu-pale' ): $couleur = '#13a7cf'; //done
elseif( get_field('couleur', get_field('domaine_personne')) == 'jaune' ): $couleur = '#e6d800'; //done

?>
<?php endif; ?>

<div dkit-grid="col-12" class="Services Fond">
    <div dkit-grid="col-4" class="Aside pro-info colonne-service">
        <div class="pro-item">
           <?php if( get_field('photo_personne') ): ?>
	        <img src="<?php echo get_field('photo_personne')['url']; ?>" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
	        <?php else: ?>
	        <img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne'); ?>.jpg" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
	        <?php endif; ?>
        </div>

        <?php if( have_rows('horaire_bloc') ): ?>

            <h2>Horaire</h2>
            <?php while ( have_rows('horaire_bloc') ) : the_row(); ?>

            <p><?php echo the_sub_field('journee'); ?>: <?php echo the_sub_field('heures'); ?></p>

            <?php endwhile;

        endif; ?>

        <h2>Me rejoindre</h2>
        <?php if( have_rows('telephone') ): ?>

            <?php while ( have_rows('telephone') ) : the_row(); ?>

            <p><?php echo get_sub_field('type').': '; ?><a href="<?php if( get_sub_field('type') != 'Télécopieur' ): echo 'tel'; else: echo 'fax'; endif; ?>:+1<?php echo linkify_tel( get_sub_field('numero') ) ?>"><?php echo get_sub_field('numero'); ?></a></p>

            <?php endwhile;

        endif; ?>

        <?php if( get_field('adresse_ext')){
            echo "<h2>Adresse</h2><div class='adresse-ext'><p>".get_field('adresse_ext')."</p></div>";
        } ?>

        <?php if( get_field('site_web') ): ?>
        <a href="<?php the_field('site_web'); ?>" class="site <?php the_field('couleur', get_field('domaine_personne')); ?>" target="_blank"><?php echo strip_url( get_field('site_web') ); ?></a>
        <?php endif; ?>

        <?php if( get_field('facebook') || get_field('twitter') || get_field('linkedin') || get_field('instagram') || get_field('youtube') ): ?>
        <ul>
            <?php if( get_field('facebook') ){ ?>
                <li><a href="<?php echo get_field('facebook'); ?>" target="_blank"><span class="icon-uniE01D"></span></a></li>
            <?php } ?>
            <?php if( get_field('twitter') ){ ?>
                <li><a href="<?php echo get_field('twitter'); ?>" target="_blank"><span class="icon-uniE018"></span></a></li>
            <?php } ?>
            <?php if( get_field('linkedin') ){ ?>
                <li><a href="<?php echo get_field('linkedin'); ?>" target="_blank"><span class="icon-uniE01D"></span></a></li>
            <?php } ?>
            <?php if( get_field('instagram') ){ ?>
                <li><a href="<?php echo get_field('instagram'); ?>" target="_blank"><span class="icon-uniE00B"></span></a></li>
            <?php } ?>
            <?php if( get_field('youtube') ){ ?>
                <li><a href="<?php echo get_field('youtube'); ?>" target="_blank"><span class="icon-uniE012"></span></a></li>
            <?php } ?>
        </ul>
        <?php endif; ?>
        
        
        <?php if(get_field('button_activate') && get_field('link_url')): ?>
            <a target="_blank" href="<?php echo get_field('link_url') ;?>" class="bouton-contour" style="color: <?php echo $couleur; ?>; border: 2px solid <?php echo $couleur; ?>;">Prendre rendez-vous avec moi <span class="icon-arrow-right"></span></a>
        <?php endif; ?>

        <!--<a href="<?php echo get_the_permalink(313).'?professionnel='. $post->post_name .'&service-demande='. get_field('domaine_personne')->post_name ?>" class="bouton-contour" style="color: <?php echo $couleur; ?>; border: 2px solid <?php echo $couleur; ?>;">Prendre rendez-vous avec moi <span class="icon-arrow-right"></span></a>-->

    </div>

    <section dkit-grid="col-8" class="container">
        <div class="services-items Domaine">
            <div class="items-wrapper">
							
							<a href="<?php echo get_field('lien_domaine')?>"><h2 class="<?php echo get_field('couleur', get_field('domaine_personne')); ?>"><?php echo get_field('abreviation_services', get_field('domaine_personne')) ?></h2></a>
                <div class="personne-info">
                    <h3 class="Nom_doc"><?php echo the_title(); ?></h3>
                    <span class="Info_doc_domaine">
                        <?php
												if(get_the_ID()==399){
													echo "Consultant en activité physique";} 
												else if(get_the_ID()==388){
													echo "Chiropraticienne";
												}
												else{
                        $genre = get_field('genre_personne');
                        echo get_field('nom_'.$genre, get_field('domaine_personne')); if( get_field('domaineAdd_personne') ): echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne'))); endif; }; ?>
                    </span>
                </div>

            <div style="clear:both;"></div>
            </div>
        </div>

        <div dkit-grid="col-12" class="Content">

	        <div class="bloc Description" dkit-grid="col-12">
						<?php if(get_field('txt_promo')){ ?>
            	<div class="promo" style="border: 1px solid <?= $couleur ?>;">
								<h2><?php echo get_field('title_promo'); ?></h2>
								<?php echo get_field('txt_promo'); ?>
							</div>
						<?php }; ?>
						<h2>À propos de moi</h2>
            <?php echo get_field('description_personne'); ?>
	        </div>


            <?php $images = get_field('galerie');
            if( $images ): ?>
            <div class="bloc Galerie" dkit-grid="col-12">
                <h2>Photos</h2>
                <div class="Galerie_roll" style="position: relative;">
                <?php foreach( $images as $image ): ?>
                   <div class="Photo" style="background-image:url('<?php echo $image['sizes']['500x425']; ?>')"></div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>


            <?php if( have_rows('videos') ): ?>
			<div class="bloc Videos" dkit-grid="col-12">
                <h2><?php
                    $count = count(get_field("videos"));
                    if($count > 1): echo"Vidéos";
                        else : echo"Vidéo";
                    endif;
                    ?></h2>
                <?php while ( have_rows('videos') ) : the_row(); ?>

                <div dkit-grid="col-12" id="Video-container">
	                <?php
			        $video_index_url = get_sub_field('lien_video', FALSE, FALSE); //U
			        $video_index_arr = parse_video_uri($video_index_url);
			        $video_index_type = $video_index_arr['type'];
			        $video_index_id = $video_index_arr['id'];
			        ?>

		          	<?php if( $video_index_type == 'youtube' ): ?>
                    	<iframe width="860" height="484" src="https://www.youtube.com/embed/<?php echo $video_index_id ?>?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                    	<iframe width="860" height="484" src="https://player.vimeo.com/video/<?php echo $video_index_id ?>?title=0&byline=0&portrait=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    <?php endif; ?>

                </div>
                <div style="clear:both;"></div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>


            <?php if( have_rows('temoignages') ): ?>
            <div class="bloc Temoignages" dkit-grid="col-12">
                <div class="Content">
                    <h2><?php
                    $count = count(get_field("temoignages"));
                    if($count > 1): echo"Témoignages";
                        else : echo"Témoignage";
                    endif;
                    ?></h2>

                    <?php
                    $count = count(get_field("temoignages"));
                    if($count > 1): echo"
                    <div class='slider_next'>
                        <span class='icon-uniE610 next'></span>
                    </div>

                    <div class='slider_prev'>
                        <span class='icon-arrow-left3 prev'></span>
                    </div>
                    ";
                    endif;
                    ?>

                    <div id="Partner-slider" dkit-grid="grid-wrapper" class="temoignages-carousel">

                    <?php while ( have_rows('temoignages') ) : the_row(); ?>

                        <div class="item">
                            <?php echo get_sub_field('texte_temoignage'); ?>
                            <h3><?php echo get_sub_field('nom_temoignage'); ?>,</h3>
                            <h4><?php echo get_sub_field('ville_temoignage'); ?></h4>
                        </div>

                    <?php endwhile; ?>

                    </div>
                </div>
            </div>
            <?php endif; ?>


            <?php if( have_rows('liens_personne') ): ?>
			<div class="bloc Liens" dkit-grid="col-12">
                <h2><?php
                    $count = count(get_field("liens_personne"));
                    if($count > 1): echo"Liens";
                        else : echo"Lien";
                    endif;
                    ?></h2>
                <div dkit-grid="col-12" class="Liens">
                <?php while ( have_rows('liens_personne') ) : the_row(); ?>

                <div dkit-grid="col-12" class="Liens_items">
                    <p>
                        <span class="icon-uniE033"></span>
                        <a href="<?php the_sub_field('liens'); ?>" target="_blank"><?php the_sub_field('texte') ; ?>
                        </a>
                    </p>
                </div>

                <?php endwhile; ?>
                </div>
			</div>
            <?php endif; ?>


        </div>
    </section>
</div>

 <?php endwhile;
endif; ?>

<?php
get_template_part( 'module-blog' );

get_footer(); ?>

<?php $images = get_field('galerie');
if( $images ): ?>
<script>
    $(document).ready(function(){
       MASONRY.init();
    });
</script>
<?php endif; ?>
