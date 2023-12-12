<?php get_header();

get_template_part( 'module-header' );

$args = array(
        'post_type' => 'equipe',
        'meta_key'		=> 'consultant_externe',
        'meta_value'	=> 'non',
        'order'     => 'ASC',
        'orderby'    => 'menu_order',
        'posts_per_page' => -1
             );
$loop = new WP_Query( $args ); ?>

<section dkit-grid="col-12" class="Services_section equipe" dkit-fixHeight="parent">

    <h1 class="titre"><?php echo get_the_title(); ?></h1>
    <p class="legende"><?php echo strip_p( get_field('texte_equipe') ); ?></p>

    <div dkit-grid="grid-wrapper" class="services-content">
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

    <?php get_template_part('item-equipe') ?>

    <?php endwhile; ?>
    </div>
</section>

<?php $args = array(
        'post_type' => 'equipe',
        'meta_key'		=> 'consultant_externe',
        'meta_value'	=> 'oui',
        'orderby'    => 'name',
        'order'     => 'ASC',
        'posts_per_page' => -1
             );
$loop = new WP_Query( $args ); ?>

<?php if($loop->have_posts()) :?>

<section dkit-grid="col-12" class="Services_section Consultant" dkit-fixHeight="parent">

    <h1 class="titre">Consultants externes</h1>
    <div dkit-grid="grid-wrapper" class="services-content">

    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

    <?php get_template_part('item-equipe') ?>

    <?php
    endwhile; ?>
    </div>
</section>
<?php endif;
wp_reset_postdata(); ?>
<?php get_footer(); ?>

<script>

    $(document).ready(function(){
       FIXHEIGHT.init();
    });

</script>
