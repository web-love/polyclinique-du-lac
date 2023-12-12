<section dkit-grid="grid-wrapper" class="apropos">
        <div dkit-grid="grid-wrapper" class="ligne">
            <h1>À propos de nous</h1>
        </div>

        
    <div class="info_apropos">
        <h1>
						<?php
            $args = array(
                'post_type' => 'service',
                'posts_per_page' => -1
            );
            $the_query = new WP_Query( $args );
      
             echo "$the_query->found_posts services";
            ?>
        </h1>
        <p>
            <span class="separation">|</span>
            <?php the_field('soustitre_apropos'); ?>
        </p>
        <?php the_field('texte_apropos'); ?>
        <div dkit-grid="col-12">
            <a href="<?php echo get_the_permalink(285); ?>" class="bouton-contour"><?php the_field('texte_bouton'); ?><span class="icon-arrow-right next"></span>
            </a>
        </div>
    </div>

    <div dkit-grid="grid-wrapper" class="ligne home-chroniques">
      <h1>Chroniques</h1>
    </div>

</section>
