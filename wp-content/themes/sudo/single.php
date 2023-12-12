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
 <div dkit-grid="col-12" class="Article">
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
        <p class="domaine">
            <?php 
                $genre = get_field('genre_personne', get_field('article_auteur'));
                $nomComplet = get_field('titre_personne', get_field('article_auteur')).' '.get_field('prenom_personne', get_field('article_auteur')).' '.get_field('nom_personne', get_field('article_auteur'));
                echo get_field('nom_'.$genre, get_field('domaine_personne', get_field('article_auteur'))); 
                if( $nomComplet != 'Dre Marie-Claude Déry' ) :
                    if( get_field('domaineAdd_personne', get_field('article_auteur')) ): 
                        echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne', get_field('article_auteur')))); 
                    endif;
                endif;  
            ?>        
        </p>
        

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
    
    <?php if( get_field('article_auteur') ): ?><section dkit-grid="col-8" class="container"><?php else: ?><section dkit-grid="col-8" class="container" style="float:right;"><?php endif; ?>
        <div dkit-grid="col-6" class="titre">
                <p>
                    <span class="domaine" style="color:<?php echo $couleur_titre;?>;">
                    <?php $terms = get_field('categorie_article');
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
                    endforeach; endif; ?>
                    </span>
                    <span class="separateur" style="color:<?php echo $couleur_titre;?>;">|</span> 
                    <span class="date" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_time('j F') ?></span>
                </p>
               <h1 style="color:<?php echo $couleur_titre;?>;"><?php the_title(); ?></h1>
        </div>
        
        <div dkit-grid="col-12" class="content">
            <a href="<?php echo get_the_permalink(291) ?>" class="retour"><span class="icon-arrow-left"></span>Retour aux articles</a>
            
            <?php

            if( have_rows('contenu_article') ):

                while ( have_rows('contenu_article') ) : the_row();

                switch ( get_row_layout() ) {
                    case 'texte':
                        if( get_sub_field('texte_article') ) : ?>
                        <?php the_sub_field('texte_article') ?>
                        <?php else : endif; 
                        break;
                        
                    case 'images_article':
                        $images = get_sub_field('image_article');

                        if( $images ): ?>
                            
                            <div class="Galerie_roll" style="position: relative;" dkit-grid="col-12" dkit-masonry="parent" dkit-masonrynumcol="2" dkit-masonrygutter="12" dkit-masonrystopat="520" dkit-masonryrespat="520">
			                <?php foreach( $images as $image ): ?>
			                   <div class="Photo" dkit-masonry="child"><img src="<?php echo $image['url']; ?>"></div>
			                <?php endforeach; ?>
			                </div>

                            <div style="clear:both;"></div>

                            <?php if( get_sub_field('credit_photos') ): ?><p class="credit-photo">Crédit photos : <?php echo strip_p( get_sub_field('credit_photos') ); ?></p><?php else : endif; ?>

                        <?php else : endif;
                        break;
                        
                    case 'citation':
                        if( get_sub_field('blockquote_article') ): ?>
                            <blockquote><?php echo strip_p( get_sub_field('blockquote_article') ); ?></blockquote>
                            
                            <?php if( get_sub_field('auteur_blockquote') ): ?><p class="auteur-blockquote">- <?php echo strip_p( get_sub_field('auteur_blockquote') ); ?></p><?php else : endif; ?>                            
                            
                        <?php else : endif;
                        break;
                        
                    case 'titre_texte': ?>
                            <h2><?php echo get_sub_field('titre_article'); ?></h2>
                            <?php the_sub_field('texte_article') ?>
                        <?php break;
                }

                endwhile;

            else : endif; ?>
            
            <a href="<?php echo get_the_permalink(291) ?>" class="retour"><span class="icon-arrow-left"></span>Retour aux articles</a>
            
            <p class="like">Vous aimez cette chronique, partagez-la !</p>
            <div class="addthis_boite">
            <?php do_action('addthis_widget',get_permalink($post->ID), get_the_title($post->ID), 'large_toolbox'); ?>
            </div>
        </div>
    </section>
</div>

 <?php endwhile; else: endif; ?>
 
 <?php get_template_part('module-blog') ?>
 
<?php get_footer(); ?>

<?php $images = get_field('galerie');
if( $images ): ?>
<script>
    $(document).ready(function(){
       MASONRY.init(); 
    });
</script>
<?php endif; ?>