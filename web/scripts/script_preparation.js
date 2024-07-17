/* Messages Modal */

$(".edit-btn").click(function() {
var link="load.php?get=ModElem&id="+this.getAttribute("data-id")+'&mod='+this.getAttribute("data-mod")+'&periode='+this.getAttribute("data-periode");
$("#edit .modal-body").load(link);
});


/*fonction pour charger les modules*/
function load_modules(link){
$('#sel_mod').select2({

      placeholder: "Selection d'un module",
	  blurOnChange: true,
    openOnEnter: false,   
        ajax: {
            url: link ,
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                  };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            }
        }
    });
}
/* filieres*/
$('#sel_filiere').select2({

    placeholder: "Selection d'une filière",
    allowClear: true,
    blurOnChange: true,
    openOnEnter: false
});

/*Fonctions de validation*/

function not_empty(el) {
	var val = el.val();
	ret = {
		status: true
	};
	if (!val.length || val==0) {
			ret.status = false;
			ret.msg = "problème dans ce champ!";
	}
	if(!isFinite(val) || isNaN(val)){
			ret.status = false;
			ret.msg = "Valeur non valide!";
	}
	if(val<0 || val % 1 != 0){
			ret.status = false;
			ret.msg = "Choisir un nombre raisonable!";
	}
	return ret;
}

/* wizard *//*
 $(function() {
    var options = {}; //submitUrl: "process_gestion.php"
    var wizard = $("#wizard_ajout").wizard(options);
	$("#open-wizard").click(function() {
		wizard.show();
	});
	
	wizard.cards["card3"].on("selected", function(card) {
	var res="load.php?get=element&mod="+$("#sel_mod").select2("val");
    $("#element").load(res);
	});
	wizard.cards["card2"].on("loaded", function(card) {
	var s=$("#semestre").val();
	var link="load.php?type=module&s="+s;
	load_modules(link);
	});
	wizard.cards["card2"].on("validate", function(card) {
    var val = $("#sel_mod").val();
    if (val == "") {
        card.wizard.errorPopover( card.el.find("span"), "Il faut choisir un module!");
        return false;
    }
    return true;
	});
	wizard.on("submit", function(wizard) {
		$.ajax({
			url: "process_gestion.php",
			type: "POST",
			data: wizard.serialize(),
			success: function() {
				wizard.submitSuccess(); // displays the success card
				wizard.hideButtons(); // hides the next and back buttons
				wizard.updateProgressBar(0); // sets the progress meter to 0
			},
			error: function() {
				wizard.submitError(); // display the error card
				wizard.hideButtons(); // hides the next and back buttons
			}
		});
	/*	setTimeout(function() {
			wizard.trigger("success");
			wizard.hideButtons();
			wizard._submitting = false;
			wizard.showSubmitCard("success");
			wizard._updateProgressBar(0);
		}, 1000);*//*
	});
	
		wizard.on("reset", function(wizard) {
		wizard.setSubtitle("");
		wizard.el.find("#nbr").val("");
		var s=$("#semestre").val();
		var link="load.php?type=module&s="+s;
		load_modules(link);
	});

	wizard.el.find(".wizard-success .fini").click(function() {
		wizard.reset().close();
		$('#sel_mod').val("");
		location.reload();
	});

	wizard.el.find(".wizard-success .autre-instance").click(function() {
		wizard.reset();
		$('#sel_mod').val("");
	});
});
*/
/* Recharger les Module lors du changement du semestre *//*
 $("#semestre").change(function(){
   var s=$(this).val();
   var link="load.php?type=module&s="+s;

   load_modules(link);
$('#sel_mod').val("");
});
*/
/*  switch  */ 

    $('.switch').on('switch-change', function (e, data) {
    var $el = $(data.el)
    , value = data.value;
	var T=this;
	if(data.value)
	{
		this.style.display= "none";
		var id=this.getAttribute("data-id");
		var mod=this.getAttribute("data-mod");
		var periode=this.getAttribute("data-periode");
	//	var html='<button id="save-btn-modD'+id+'" class="btn btn-primary pull-right">Instancier!</button><div style="margin-bottom: 0px;padding: 0px " id="msg'+id+'" class="alert hide alert-error" ></div>';
	//	this.parentElement.innerHTML=html;
		var btn='#save-btn-modD'+id;
		
		$(btn).show();
	//	this.parentElement.style.display= "none";

	//	setTimeout(function() { this.style.display= "none"; }, 1000);
		
		
		var cours="#grp_cours"+id;
		var TD="#grp_td"+id;
		var TP="#grp_tp"+id;
	//	alert(TD);
	
		//////////////////////////	
	function editable_new()
	{
        $(cours).editable({
            type: 'number',
            url: 'process_gestion.php',
            title: 'Entrer nombre des sections de Cours',
                validate: function(value) {
                //	alert(value);
                if($.trim(value) == '') {
                    return 'Il faut remplir ce champs!';
                }
            }
        });

	$(TD).editable({
    type: 'number',
    url: 'process_gestion.php',
    title: 'Entrer nombre des groupes de TD',
	validate: function(value) {
	//	alert(value);
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		}
    });
	
	$(TP).editable({
    type: 'number',
    url: 'process_gestion.php',
    title: 'Entrer nombre des groupes de TP',
	validate: function(value) {
	//	alert(value);
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}
		}
    });
		
}
			///////////////////////////////////////////
	editable_new();
	
	
	e.stopPropagation();
			$(TD).editable('show');
		
		//automatically show next editable
		var edit_class='.myeditable'+id;
		$(edit_class).on('save.newuser', function(){
		var that = this;
		setTimeout(function() {
			$(that).closest('td').next().find(edit_class).editable('show');
		}, 200);
		});	
		//////////
		
		
		////// save button
		
		$(btn).click(function() {
		
		var link='process_gestion.php?new=modD&mod='+mod+'&periode='+periode;
		
		$(edit_class).editable('submit', { 
       url: link, 
       ajaxOptions: {
           dataType: 'json' //assuming json response
       },           
       success: function(data, config) {
	  // alert('1');
           if(data && data.id && data.nb) {  //record created, response like {"id": 2}
               //set pk
               $(cours).editable('option', 'pk', data.id);
               $(cours).editable('option', 'name', 'ModD_cours');
               $(TD).editable('option', 'pk', data.id);
               $(TD).editable('option', 'name', 'ModD_td');
               $(TP).editable('option', 'pk', data.id);
               $(TP).editable('option', 'name', 'ModD_tp');

               //remove unsaved class
               $(this).removeClass('editable-unsaved');
               //show messages
               var msg = data.nb+' elements instancié avec succès!';
			   var elem_inst='elem_inst_'+id;
			   document.getElementById(elem_inst).innerHTML=data.nb;
			   var msg_div='#msg'+id;
               $(msg_div).addClass('alert-success').removeClass('alert-error').html(msg).show();
			//   setTimeout(function() {  window.location.reload();}, 1000);
               $(btn).hide(); 
               $(this).off('save.newuser');                     
           } else if(data && data.errors){ 
               //server-side validation error, response like {"errors": {"username": "username already exist"} }
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
       //    $('msg_div').removeClass('alert-success').addClass('alert-error').html(msg).show();
			 var popover = $(btn).popover({ content: msg,trigger: "manual",html: "TRUE", placement: "top"});
			 popover.popover("show");
			 setTimeout(function() { popover.popover("destroy");}, 2000);
       }
	 });
	});

	}
	else
	{
		var r=confirm("Etes vous sûre?");
		if (r==true)
		{
			x="You pressed OK!";
			
			var xmlhttp;
			function loadXMLDoc(url,cfunc)
			{
				if (window.XMLHttpRequest)
				{// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}
				else
				{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=cfunc;
				xmlhttp.open("GET",url,true);
				xmlhttp.send();
			}
			var id=this.getAttribute("data-id");
			var url="process_gestion.php?delete=modD&id="+id;
			
			loadXMLDoc(url,function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					var msg=xmlhttp.responseText;
					if(msg=="true") 
					{
						//setTimeout(function() { popover.popover("destroy");}, 2000);
						T.parentElement.innerHTML='<div style="margin-bottom: 0px;padding: 0px " class="alert alert-success" >Supprimé avec succès!</div>';
                        $(TD).editable('option', 'disabled', true);
                        $(TP).editable('option', 'disabled', true);
                        $(cours).editable('option', 'disabled', true);
					}
					else T.parentElement.innerHTML='<div style="margin-bottom: 0px;padding: 0px " class="alert alert-important" >'+msg+'</div>';
				}
			});
			
		}
		else
		{
			location.reload();
		}
		console.log(e, $el, value);
	}
	
});
$('.ModD_cours').editable({
    type: 'number',
    highlight: true,
    url: 'process_gestion.php',
    title: 'Entrer le nombre de section de cours',
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
$('.ModD_td').editable({
    type: 'number',
    highlight: true,
    url: 'process_gestion.php',
    title: 'Entrer le nombre de groupe de TD',
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
$('.ModD_tp').editable({
    type: 'number',
    highlight: true,
    url: 'process_gestion.php',
    title: 'Entrer le nombre de groupes de TP',
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

