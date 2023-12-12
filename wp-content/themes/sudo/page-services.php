<?php get_header();

get_template_part( 'module-header' );

$args = array(
        'post_type' => 'service',
        'meta_key'		=> 'consultants_externes_services',
        'meta_value'	=> 'non',
        'order'         => 'ASC',
        'orderby'       => 'menu_order',
        'posts_per_page'=> -1
             );
$loop = new WP_Query( $args ); ?>

<section dkit-grid="col-12" class="Services_section" dkit-fixHeight="parent">

    <h1 class="titre"><?php echo get_the_title(); ?></h1>
    <p class="legende"><?php echo strip_p( get_field('texte_service') ); ?></p>

    <div dkit-grid="grid-wrapper" class="services-content">
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

    <div dkit-grid="col-4" class="services-items">
        <div class="items-wrapper">
            <a
               dkit-fixHeight="child"
               dkit-fixheightstopat="530"
               dkit-fixheightorient="top"
               href="<?php echo get_permalink(); ?>" class="<?php echo get_field('couleur'); ?>">

                <h2 class="<?php echo get_field('couleur'); ?>"><?php echo get_field('abreviation_services'); ?></h2>
                <h3><?php echo get_the_title(); ?></h3>
                <p><?php echo get_field('mini_description_services'); ?></p>
            </a>
        </div>
    </div>

    <?php endwhile;
    wp_reset_postdata(); ?>
    </div>
</section>

<?php $args = array(
        'post_type'  => 'service',
        'meta_key'	 => 'consultants_externes_services',
        'meta_value' => 'oui',
        'order'         => 'ASC',
        'orderby'       => 'menu_order'
             );
$loop = new WP_Query( $args ); ?>
<?php if($loop->have_posts()) :?>

<div dkit-grid="col-12" class="Services_section Consultant" dkit-fixHeight="parent">

    <h1 class="titre">Consultants externes</h1>
    <div dkit-grid="grid-wrapper" class="services-content">

    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

    <div dkit-grid="col-4" class="services-items">
        <div class="items-wrapper">
            <a
               dkit-fixHeight="child"
               dkit-fixheightstopat="530"
               dkit-fixheightorient="top"
               href="<?php echo get_permalink(); ?>" class="<?php echo get_field('couleur'); ?>">
                <h2 class="<?php echo get_field('couleur'); ?>"><?php echo get_field('abreviation_services'); ?></h2>
                <h3><?php echo get_the_title(); ?></h3>
                <p><?php echo get_field('mini_description_services'); ?></p>
            </a>
        </div>
    </div>

    <?php
    endwhile; ?>
    </div>
</div>
<?php endif;
wp_reset_postdata();
get_footer(); ?>


<script>

    $(document).ready(function(){
       FIXHEIGHT.init();
    });

</script>
