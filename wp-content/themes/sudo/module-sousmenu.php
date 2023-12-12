<div class="Sousmenu-services">
    <div dkit-grid="grid-wrapper" class="Services-items">
       <?php 
					$args = array(
						 'post_type' => 'service', 
						 'meta_key'  => 'abreviation_services',
						 'orderby'   => 'abreviation_services',
						 'order'     => 'ASC',
						 'meta_key'  => 'consultants_externes_services',
						 'meta_value'	=> 'non', 
						 'posts_per_page' => -1
					);
					$loop = new WP_Query( $args );

					while ( $loop->have_posts() ) : $loop->the_post(); ?>
									<div dkit-grid="col-3" class="Sousmenu-services-items">
											<a href="<?php the_permalink()?>">
												<h2><?php echo get_field('abreviation_services'); ?></h2>
												<h3><?php the_title(); ?></h3>
											</a>
									</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
        <div style="clear:both;"></div>
    </div>
	<?php 
		$args = array(
			 'post_type' => 'service', 
			 'meta_key'  => 'abreviation_services',
			 'orderby'   => 'abreviation_services',
			 'order'     => 'ASC',
			 'meta_key'  => 'consultants_externes_services',
			 'meta_value'	=> 'oui', 
			 'posts_per_page' => -1
		);
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) : ?>
		<div dkit-grid="grid-wrapper" class="Services-items">
			<div dkit-grid="grid-wrapper" class="ligne">
		   <h1>Services externes</h1>
			</div>
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<div dkit-grid="col-3" class="Sousmenu-services-items">
						<a href="<?php the_permalink()?>">
							<h2><?php echo get_field('abreviation_services'); ?></h2>
							<h3><?php the_title(); ?></h3>
						</a>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
			<div style="clear:both;"></div>
    </div>
	<?php endif; ?>
</div>

<div class="Sousmenu-equipes">
    <div dkit-grid="grid-wrapper" class="Equipes-items">
		<?php $args = array(
		   'post_type'  => 'equipe',
		   'meta_key'	=> 'consultant_externe',
		   'meta_value'	=> 'non', 
		   'orderby'    => 'name',
		   'order'      => 'ASC',
		   'posts_per_page' => -1
		);
		$loop = new WP_Query( $args );
		
		while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php if(get_field('mini_description_personne')){ ?>
	        <div dkit-grid="col-3" class="Sousmenu-equipes-items">
	            <a href="<?php the_permalink() ?>">
	                <h2><?php the_title(); ?></h2>
	                <h3><?php $genre = get_field('genre_personne');
							if(get_the_ID()==388){echo "Chiropraticienne";}																		else{			 
	            echo get_field('nom_'.$genre, get_field('domaine_personne')); if( get_field('domaineAdd_personne') ): echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne'))); endif; }; ?></h3>
	            </a>
            </div>
				<?php } ?>
       <?php endwhile; ?>
       <?php wp_reset_postdata(); ?>
    </div> 

	<?php $args = array(
	   'post_type'  => 'equipe',
	   'meta_key'	=> 'consultant_externe',
	   'meta_value'	=> 'oui', 
	   'orderby'    => 'name',
	   'order'      => 'ASC',
		'posts_per_page' => -1
	);
	
	$loop = new WP_Query( $args );
	
	if ( $loop->have_posts() ) : ?>
	<div dkit-grid="grid-wrapper" class="Equipes-items">
       
		<div dkit-grid="grid-wrapper" class="ligne">
		   <h1>Consultants externes</h1>
		</div>
      
		<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		    <div dkit-grid="col-3" class="Sousmenu-equipes-items">
            <a href="<?php the_permalink() ?>">
                <h2><?php the_title(); ?></h2>
                <h3><?php $genre = get_field('genre_personne');
								if(get_the_ID()==399){echo "Consultant en activité physique";} else{
            	echo get_field('nom_'.$genre, get_field('domaine_personne')); if( get_field('domaineAdd_personne') ): echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne'))); endif; };?></h3>
            </a>
            </div>
       <?php endwhile; 
       endif; ?>
       <?php wp_reset_postdata(); ?>
    </div>     
</div>