<?php

get_header(); ?>

<?php get_template_part( 'module-header' ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
    if( get_field('couleur_titre') == 'blanc' ):
        $couleur_titre = '#F5F5F5';
    else :
        $couleur_titre = '#F5F5F5';
    endif;
?>
 <div dkit-grid="col-12" class="Article exercice">
    <div dkit-grid="col-4" class="Aside author">
      <?php if( get_field('article_auteur') ): ?>
        <div class="item">
           <?php if( get_field('photo_personne', get_field('article_auteur')) ): ?>
	        <img src="<?php echo get_field('photo_personne', get_field('article_auteur'))['url']; ?>" alt="<?php echo get_field('titre_personne', get_field('article_auteur')).' '.get_field('prenom_personne', get_field('article_auteur')).' '.get_field('nom_personne', get_field('article_auteur')); ?>">
	        <?php else: ?>
	        <img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne', get_field('article_auteur')); ?>.jpg" alt="<?php echo get_field('titre_personne', get_field('article_auteur')).' '.get_field('prenom_personne', get_field('article_auteur')).' '.get_field('nom_personne', get_field('article_auteur')); ?>">
	        <?php endif; ?>
       </div>

        <h1><?php echo get_field('titre_personne', get_field('article_auteur')).' '.get_field('prenom_personne', get_field('article_auteur')).' '.get_field('nom_personne', get_field('article_auteur')); ?></h1>
        <p class="domaine"><?php $genre = get_field('genre_personne', get_field('article_auteur'));
            echo get_field('nom_'.$genre, get_field('domaine_personne', get_field('article_auteur'))) ?></p>
        <p><?php echo get_field('mini_description_personne', get_field('article_auteur')); ?></p>

        <?php if( get_field('facebook', get_field('article_auteur') ) ||
                  get_field('twitter', get_field('article_auteur') ) ||
                  get_field('linkedin', get_field('article_auteur') ) ||
                  get_field('instagram', get_field('article_auteur') ) ||
                  get_field('youtube', get_field('article_auteur') )
                ): ?>
            <ul>
                <?php if( get_field('facebook', get_field('article_auteur') ) ): ?>
                <li><a href="<?php echo get_field('facebook'); ?>" target="_blank"><span class="icon-uniE01D"></span></a></li
                ><?php endif; ?>
                <?php if( get_field('twitter', get_field('article_auteur') ) ): ?>
                <li><a href="<?php echo get_field('twitter'); ?>" target="_blank"><span class="icon-uniE018"></span></a></li
                ><?php endif; ?>
                <?php if( get_field('linkedin', get_field('article_auteur') ) ): ?>
                <li><a href="<?php echo get_field('linkedin'); ?>" target="_blank"><span class="icon-uniE01D"></span></a></li
                ><?php endif; ?>
                <?php if( get_field('instagram', get_field('article_auteur') ) ): ?>
                <li><a href="<?php echo get_field('instagram'); ?>" target="_blank"><span class="icon-uniE00B"></span></a></li
                ><?php endif; ?>
                <?php if( get_field('youtube', get_field('article_auteur') ) ): ?>
                <li><a href="<?php echo get_field('youtube'); ?>" target="_blank"><span class="icon-uniE012"></span></a></li
                ><?php endif; ?>
            </ul>
        <?php else : ?>
        <?php endif; ?>

        <a href="<?php echo get_permalink(get_field('article_auteur')); ?>" class="plus">En savoir plus sur <?php echo get_field('prenom_personne', get_field('article_auteur')); ?><span class="icon-arrow-right"></span></a>
    <?php endif; ?>
    </div>

    <?php if( get_field('article_auteur') ): ?>
      <section dkit-grid="col-8" class="container">

    <?php else: ?>
      <section dkit-grid="col-8" class="container" style="float:right;">
    <?php endif; ?>
    
        <div dkit-grid="col-6" class="titre">
                <p>
                    <span class="domaine" style="color:<?php echo $couleur_titre;?>;">
                    <?php 
                      $postID = get_the_ID();
                      $terms = get_field('categorie_article');
                      $terms_exercices = get_field('categorie_exercices');
                      $count = 1;
                      if( $terms ) :
                        foreach($terms as $term) :
                          if($count != 1):
                            echo ', ';
                          endif;
                          echo $term->name;
                          $count++;
                        endforeach;
                      else :
                        foreach($terms_exercices as $term_exercice) :
                          if($count != 1):
                            echo ', ';
                            endif;
                          echo $term_exercice->name;
                          $count++;
                        endforeach;
                      endif;
                    ?>
                    </span>
                </p>
               <h1 style="color:<?php echo $couleur_titre;?>;"><?php the_title(); ?></h1>
        </div>

        <p style="display:none;"><?php echo $postID .'PATATE'; ?></p>

        <div dkit-grid="col-12" class="content">

            <div class="bloc Description" dkit-grid="col-12">
              <h2>Description de l'exercice</h2>
              <?php echo get_field('description'); ?>
              <p><img src="<?php echo get_field('background')['url']; ?>"></p>
            </div>

              <?php
                if( have_rows('section_repeater') ):

                  // loop through the rows of data
                  while ( have_rows('section_repeater') ) : the_row();

                    if( get_sub_field('bloc_de_texte') ):
              ?>
                      <div dkit-grid="col-12">  
                        <p><?php echo get_sub_field('bloc_de_texte'); ?></p>
                      </div>
              <?php
                    endif;

                    if( get_sub_field('image_pleine_largeur') ):
              ?>
                      <div dkit-grid="col-12">  
                        <img src="<?php echo get_sub_field('image_pleine_largeur')['url']; ?>" />
                      </div>
              <?php
                    endif;

                    if( get_sub_field('video_repeater') ):
              ?>
                      <div dkit-grid="col-12" id="Video-container">
                        <?php
                          $video_index_url = get_sub_field('video_repeater', FALSE, FALSE); //U
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
              <?php
                    endif;

                    if( get_sub_field('gallery_repeater') ):
                      $images = get_sub_field('gallery_repeater');              
                      
                      if( $images ): ?>
                        <div class="bloc Galerie" dkit-grid="col-12">
                          <h2>Photos</h2>
                          <div class="Galerie_roll" style="position: relative;" dkit-grid="col-12" dkit-masonry="parent" dkit-masonrynumcol="2" dkit-masonrygutter="12" dkit-masonrystopat="520" dkit-masonryrespat="520">
                            <?php foreach( $images as $image ): ?>
                              <div class="Photo fixHeight" dkit-masonry="child"><img src="<?php echo $image['url']; ?>"></div>
                            <?php endforeach; ?>
                          </div>
                        </div>
                        <script>
                          jQuery(document).ready(function($){
                             MASONRY.init();
                          });
                          </script>
              <?php endif; ?>
              <?php
                    endif;

                  endwhile;

                else :

                // no rows found

                endif;
              ?>

            <!-- <?php
            if( have_rows('contenu_article') ):

                while ( have_rows('contenu_article') ) : the_row();

                switch ( get_row_layout() ) {
                    case 'texte':
                        if( get_sub_field('texte_article') ) : ?>
                        <p><?php echo strip_p( get_sub_field('texte_article') ); ?></p>
                        <?php else : endif;
                        break;

                    case 'images_article':
                        $images = get_sub_field('image_article');

                        if( $images ): ?>
                        <div class="bloc Galerie" dkit-grid="col-12">
                            <h2>Photos</h2>
                            <div class="Galerie_roll" style="position: relative;" dkit-grid="col-12" dkit-masonry="parent" dkit-masonrynumcol="2" dkit-masonrygutter="12" dkit-masonrystopat="520" dkit-masonryrespat="520">
                            <?php foreach( $images as $image ): ?>
                               <div class="Photo" dkit-masonry="child"><img src="<?php echo $image['url']; ?>"></div>
                            <?php endforeach; ?>
                            <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                        <div style="clear:both;"></div>
                        <?php endif;
                        break;

                    case 'citation':
                        if( get_sub_field('blockquote_article') ): ?>
                            <blockquote><?php echo strip_p( get_sub_field('blockquote_article') ); ?></blockquote>

                            <?php if( get_sub_field('auteur_blockquote') ): ?><p class="auteur-blockquote">- <?php echo strip_p( get_sub_field('auteur_blockquote') ); ?></p><?php else : endif; ?>

                        <?php else : endif;
                        break;

                    case 'titre_texte': ?>
                            <h2><?php echo get_sub_field('titre_article'); ?></h2>
                            <p><?php echo strip_p( get_sub_field('texte_article') ); ?></p>
                        <?php break;
                }

                endwhile;

            else : endif; ?> -->
						<div style="clear:both;"></div>
            <a href="<?php echo get_the_permalink(303) ?>" class="retour"><span class="icon-arrow-left"></span>Retour aux exercices</a>

            <p class="like">Vous aimez cet exercice, partagez-le !</p>
            <div class="addthis_boite">
            <?php do_action('addthis_widget',get_permalink($post->ID), get_the_title($post->ID), 'large_toolbox'); ?>
            </div>
        </div>
    </section>
</div>

<style>
  .fixHeight{
    height: auto !important;
  }
</style>



 <?php endwhile; else: endif; ?>
<?php wp_reset_postdata();?>

 <?php include(locate_template('module-exercices.php')); ?>

<?php get_footer(); ?>

