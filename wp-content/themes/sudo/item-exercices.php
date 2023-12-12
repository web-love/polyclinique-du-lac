<?php if( get_field('couleur_titre') == 'blanc'): 
        $couleur_titre = '#e5eff3';
        $couleur_fond = 'noir';
    else :
        $couleur_titre = '#15150d';
        $couleur_fond = 'blanc';
    endif;
?>
 
  <div class="left-small" data-grid="col-4" style="background-image: url('<?php if( get_field("background") ): $bgImage = get_field('background'); echo $bgImage["url"]; else: echo bloginfo("template_directory")."/img/bg_chronique.jpg"; endif; ?>');">
	  
	<div class="filter <?php echo $couleur_fond ?>" dkit-grid="col-12">
    <a class="block-link" href="<?php echo get_permalink(); ?>"></a>
    <div id="post-<?php the_ID(); ?>" class="post-flash" data-grid="col-12">
    
        <h2 style="color:<?php echo $couleur_titre;?>;">
	        <a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a>
	    </h2>

        <span class="type no_after" style="color:<?php echo $couleur_titre;?>;">
        <?php $terms = get_field('categorie_exercices');
        foreach($terms as $term) : 
            echo $term->name;
        endforeach; ?>
        </span>
        
    </div>
	</div>
    
<div class="shadow-tile"></div>
</div>