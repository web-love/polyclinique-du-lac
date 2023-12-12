<?php

get_header(); ?>

<?php get_template_part( 'module-header' ); ?>



 <!--DÉBUT CONTENT-->
<div dkit-grid="col-12" class="Services Fond">
    <div class="Aside" dkit-grid="col-4">

	    <?php $couleur_domaine = get_field('couleur'); ?>

   		<?php
        $args = array( 'post_type'  => 'equipe',
		   'orderby'    => 'name',
		   'order'      => 'ASC',
		   'posts_per_page' => -1 );
        $loop = new WP_Query( $args );
        $postid = get_the_ID();
        $compteur = 0;  ?>

        <?php if( $loop->found_posts > 0 ) : ?>

        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

          <?php if( get_field('couleur') == 'turquoise' ): $couleur = '#00f1f1'; //done
          elseif( get_field('couleur') == 'vert' ): $couleur = '#05761d'; //done
          elseif( get_field('couleur') == 'orange' ): $couleur = '#ff9000'; // done
          elseif( get_field('couleur') == 'jaune-vert' ): $couleur = '#b3de3e'; //done
          elseif( get_field('couleur') == 'rouge' ): $couleur = '#fc2b2c'; //done
          elseif( get_field('couleur') == 'violet' ): $couleur = '#9649ff'; //done
          elseif( get_field('couleur') == 'mauve' ): $couleur = '#a419a0'; //done
          elseif( get_field('couleur') == 'vert-pomme' ): $couleur = '#20d40d'; //done
          elseif( get_field('couleur') == 'bleu' ): $couleur = '#345fff'; //done
          elseif( get_field('couleur') == 'bleu-pale' ): $couleur = '#13a7cf'; //done
          elseif( get_field('couleur') == 'jaune' ): $couleur = '#e6d800'; //done
          endif;
          ?>

           <?php
            $domaine_personne = get_field('domaine_personne');
            $domaineAdd_personne = get_field('domaineAdd_personne');
            
            if (
                ($domaine_personne && $domaine_personne->ID == $postid) ||
                ($domaineAdd_personne && $domaineAdd_personne->ID == $postid)
            ) :
            ?>
           <?php if( $compteur == 0 ): ?>
           <span class="label">Nos professionnels</span>
           <?php endif; ?>

           <div class="Aside-item" dkit-grid="col-12">
                <div class="item_img" dkit-grid="col-12">
                    <?php if( get_field('photo_personne') ): ?>
			        <img src="<?php echo get_field('photo_personne')['url']; ?>" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
			        <?php else: ?>
			        <img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne'); ?>.jpg" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
			        <?php endif; ?>
                </div>
                <div class="item_desc" dkit-grid="col-12">
                    <span class="name">
                    <?php
                        echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?></span>
                    <span class="job"><?php
                    $genre = get_field('genre_personne');
                    if(get_the_ID()==399){echo "Consultant en activité physique";} else if(get_the_ID()==388){echo "Chiropraticienne";}
											
											else { echo get_field('nom_'.$genre, get_field('domaine_personne')); if( get_field('domaineAdd_personne') ): echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne'))); endif; };?></span>
                    <a href="<?php the_permalink(); ?>"  class="<?php echo $couleur_domaine ?>">En savoir plus <span class="icon-arrow-right"></span></a>
                </div>
            </div>

           <?php
        $compteur++;
        endif;
        endwhile; ?>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>

    <section dkit-grid="col-8" class="Service-container">
        <div class="services-items Domaine">
            <div class="items-wrapper">
                <h2 class="<?php echo get_field('couleur'); ?>"><?php echo get_field('abreviation_services'); ?></h2>
                <h3><?php echo the_title(); ?></h3>
            <div style="clear:both;"></div>
            </div>
        </div>
				
				<?php if( have_rows('contenu') ){ ?>
				<?php while ( have_rows('contenu') ) : the_row(); ?>
        <div dkit-grid="col-12" class="Content">
            <?php echo get_sub_field('texte_services'); ?>

            <?php
            if( have_rows('questions_services') ): ?>
            <h2>Questions fréquentes</h2>
                <?php while ( have_rows('questions_services') ) : the_row(); ?>

                <div dkit-grid="col-12" class="Questions">
                    <div dkit-grid="col-12" class="Question" dkit-vertical="parent">
	                    <div dkit-grid="col-11"><?php  the_sub_field('question'); ?></div>
	                    <div dkit-grid="col-1" class="icon-uni60 Plus" dkit-vertical="child" dkit-verticalstopat="100"></div>
                    </div>
                    <div dkit-grid="col-12" class="Reponse">
                        <?php echo the_sub_field('reponse'); ?>
                    </div>

                </div>

                <?php endwhile;

            endif; ?>
					
						<?php if( have_rows('temoignages') ): ?>
            <div class="bloc Temoignages" dkit-grid="col-12">
                <div class="Content">
                    <h2><?php
                    $count = count(get_sub_field("temoignages"));
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
					

            <?php
            if( have_rows('liens_utiles') ): ?>
                <h2>Liens utiles</h2>
                <div dkit-grid="col-12" class="Liens">

                    <?php while ( have_rows('liens_utiles') ) : the_row(); ?>

                    <div dkit-grid="col-12" class="Liens_items">
                            <span class="icon-uniE033"></span>
                            <a href="<?php echo the_sub_field('lien'); ?>" target="_blank"><?php echo the_sub_field('titre_lien'); ?></a>
                    </div>

                    <?php endwhile; ?>

                </div>
            <?php endif; ?>
        </div>
			<?php endwhile;
			} ?>
    </section>
</div>

<?php
get_template_part( 'module-blog' );

get_footer(); ?>
<script>
    $(document).ready(function(){
       FIXVERTICAL.init();
    });
</script>
