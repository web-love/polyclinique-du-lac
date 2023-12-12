<?php 
    //Si c'est la page d'accueil
    if(is_home() || is_front_page()) : ?>
    
        <header dkit-grid='col-12' style='background-image: url(<?php if( get_field('background') ): the_field('background'); else: echo bloginfo('template_directory').'/img/bg_header.jpg'; endif; ?>);'>
	        
	        <div class='Gradient'></div>
	        <?php get_template_part( 'module-menu' ); ?>
	    
	        <div dkit-grib='col-12' class='Accueil'>
	            <p><?php echo strip_p_single( get_field('phrase_accueil') ); ?>
	            <h1><?php echo strip_p_single( get_field('titre_accueil') ); ?></h1>
	            <a class='bouton-contour' <?php echo (get_field('bouton_link') ?  'target="_blank"' : ''); ?>  href='<?php echo (get_field('bouton_link') ? get_field('bouton_link') : get_the_permalink(136)); ?>'><?php echo get_field('bouton_accueil'); ?>
	            <span class='icon-arrow-right'></span>
	            </a>
	        </div>
	        
        </header>  
        
    <?php elseif( is_singular( array( 'post', 'exercice' ) ) ) : ?>
        <header dkit-grid='col-12' class='page w_filter' style='background-image: url("<?php echo bloginfo('template_directory').'/img/bg_pro.jpg'; ?>"); background-position: center;'>
	        
		    <div class="filter">
	
	        	<div class='Gradient'></div>
				<?php get_template_part( 'module-menu' ); ?>
	        
		    </div>
		    
        </header> 
        
    <?php elseif( is_single() ) : ?>
        <header dkit-grid='col-12' class='page' style='background-image: url("<?php if( get_field('background') ): echo get_field('background')['url']; else: echo bloginfo('template_directory').'/img/bg_pro.jpg'; endif;?>"); background-position: center;'>

        <div class='Gradient'></div>
        <?php get_template_part( 'module-menu' ); ?>
        </header> 
        
    <?php else : ?>
        <header dkit-grid='col-12' style='background: #15160f;'>
	        
        	<div class='Gradient'></div>
			<?php get_template_part( 'module-menu' ); ?>
			
        </header> 
    <?php endif; ?>
    
<?php get_template_part( 'module-sousmenu' ); ?>