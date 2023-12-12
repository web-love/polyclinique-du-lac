<?php /* Template name: Confidentialité */

get_header(); 

get_template_part( 'module-header' ); ?>

<section dkit-grid="col-12" class="Services_section Formulaires">
    <h1 class="titre"><?php echo strip_p( get_the_title() ); ?></h1>
</section>

<section dkit-grid="col-12" class="Contact">
    <div dkit-grid="grid-wrapper" class="politique">
    
    <?php
    if( have_rows('contenu') ):

        while ( have_rows('contenu') ) : the_row();

            if( get_row_layout() == 'contenu' ): ?>

                <h2><?php the_sub_field('titre');?></h2>
                <?php the_sub_field('texte') ; ?>

            <?php endif;

        endwhile;

    else : endif;
    wp_reset_postdata(); ?>
    </div>
</section>

<?php get_footer(); ?>