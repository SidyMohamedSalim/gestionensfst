
/* x-editable*/
	
	//enseignant
    $('.prof_nom').editable({
    type: 'text',
    highlight: true,
    url: 'process_gestion.php',
    title: 'Entrer le nom',
	validate: function(value) {
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		},
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
    });
	$('.prof_prenom').editable({
    type: 'text',
    highlight: true,
    url: 'process_gestion.php',
    title: 'Entrer le prenom',
	validate: function(value) {
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		},
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
     });
	$('.prof_email').editable({
    type: 'text',
    url: 'process_gestion.php',
    title: 'Entrer un email',
	inputclass : 'input-large',
    ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
    });
	$('.prof_dept').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=dept',
    title: 'Departement?',
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
    });
	
	$('.prof_grade').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=grade',
	inputclass : 'input-small',
    title: 'Grade?',
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }
    });
	$('.prof_status').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=status',
	inputclass : 'input-small',
    title: 'Status?',
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
            else location.reload();
        }
        ,
        error: function(response, newValue) {
        //    alert(JSON.stringify(response, null, 4));
            if(response.status === 500) {
                return 'Service unavailable. Please try later.';
            }
        }
    });
	
	//////////////////////////	
	function editable_new()
	{	
	$('#prof_nom').editable({
    type: 'text',
    url: 'process_gestion.php',
    title: 'Entrer le nom',
	validate: function(value) {
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		}
    });
	$('#prof_prenom').editable({
    type: 'text',
    url: 'process_gestion.php',
    title: 'Entrer le prenom',
	validate: function(value) {
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		}
    });
	$('#prof_email').editable({
    type: 'email',
    url: 'process_gestion.php',
    title: 'Entrer un email',
	inputclass : 'input-large'
    });
	$('#prof_dept').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=dept',
    title: 'Departement?'
    });
	
	$('#prof_grade').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=grade',
	inputclass : 'input-small',
    title: 'Grade?'
    });	
}
			///////////////////////////////////////////
	// nouveau enseignant
	$("#add_prof").click(function(e) {
	
	$('#add_prof').hide();
	
	var html='<tr class="even"><td></td><td><span id="prof_nom" class="myeditable" data-name="prof_nom"></span></td><td><span id="prof_prenom" class="myeditable" data-name="prof_prenom"></span></td><td><span id="prof_dept" class="myeditable" data-name="prof_dept"></span></td><td><span id="prof_grade" class="myeditable" data-name="prof_grade"></span></td><td><span id="prof_email" class="myeditable" data-type="email" data-name="prof_email"></span></td><td><button id="save-btn-prof" class="btn btn-primary pull-right">Enregistrer!</button><div id="msg" class="alert hide alert-error" ></div></td>';
	$('#liste_prof').prepend(html);
	
		
	editable_new();
	
	
	e.stopPropagation();
			$('#prof_nom').editable('show');
		
		//automatically show next editable
		$('.myeditable').on('save.newuser', function(){
		var that = this;
		setTimeout(function() {
			$(that).closest('td').next().find('.myeditable').editable('show');
		}, 200);
		});	
		//////////
		
		
		////// save button
		$('#save-btn-prof').click(function() {
		
		var link='process_gestion.php?new=prof';
		
		$('.myeditable').editable('submit', { 
       url: link, 
       ajaxOptions: {
           dataType: 'json' //assuming json response
       },           
       success: function(data, config) {
           if(data && data.id) {  //record created, response like {"id": 2}
               //set pk
               $(this).editable('option', 'pk', data.id);
							
               //remove unsaved class
               $(this).removeClass('editable-unsaved');
               //show messages
               var msg = 'Nouveau element créer! Rechargement de la page...';
               $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
			   setTimeout(function() {  window.location.reload();}, 1000);
               $('#save-btn-prof').hide(); 
               $(this).off('save.newuser');                     
           } else if(data && data.errors){ 
               //server-side validation error, response like {"errors": {"element": "problème lors de la creation des elements.."} }
               config.error.call(this, data.errors);
           }               
       },
       error: function(errors) {
           var msg = '';
           if(errors && errors.responseText) { //ajax error, errors = xhr object
               msg = errors.responseText;
           } else { //validation error (client-side or server-side)
               $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
           } 

			 var popover = $('#save-btn-prof').popover({ content: msg,trigger: "manual",html: "TRUE", placement: "top"});
			 popover.popover("show");
			 setTimeout(function() { popover.popover("hide");}, 2000);
       }
	 });
	});
	
	
});


//nouveau vacataire
	$("#add_vacataire").click(function(e) {
	
	$('#add_vacataire').hide();

	var html='<tr class="even"><td></td><td><span id="prof_nom" class="myeditable" data-name="prof_nom"></span></td><td><span id="prof_prenom" class="myeditable" data-name="prof_prenom"></span></td><td><span id="prof_grade" class="myeditable" data-name="prof_grade"></span></td><td><span id="prof_email" class="myeditable" data-type="email" data-name="prof_email"></span></td><td><button id="save-btn-vacataire" class="btn btn-primary pull-right">Enregistrer!</button><div id="msg" class="alert hide alert-error" ></div></td>';
	$('#liste_vacataire').prepend(html);
	
		
	editable_new();
	
	
	e.stopPropagation();
			$('#prof_nom').editable('show');
		
		//automatically show next editable
		$('.myeditable').on('save.newuser', function(){
		var that = this;
		setTimeout(function() {
			$(that).closest('td').next().find('.myeditable').editable('show');
		}, 200);
		});	
		//////////
		
		
		////// save button
		$('#save-btn-vacataire').click(function() {
		
		var link='process_gestion.php?new=prof&v=1';
		
		$('.myeditable').editable('submit', { 
       url: link, 
       ajaxOptions: {
           dataType: 'json' //assuming json response
       },           
       success: function(data, config) {
           if(data && data.id) {  //record created, response like {"id": 2}
               //set pk
               $(this).editable('option', 'pk', data.id);
							
               //remove unsaved class
               $(this).removeClass('editable-unsaved');
               //show messages
               var msg = 'Nouveau element créer! Rechargement de la page...';
               $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
			   setTimeout(function() {  window.location.reload();}, 1000);
               $('#save-btn-vacataire').hide(); 
               $(this).off('save.newuser');                     
           } else if(data && data.errors){
               config.error.call(this, data.errors);
           }               
       },
       error: function(errors) {
           var msg = '';
           if(errors && errors.responseText) { //ajax error, errors = xhr object
               msg = errors.responseText;
           } else { //validation error (client-side or server-side)
               $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
           } 

			 var popover = $('#save-btn-vacataire').popover({ content: msg,trigger: "manual",html: "TRUE", placement: "top"});
			 popover.popover("show");
			 setTimeout(function() { popover.popover("hide");}, 2000);
       }
	 });
	});
	
	
});

	
/* tooltip */
$('.tip_top').tooltip();
