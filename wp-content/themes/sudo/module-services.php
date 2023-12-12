<div dkit-grid="col-12" class="Section">
    <div class="services_bg"></div>
    
    <!-- DÉBUT SERVICES -->
    <section dkit-grid="grid-wrapper" class="Services" dkit-fixHeight="parent">
        <div dkit-grid="grid-wrapper" class="ligne">
            <h1>Services offerts</h1>
        </div>

        <div dkit-grid="col-12">
          
        <?php $args = array(
            'post_type' => 'service',
            'order'         => 'ASC',
            'orderby'       => 'name',
            'posts_per_page'=> -1 
        );
        
        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <div dkit-grid="col-3" class="services-items">
           <div class="border" dkit-fixHeight="child"
               dkit-fixheightstopat="530"
               dkit-fixheightorient="center">
               <a href="<?php the_permalink(); ?>">
              <div class="items-wrapper <?php echo get_field('couleur'); ?>">
                    <h2><?php the_field('abreviation_services'); ?></h2>
                    <h3><?php the_title(); ?></h3>
                    <div style="clear:both;"></div>
                </div>
           </a>
           </div>
            
        </div>
       <?php endwhile; ?>
       <?php wp_reset_postdata(); ?>
            
        </div>

    </section>

    <?php get_template_part( 'module-apropos' ); ?>
    
</div>