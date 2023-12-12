<!-- DEBUT MENU -->

	<div class="Menu_out"></div>

    <div dkit-grid="grid-wrapper" class="Position">
        <div dkit-grid="col-3" class="logo">
           <a href="<?php echo get_home_url(); ?>"><img src="<?php bloginfo('template_directory'); ?>/img/logo_poly.svg" onerror="this.src='<?php bloginfo('template_directory'); ?>/img/logo_poly.png'" alt=""></a>
        </div>

        <div dkit-grid="col-9" class="Menu">
          <div dkit-grid="col-12" class="Rendezvous">

          <?php $the_query = new WP_Query( 'page_id=127' );

          if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

              <a target="_blank" class="rdv" href="<?php echo get_field('bouton_rendez-vous_url'); ?>"><?php echo get_field('bouton_rendez-vous'); ?><span class="icon-arrow-right"></span></a>
							<?php $first_tel = true;
	            while( $first_tel && has_sub_field('telephone') ): ?>
	                <?php $numero = preg_replace( '/\D/', '', get_sub_field('numero') ); ?>
	                <p><a href="tel:+1<?php echo $numero ?>"><?php echo get_sub_field('numero'); ?></a></p>

	            <?php endwhile;
	            ?>

            <?php endwhile;
        endif;
        wp_reset_postdata();?>
          </div>

          <div id="hamburger">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </div>

           <nav dkit-grid="col-12">
               <ul>
                    <li><a href="<?php echo get_the_permalink(136); ?>" class="Arrow">Services offerts<span class="icon-arrow-down"></span></a>
                    <div class="triangle-services"></div></li
                    ><li><a href="<?php echo get_the_permalink(285); ?>" class="Arrow">Notre équipe<span class="icon-arrow-down"></span></a>
                    <div class="triangle-equipe"></li
                    ><li><a href="<?php echo get_the_permalink(303); ?>">Exercices</a></li
                    ><li><a href="<?php echo get_the_permalink(291); ?>">Chroniques</a></li
                    ><li><a href="<?php echo get_the_permalink(127); ?>">Nous joindre</a></li>
                </ul>
          </nav>

       </div>
    </div>
    <nav dkit-grid="col-12" class="menu-mobile">
       <ul>
            <li><a href="<?php echo get_the_permalink(136); ?>">Services offerts</a></li
            ><li><a href="<?php echo get_the_permalink(285); ?>">Notre équipe</a></li
            ><li><a href="<?php echo get_the_permalink(303); ?>">Exercices</a></li
            ><li><a href="<?php echo get_the_permalink(291); ?>">Chroniques</a></li
            ><li><a href="<?php echo get_the_permalink(127); ?>">Nous joindre</a></li
            >
                <li>
                <?php $the_query = new WP_Query( 'page_id=127' ); 
                if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <a target="_blank" href="<?php echo get_field('bouton_rendez-vous_url'); ?>"><?php echo get_field('bouton_rendez-vous'); ?></a></li>
						

	          
						<?php $first_tel = true;
						if( $first_tel && has_sub_field('telephone') ): ?>
						<?php $numero = preg_replace( '/\D/', '', get_sub_field('numero') ); ?>
						<li><a class="tel" href="tel:+1<?php echo $numero ?>"><?php echo get_sub_field('numero'); ?></a></li>
					<?php endif;
						?>

						<?php endwhile;
				endif;
				wp_reset_postdata();?>
			  </ul>
    </nav>
<!-- FIN MENU -->
