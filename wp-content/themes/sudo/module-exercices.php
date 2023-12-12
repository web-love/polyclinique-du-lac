<section dkit-grid="col-12" class="Blog">
<p style="display:none;"><?php echo $postID; ?></p>

    <div class="Blog_roll" data-grid="col-12">

    <?php
        $args = array(
                'post_type'      	=> 'exercice',
                'posts_per_page' 	=> 3,
                'orderby'        	=> 'post_date',
                'order'          	=> 'DESC',
                'post__not_in'		=> array($postID),
                'suppress_filters' 	=> true );
        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post();

        if( get_field('couleur_titre') == 'blanc' ):
	        $couleur_titre = '#e5eff3';
	        $couleur_fond = 'noir';
	    else :
	        $couleur_titre = '#15150d';
	        $couleur_fond = 'blanc';
	    endif;
    ?>



    <div class="left-small" data-grid="col-4" style="background-image: url('<?php echo get_field("background")["sizes"]["600x600"]; ?>');">

	    <div class="filter <?php echo $couleur_fond ?>" dkit-grid="col-12">
	        <a class="block-link" href="<?php echo get_permalink(); ?>"></a>
	        <div class="post-flash" data-grid="col-12">
	            <h2 style="color:<?php echo $couleur_titre;?>;"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
	            <?php $terms = get_field('categorie_article');

	            if( $terms ): ?>
	                <span class="type" style="color:<?php echo $couleur_titre;?>;">
	                <?php
	                $count = 1;
					foreach($terms as $term) :
			            if($count != 1):
			            echo ', ';
			            endif;
			            echo $term->name;
			            $count++;
			        endforeach;
	                ?>
	                </span>
	            <?php endif; ?>

	            <!--<span class="date" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_time('j F') ?></span>-->
	        </div>
					<?php if(get_field('article_auteur')){ ?>
						<div class="post-author">
										<div class="author-img">
												<?php $auteur_id = get_field('article_auteur'); ?>
												<?php if( get_field('photo_personne', $auteur_id) ): ?>
									<img src="<?php echo get_field('photo_personne'), $auteur_id['url']; ?>" alt="<?php echo get_field('titre_personne', $auteur_id).' '.get_field('prenom_personne', $auteur_id).' '.get_field('nom_personne', $auteur_id); ?>">
									<?php else: ?>
									<img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne', $auteur_id); ?>.jpg" alt="<?php echo get_field('titre_personne', $auteur_id).' '.get_field('prenom_personne', $auteur_id).' '.get_field('nom_personne', $auteur_id); ?>">
									<?php endif; ?>
										</div>
										<div class="author-flash">
												<a href="<?php echo get_permalink($auteur_id); ?>" class="name"  style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_title($auteur_id) ?></a>
												<span class="job" style="color:<?php echo $couleur_titre;?>;">
													 <?php
																$genre = get_field('genre_personne', $auteur_id);
																echo get_field('nom_'.$genre, get_field('domaine_personne', $auteur_id));
																if( get_field('domaineAdd_personne', $auteur_id) ): echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne', $auteur_id))); endif;
														?>
												</span>
												<?php wp_reset_postdata();?>
										</div>
								</div>
						<?php } ?>
				<div class="shadow-tile"></div>
			</div>
		</div>
		<?php endwhile; ?>

	</div>  

</section>
