<?php if( get_field('couleur_titre') == 'blanc' ):
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
    <div id="post-<?php the_ID(); ?>" class="post-flash" data-grid="col-12">

        <h2 style="color:<?php echo $couleur_titre;?>;">
	        <a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a>
	    </h2>

        <span class="type" style="color:<?php echo $couleur_titre;?>;">
        <?php $terms = get_field('categorie_article');
        $count = 1;
        foreach($terms as $term) :
            if($count != 1):
            echo ', ';
            endif;
            echo $term->name;
            $count++;
        endforeach; ?>
        </span>

        <span class="date" style="color:<?php echo $couleur_titre;?>;">
        	<?php echo get_the_time('j F') ?>
		</span>

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
        </div>

    </div>
<div class="shadow-tile"></div>
	</div>
</div>
