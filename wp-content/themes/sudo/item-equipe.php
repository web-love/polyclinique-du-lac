<div dkit-grid="col-4" class="services-items">
	<?php if(get_field('mini_description_personne')){ ?>
   <a href="<?php echo get_permalink(); ?>">
	<?php } else{ ?>
	 <div>
	<?php } ?>
    <div class="items-wrapper equipe <?php echo get_field('couleur', get_field('domaine_personne')) ?>">
       <div dkit-fixHeight="child"
           dkit-fixheightstopat="530"
           dkit-fixheightorient="top"
           class="fix-height">
		   
		<?php if( get_field('photo_personne') ): ?>
        <img src="<?php echo get_field('photo_personne')['url']; ?>" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
        <?php else: ?>
        <img src="<?php bloginfo('template_directory'); ?>/img/lorem_<?php echo get_field('genre_personne'); ?>.jpg" alt="<?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?>">
        <?php endif; ?>
        <h2><?php echo get_field('titre_personne').' '.get_field('prenom_personne').' '.get_field('nom_personne'); ?></h2>
        <h3><?php $genre = get_field('genre_personne');
					if(get_the_ID()==399){echo "Consultant en activité physique";} 
					else if (get_the_ID()==388){echo "Chiropraticienne";} 
					else if(!get_field('mini_description_personne')){ 
					    if($genre == "homme") {
					        echo "Assistant";
					    }
					    else {
					        echo "Assistante";
					    }
					     
					}
						
				
					else{
						echo get_field('nom_'.$genre, get_field('domaine_personne')); 
						if( get_field('domaineAdd_personne') ): 
							echo ' et '.strtolower(get_field('nom_'.$genre, get_field('domaineAdd_personne'))); 
						endif; } ?>
				 </h3>
        <?php echo get_field('mini_description_personne'); ?>
        <?php if(get_field('mini_description_personne')){ ?>
				 <span class="link">En savoir plus sur <?php echo get_field('prenom_personne'); ?><span class="icon-arrow-right"></span></span>
				<?php } ?>
        </div>
    </div>
    <?php if(get_field('mini_description_personne')){ ?>
		 </a>
		<?php } else{ ?>
			</div>
		<?php } ?>

</div>