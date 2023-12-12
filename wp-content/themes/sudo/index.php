<?php get_header(); ?>

  <?php get_template_part( 'module-header' ); ?>

  <?php get_template_part( 'module-services' ); ?>

  <?php get_template_part( 'module-blog' ); ?>
    
<?php get_footer(); ?>

<script>
    $(document).ready(function(){
       FIXHEIGHT.init(); 
    });
</script>