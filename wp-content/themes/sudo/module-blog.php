<section dkit-grid="col-12" class="Blog<?php if( is_home() || is_front_page() ):?> Accueil<?php endif; ?>">
	
    <?php if( is_home() || is_front_page() ):
		
        for ($i = 1; $i <= 4; $i++) {
            if( $i == 1 ) : ?>
            <div class="blog-left" data-grid="col-8">
            <?php endif; ?>

            <?php
            if( get_field('couleur_titre', get_field('article_'.$i)) == 'blanc' ):
		        $couleur_titre = '#F5F5F5';
		        $couleur_fond = 'noir';
		    else :
		        $couleur_titre = '#15150d';
		        $couleur_fond = 'blanc';
		    endif;

		    switch( $i ):
		    	case 1:
		    		$size = "950x600";
		    	break;
		    	case 4:
		    		$size = "600x950";
		    	break;
		    	default:
		    		$size = "600x600";
		    endswitch;

            ?>
					<div dkit-grid="grid-wrapper" class="blog-wrap">
						<div dkit-grid="grid-wrapper" class="ligne">
							<h1>Chroniques</h1>
						</div>
					</div>
            <div <?php if( $i == 1 ): ?>class="left-large" data-grid="col-12"<?php elseif( $i == 4 ): ?>class="right-large" data-grid="col-12"<?php else: ?>class="left-small" data-grid="col-6"<?php endif; ?> style="background-image: url('<?php if(get_field("background", get_field("article_".$i))){
							echo get_field("background", get_field("article_".$i))["sizes"][$size];
						}else{
							echo bloginfo('template_directory')."/img/bg_chronique.jpg";
						}?>')">
						

	            <div class="filter <?php echo $couleur_fond ?>" dkit-grid="col-12">
                <a class="block-link" href="<?php the_permalink( get_field('article_'.$i ) ); ?>"></a>

                <div class="post-flash" data-grid="col-12">
                    <h2><a href="<?php get_permalink(); ?>" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_title( get_field('article_'.$i) ); ?></a></h2>

                    <?php $terms = get_field('categorie_article', get_field('article_'.$i));

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

                    <span class="date" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_time('j F', get_field('article_'.$i)) ?></span>
                </div>

                <div class="post-author">
                    <div class="author-img">
                        <?php $auteur_id = get_field('article_auteur', get_field('article_'.$i)); ?>
                        <?php if( get_field('photo_personne', $auteur_id) ): ?>
				        <img src="<?php echo get_field('photo_personne', $auteur_id)['url']; ?>" alt="<?php echo get_field('titre_personne', $auteur_id).' '.get_field('prenom_personne', $auteur_id).' '.get_field('nom_personne', $auteur_id); ?>">
				        <?php else: ?>
				        <img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne', $auteur_id); ?>.jpg" alt="<?php echo get_field('titre_personne', $auteur_id).' '.get_field('prenom_personne', $auteur_id).' '.get_field('nom_personne', $auteur_id); ?>">
				        <?php endif; ?>
                    </div>
                    <div class="author-flash">
                        <a href="<?php echo get_permalink($auteur_id); ?>" class="name" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_title($auteur_id) ?></a>
                        <span class="job" style="color:<?php echo $couleur_titre;?>;">                        	
                        	<?php 

				                $genre = get_field('genre_personne', $auteur_id);
						        echo get_field('nom_'.$genre, get_field('domaine_personne', $auteur_id));

				                if ( get_the_title($auteur_id) != 'Dre Marie-Claude Déry'){

				                    if( get_field('domaineAdd_personne', $auteur_id) ){
				                        echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne', $auteur_id))); }

				                }
			                ?>  
                        </span>
                        <?php wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <div class="shadow-tile white"></div>

            </div>
            </div>
            <?php if( $i == 3 ): ?>
            </div>
            <div class="blog-right" data-grid="col-4">
            <?php endif;
        } ?>
        </div>

    <?php else: ?>

    <div class="Blog_roll" data-grid="col-12">

    <?php
        $args = array(
                'post_type'      => 'post',
                'posts_per_page' => 4,
                'orderby'        => 'post_date',
                'order'          => 'DESC',
                'suppress_filters' => true );
        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post();

        if( get_field('couleur_titre') == 'blanc' ):
	        $couleur_titre = '#F5F5F5';
	        $couleur_fond = 'noir';
	    else :
	        $couleur_titre = '#15150d';
	        $couleur_fond = 'blanc';
	    endif;
    ?>

    <div class="left-small" data-grid="col-4" style="background-image: url('<?php if( get_field("background") ): echo get_field("background")["url"]; else: echo bloginfo("template_directory")."/img/bg_chronique.jpg"; endif; ?>');">

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

	            <span class="date" style="color:<?php echo $couleur_titre;?>;"><?php echo get_the_time('j F') ?></span>
	        </div>
	        <div class="post-author">
	                <div class="author-img">
	                    <?php $auteur_id = get_field('article_auteur'); ?>
	                    <?php if( get_field('photo_personne', $auteur_id) ): ?>
				        <img src="<?php echo get_field('photo_personne', $auteur_id)['url']; ?>" alt="<?php echo get_field('titre_personne', $auteur_id).' '.get_field('prenom_personne', $auteur_id).' '.get_field('nom_personne', $auteur_id); ?>">
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

				                if ( get_the_title($auteur_id) != 'Dre Marie-Claude Déry'){

				                    if( get_field('domaineAdd_personne', $auteur_id) ){
				                        echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne', $auteur_id))); }

				                }
			                ?>  
	                    </span>
	                    <?php wp_reset_postdata();
	                    ?>
	                </div>
	            </div>
				<div class="shadow-tile"></div>
			</div>
		</div>
		<?php endwhile; ?>

	</div>
    <?php endif; ?>

</section>
