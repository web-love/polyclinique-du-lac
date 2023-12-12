<?php get_header();

get_template_part( 'module-header' ); ?>

<section dkit-grid="col-12" class="Services_section Formulaires">
    <h1 class="titre"><?php echo strip_p( get_the_title() ); ?></h1>
</section>

<section dkit-grid="col-12" class="Contact">
    <div dkit-grid="grid-wrapper">
        <div dkit-grid="col-4" class="contact-items">
            <h1><?php echo strip_p( get_field('titre') ); ?></h1>
            <p class="marge"><?php echo strip_p( get_field('adresse') ); ?></p>

            <p class="marge"></p>
            <?php if( have_rows('telephone') ): ?>
                <?php $rows = get_field('telephone'); ?>
							<?php foreach( $rows as $row ): ?>
								

								<p class="tel"><?php echo $row['type']; ?> : <a href="<?php 
								 if( $row['type'] != 'Télécopieur' ): 
									 echo 'tel'; 
									 else: echo 'fax'; 
									endif; ?>:+1<?php echo linkify_tel( $row['numero'] ) ?>"><?php echo $row['numero'] ?></a></p>

							<?php endforeach; ?>

            <?php endif; ?>


            <a target="_blank" href="<?php echo get_field('bouton_rendez-vous_url'); ?>" class="bouton-contour"><?php echo strip_p( get_field( 'bouton_rendez-vous' ) ); ?><span class="icon-arrow-right next"></span></a>

           
        </div>
        
         <div dkit-grid="col-4" class="contact-items">

             <h1><?php echo strip_p( get_field( 'titre_horaire' ) ); ?></h1>
            <p class="marge"></p>

            <?php if( have_rows('horaire_footer') ): ?>
                <?php while ( have_rows('horaire_footer') ) : the_row(); ?>

                <p class="horaire"><?php echo '<strong>'.get_sub_field('journee_horaire').':</strong> '.get_sub_field('heures_horaire'); ?></p>

                <?php endwhile; ?>

            <?php endif; ?>

            <p class="note marge"><?php echo strip_p( get_field( 'note' ) ); ?></p>

        </div>

        <div dkit-grid="col-4" class="contact-items">
            <h1>Écrivez-nous</h1>
            <p class="marge"></p>
            <p><a href="mailto:info@polycliniquedulac.com">info@polycliniquedulac.com</a></p>
            <p><em>Pour une prise de rendez-vous, veuillez utiliser le bouton « Prendre rendez-vous en ligne » qui vous redirigera directement sur notre plate-forme de rendez-vous web</em></p>
            
            <?php //echo do_shortcode( '[contact-form-7 id="309" title="Nous joindre"]' ); ?>
            

        </div>
    </div>
</section>

<div dkit-grid="col-12" id="Map-container"></div>

<?php get_footer(); ?>

<script src="<?php bloginfo('template_directory'); ?>/scripts/map.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&key=AIzaSyAiEmHReC6V2j1s5bi5Unwye8KT1XP2RDk" async defer></script>
