<?php get_header();

get_template_part( 'module-header' ); ?>

<?php
    @$service = $_GET['service-demande'];
    @$professionnel = $_GET['professionnel'];
?>

<section dkit-grid="col-12" class="Services_section Formulaires">
    <h1 class="titre"><?php echo strip_p( get_field('titre_chronique') ); ?></h1>
</section>

<section dkit-grid="col-12" class="Contact">
    <div dkit-grid="grid-wrapper">
        <div dkit-grid="col-8" class="contact-form">

            <?php echo do_shortcode( '[contact-form-7 id="320" title="Rendez-vous dispos"]' ); ?>

        </div>
    </div>
</section>

<?php get_footer(); ?>

<script>
$(window).on('load', function(){
	// DISABLE DE LA FDS
	$("#dispo-1 option[value='Samedi']").attr('disabled','disabled');
	$("#dispo-1 option[value='Dimanche']").attr('disabled','disabled');
  $("#dispo-2 option[value='Samedi']").attr('disabled','disabled');
	$("#dispo-2 option[value='Dimanche']").attr('disabled','disabled');
	
	//JULIE LEMAIRE
  if($( "#select_equipe" ).val() == "Dre Julie Lemaire"){
    $("#dispo-1 option[value='Lundi']").attr('disabled','disabled');
    $("#dispo-1 option[value='Vendredi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Lundi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Vendredi']").attr('disabled','disabled');
  }
	// M-C DÉRY // EMILY GUENETTE
	else if($( "#select_equipe" ).val() == "Dre Marie-Claude Déry" || $( "#select_equipe" ).val() == "Dre Emily Guenette"){
    $("#dispo-1 option[value='Mardi']").attr('disabled','disabled');
    $("#dispo-1 option[value='Jeudi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Mardi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Jeudi']").attr('disabled','disabled');
  }
	// CAROLINE GAUTHIER
	else if($( "#select_equipe" ).val() == "Caroline Gauthier"){
    $("#dispo-1 option[value='Lundi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Lundi']").attr('disabled','disabled');
	}
	// RAYMOND HÉBERT
	else if($( "#select_equipe" ).val() == "Raymond Hébert"){
    $("#dispo-1 option[value='Vendredi']").attr('disabled','disabled');
    $("#dispo-2 option[value='Vendredi']").attr('disabled','disabled');
	}
	// NATACHA GAGNÉ // NATHALIE LEHOUX
	else if($( "#select_equipe" ).val() == "Natacha Gagné" || $( "#select_equipe" ).val() == "Nathalie Lehoux"){
    $("#dispo-1 option[value='Samedi']").removeAttr('disabled','disabled');
    $("#dispo-1 option[value='Dimanche']").removeAttr('disabled','disabled');
    $("#dispo-2 option[value='Samedi']").removeAttr('disabled','disabled');
    $("#dispo-2 option[value='Dimanche']").removeAttr('disabled','disabled');
	}
	
	// GESTION DES AM-PM 1
	$( "#dispo-1" ).change(function() {
		$("#dispo-1_moment option[value='Avant-midi']").removeAttr('disabled');
		$("#dispo-1_moment option[value='Après-midi']").removeAttr('disabled');
		$("#dispo-1_moment option[value='Soirée']").removeAttr('disabled');
		
		$('#dispo-1_moment option').each( function() {
			$(this).removeAttr("selected");
			$(this).prop('selected', false);
		});
		
		// JULIE LEMAIRE
		if($( "#select_equipe" ).val() == "Dre Julie Lemaire" && $( "#dispo-1" ).val() == "Mercredi"){
			$("#dispo-1_moment option[value='Après-midi']").attr('disabled','disabled');
			$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
		}
		// MARIE-CLAUDE DÉRY
		if($( "#select_equipe" ).val() == "Dre Marie-Claude Déry" && $( "#dispo-1" ).val() == "Vendredi"){
			$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
		}
		// EMILY GUENETTE
		if($( "#select_equipe" ).val() == "Dre Emily Guenette"){
			if($( "#dispo-1" ).val() == "Mercredi"){
				$("#dispo-1_moment option[value='Avant-midi']").attr('disabled','disabled');
			}	
			else if($( "#dispo-1" ).val() == "Vendredi"){
				$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// CAROLINE GAUTHIER
		if($( "#select_equipe" ).val() == "Caroline Gauthier"){
			if($( "#dispo-1" ).val() == "Mardi" || $( "#dispo-1" ).val() == "Jeudi"){
				$("#dispo-1_moment option[value='Avant-midi']").attr('disabled','disabled');
			}	
			if($( "#dispo-1" ).val() == "Mercredi" || $( "#dispo-1" ).val() == "Vendredi"){
				$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// RAYMOND HÉBERT
		if($( "#select_equipe" ).val() == "Raymond Hébert"){
			if($( "#dispo-1" ).val() == "Jeudi"){
				$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// RAYMOND HÉBERT
		if($( "#select_equipe" ).val() == "Raymond Hébert"){
			if($( "#dispo-1" ).val() == "Jeudi"){
				$("#dispo-1_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
	
	});
												 
	$( "#dispo-2" ).change(function() {
		$("#dispo-2_moment option[value='Avant-midi']").removeAttr('disabled');
		$("#dispo-2_moment option[value='Après-midi']").removeAttr('disabled');
		$("#dispo-2_moment option[value='Soirée']").removeAttr('disabled');
		
		$('#dispo-2_moment option').each( function() {
			$(this).removeAttr("selected");
			$(this).prop('selected', false);
		});
		
		// JULIE LEMAIRE
		if($( "#select_equipe" ).val() == "Dre Julie Lemaire" && $( "#dispo-2" ).val() == "Mercredi"){
			$("#dispo-2_moment option[value='Après-midi']").attr('disabled','disabled');
			$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
		}
		// MARIE-CLAUDE DÉRY
		if($( "#select_equipe" ).val() == "Dre Marie-Claude Déry" && $( "#dispo-2" ).val() == "Vendredi"){
			$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
		}
		// EMILY GUENETTE
		if($( "#select_equipe" ).val() == "Dre Emily Guenette"){
			if($( "#dispo-2" ).val() == "Mercredi"){
				$("#dispo-2_moment option[value='Avant-midi']").attr('disabled','disabled');
			}	
			else if($( "#dispo-2" ).val() == "Vendredi"){
				$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// CAROLINE GAUTHIER
		if($( "#select_equipe" ).val() == "Caroline Gauthier"){
			if($( "#dispo-2" ).val() == "Mardi" || $( "#dispo-2" ).val() == "Jeudi"){
				$("#dispo-2_moment option[value='Avant-midi']").attr('disabled','disabled');
			}	
			if($( "#dispo-2" ).val() == "Mercredi" || $( "#dispo-2" ).val() == "Vendredi"){
				$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// RAYMOND HÉBERT
		if($( "#select_equipe" ).val() == "Raymond Hébert"){
			if($( "#dispo-2" ).val() == "Jeudi"){
				$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
		// RAYMOND HÉBERT
		if($( "#select_equipe" ).val() == "Raymond Hébert"){
			if($( "#dispo-2" ).val() == "Jeudi"){
				$("#dispo-2_moment option[value='Soirée']").attr('disabled','disabled');
			}	
		}
	});		
	
	
	
												 
  $( "#select_equipe, #select_services" ).change(function() {
		//RESET
			$("#dispo-1 option[value='Lundi']").removeAttr('disabled');
			$("#dispo-1 option[value='Mardi']").removeAttr('disabled');
			$("#dispo-1 option[value='Mercredi']").removeAttr('disabled');
			$("#dispo-1 option[value='Jeudi']").removeAttr('disabled');
      $("#dispo-1 option[value='Vendredi']").removeAttr('disabled');
			$("#dispo-1 option[value='Samedi']").attr('disabled','disabled');
			$("#dispo-1 option[value='Dimanche']").attr('disabled','disabled');
      $("#dispo-2 option[value='Lundi']").removeAttr('disabled');
			$("#dispo-2 option[value='Mardi']").removeAttr('disabled');
			$("#dispo-2 option[value='Mercredi']").removeAttr('disabled');
			$("#dispo-2 option[value='Jeudi']").removeAttr('disabled');
      $("#dispo-2 option[value='Vendredi']").removeAttr('disabled');
			$("#dispo-2 option[value='Samedi']").attr('disabled','disabled');
			$("#dispo-2 option[value='Dimanche']").attr('disabled','disabled');
		
			$("#dispo-1_moment option[value='Avant-midi']").removeAttr('disabled');
			$("#dispo-1_moment option[value='Après-midi']").removeAttr('disabled');
			$("#dispo-1_moment option[value='Soirée']").removeAttr('disabled');
		
			$("#dispo-2_moment option[value='Avant-midi']").removeAttr('disabled');
			$("#dispo-2_moment option[value='Après-midi']").removeAttr('disabled');
			$("#dispo-2_moment option[value='Soirée']").removeAttr('disabled');
		
			$('#dispo-1_moment option').each( function() {
				$(this).removeAttr("selected");
				$(this).prop('selected', false);
			});
			$('#dispo-2_moment option').each( function() {
				$(this).removeAttr("selected");
				$(this).prop('selected', false);
			});
			$('#dispo-1 option').each( function() {
				$(this).removeAttr("selected");
				$(this).prop('selected', false);
			});
			$('#dispo-2 option').each( function() {
				$(this).removeAttr("selected");
				$(this).prop('selected', false);
			});
		
		
		//JULIE LEMAIRE
    if($( "#select_equipe" ).val() == "Dre Julie Lemaire"){
      $("#dispo-1 option[value='Lundi']").attr('disabled','disabled');
      $("#dispo-1 option[value='Vendredi']").attr('disabled','disabled');
      $("#dispo-2 option[value='Lundi']").attr('disabled','disabled');
      $("#dispo-2 option[value='Vendredi']").attr('disabled','disabled');
    }
		// M-C DÉRY // EMILY GUENETTE
		else if($( "#select_equipe" ).val() == "Dre Marie-Claude Déry" || $( "#select_equipe" ).val() == "Dre Emily Guenette"){
			$("#dispo-1 option[value='Mardi']").attr('disabled','disabled');
			$("#dispo-1 option[value='Jeudi']").attr('disabled','disabled');
			$("#dispo-2 option[value='Mardi']").attr('disabled','disabled');
			$("#dispo-2 option[value='Jeudi']").attr('disabled','disabled');
		}
		// CAROLINE GAUTHIER
		else if($( "#select_equipe" ).val() == "Caroline Gauthier"){
			$("#dispo-1 option[value='Lundi']").attr('disabled','disabled');
			$("#dispo-2 option[value='Lundi']").attr('disabled','disabled');
		}
		// RAYMOND HÉBERT
		else if($( "#select_equipe" ).val() == "Raymond Hébert"){
			$("#dispo-1 option[value='Vendredi']").attr('disabled','disabled');
			$("#dispo-2 option[value='Vendredi']").attr('disabled','disabled');
		}
		else if($( "#select_equipe" ).val() == "Natacha Gagné" || $( "#select_equipe" ).val() == "Nathalie Lehoux"){
			$("#dispo-1 option[value='Samedi']").removeAttr('disabled','disabled');
			$("#dispo-1 option[value='Dimanche']").removeAttr('disabled','disabled');
			$("#dispo-2 option[value='Samedi']").removeAttr('disabled','disabled');
			$("#dispo-2 option[value='Dimanche']").removeAttr('disabled','disabled');
		}
  });
});
</script>
