//x-editable
	$('.module_designation').editable({
     type: 'text',
    url: 'process_gestion.php',
    title: 'Entrer la designation',
	inputclass : 'input-xlarge',
	validate: function(value) {
		if($.trim(value) == '') {
		return 'Il faut remplir ce champs!';
			}else return false;
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
	$('.module_code').editable({
     type: 'text',
    url: 'process_gestion.php',
    title: 'Entrer le code',
	inputclass : 'input-medium',
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
	$('.module_filiere').editable({
    type: 'select',
    url: 'process_gestion.php',
	source: 'load.php?type=filiere',
	inputclass : 'input-xlarge',
    title: 'Filiere?',
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
	$('.module_sem').editable({
     type: 'number',
    url: 'process_gestion.php',
    title: 'Le semestre d\'étude?',
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
	$('.module_status').editable({
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
            else  window.location.assign("module.php");//location.reload();
        }
        ,
        error: function(response, newValue) {
            //    alert(JSON.stringify(response, null, 4));
            if(response.status === 500) {
                return 'Service unavailable. Please try later.';
            } else {
                return response.mssg;
            }
        }
    });
	
	function editable_elem(){
		
		
		$('.elem_code').editable({
		type: 'text',
		url: 'process_gestion.php',
		title: 'Entrer le code',
		inputclass : 'input-medium',
		placement: 'right',
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
		
		$('.elem_designation').editable({
		type: 'text',
		url: 'process_gestion.php',
		title: 'Entrer la designation',
		inputclass : 'input-large',
		placement: 'right',
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
		
		$('.elem_hrs_cours').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de cours?',
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
            },
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
		});
		$('.elem_hrs_td').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de TD?',
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
            },
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
		});
		$('.elem_hrs_tp').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de TP?',
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
            },
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
		});
		
		$('.elem_dept').editable({
		type: 'select',
		url: 'process_gestion.php',
		source: 'load.php?type=dept',
		title: 'Departement?',
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(response, newValue) {
                if(!response.succes) return response.mssg;
            },
            error: function(response, newValue) {
                if(response.status === 500)
                    return 'Service unavailable. Please try later.';
            }
		});
		$('.elem_status').editable({
			type: 'select',
			url: 'process_gestion.php',
			source: 'load.php?type=status',
			inputclass : 'input-small',
			placement: 'left',
			title: 'Status?',
            ajaxOptions: {
                dataType: 'json' //assuming json response
            },
            success: function(response, newValue) {
                if(!response.succes) return response.mssg;
                else {
                    var to="module.php?mod="+this.getAttribute("data-mod");
                    	   window.location.assign(to);
                }
            }
            ,
            error: function(response, newValue) {
                //    alert(JSON.stringify(response, null, 4));
                if(response.status === 500) {
                    return 'Service unavailable. Please try later.';
                }
            }
		});
		
	}
	$(window).load(function(){
		var div = document.getElementById("edit_elem");
		var elem = div.getAttribute("data-mod");

		if(elem != "-1")
		{
			var link="load.php?get=Mod_Elem&id="+elem;
			$("#edit_elem .modal-body").load(link, function() {
			editable_elem();
			});
			$('#edit_elem').modal('show');
		}

    });
	$(".edit_elem").click(function() {
		var link="load.php?get=Mod_Elem&id="+this.getAttribute("data-id");
	
		$("#edit_elem .modal-body").load(link, function() {
		editable_elem();
		
		var z= parseInt(document.getElementById('cur_elem_nbr').value);
		if(z>=4) 
		document.getElementById("new_elem").disabled =true;
		
		$('#new_elem').click(function(e) {
		var x= parseInt(document.getElementById('cur_elem_nbr').value);
		y=x+1;
	//	if(y>=4) 
	//	document.getElementById("new_elem").disabled =true;
		document.getElementById("new_elem").style.display = "none";
		document.getElementById("save-btn").style.display = "block";
		
		var html='<tr>' +
            '<td></td>' +
            '<!--<td><span class="myeditable" id="new_elem_code" data-type="text" data-name="elem_code"></span></td> -->' +
            '<td><span class="myeditable" id="new_elem_designation" data-name="elem_designation"></span></td><td><span class="myeditable" id="new_elem_hrs_cours" data-name="elem_hrs_cours"></span></td><td><span class="myeditable" id="new_elem_hrs_td" data-name="elem_hrs_td"></span></td><td><span class="myeditable" id="new_elem_hrs_tp" data-name="elem_hrs_tp"></span></td><td><span class="myeditable" id="new_elem_dept" data-name="elem_dept"></span></td><td class="center "><span class="label label-success"  data-name="elem_status">actif</span></td></tr>';
			$('#cur_elements_table').append(html);
			document.getElementById('cur_elem_nbr').value=y;
		

		//////////////////////////	
			//////////////////////////	
	function editable_new_1()
	{	
		$('#new_elem_code').editable({
		type: 'text',
		url: 'process_gestion.php',
		title: 'Entrer le code',
		inputclass : 'input-medium',
		placement: 'right',
		validate: function(value) {
			if($.trim(value) == '') {
			return 'Il faut remplir ce champs!';
				}
			}
		});
		
		$('#new_elem_designation').editable({
		type: 'text',
		url: 'process_gestion.php',
		title: 'Entrer la designation',
		inputclass : 'input-large',
		placement: 'right',
		validate: function(value) {
			if($.trim(value) == '') {
			return 'Il faut remplir ce champs!';
				}
			}
		});
		
		$('#new_elem_hrs_cours').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de cours?',
		validate: function(value) {
			if($.trim(value) == '') {
			return 'Il faut remplir ce champs!';
				}
			}
		});
		$('#new_elem_hrs_td').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de TD?',
		validate: function(value) {
			if($.trim(value) == '') {
			return 'Il faut remplir ce champs!';
				}
			}
		});
		$('#new_elem_hrs_tp').editable({
		type: 'number',
		url: 'process_gestion.php',
		title: 'heures de TP?',
		validate: function(value) {
			if($.trim(value) == '') {
			return 'Il faut remplir ce champs!';
				}
			}
		});
		
		$('#new_elem_dept').editable({
		type: 'select',
		url: 'process_gestion.php',
		source: 'load.php?type=dept',
		title: 'Departement?'
		});	
}
			///////////////////////////////////////////
			editable_new_1();
			e.stopPropagation();
		//	$('#new_elem_code').editable('show');
			$('#new_elem_designation').editable('show');

		//automatically show next editable
		$('.myeditable').on('save.newuser', function(){
		var that = this;
		setTimeout(function() {
			$(that).closest('td').next().find('.myeditable').editable('show');
		}, 200);
		});	
		//////////
		
		
		////// save button
		$('#save-btn').click(function() {
		var module=this.getAttribute("data-mod");
		var link='process_gestion.php?new=elem_mod&mod='+module;
		
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
			   setTimeout(function() {  var to="module.php?mod="+module;
				   window.location.assign(to);}, 1000);
               $('#save-btn').hide(); 
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
           $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
       }
   });
   
   
});
/////////////////
			
			
			
			
			});
		
		});
	
	});
	$('#sel_filiere').select2({

      placeholder: "Selection d'une filière",
	  allowClear: true,
	  blurOnChange: true,
	  openOnEnter: false
    });

	$('#add_elem').click(function(e) {
        e.preventDefault();
	  var x=  parseInt(document.getElementById("i").value);
	  
	  if(x < 4)
	  {
		var y=x+1;
		document.getElementById("i").value=y;
		if(y==2) $('#drop_elem').toggleClass( "disabled" ); //prop('disabled',true);//document.getElementById("drop_elem").disabled =false;
		if(y==4) $('#add_elem').toggleClass( "disabled" );//prop('disabled',true); //document.getElementById("add_elem").disabled =true;
		var row='<tr>';
		row +='<td><input type="text" class="input-medium" name="elem'+y+'_designation" data-validate="not_empty_str"  /></td>';
		row += '<td><input type="number" style="width: 25px;" name="elem'+y+'_cours" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>';
		row += '<td><input type="number" style="width: 25px;" name="elem'+y+'_td" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>';
		row += '<td><input type="number" style="width: 25px;" name="elem'+y+'_tp" data-validate="not_empty_nbr" onkeypress="return isNumberKey(event)"  /></td>';
		row += '<td><select class="input-medium" name="elem'+y+'_dept" >';
		row += document.getElementById("elem1_dept").innerHTML;
		row += '</td>';
		$('#elems').append(row);
	  }
	
   });   
   $('#drop_elem').click(function(e) {
       e.preventDefault();
       var x=  parseInt(document.getElementById("i").value);
	   if(x >1)
	  {
		var y=x-1;
		document.getElementById("i").value=y;
		if(y==3) $('#add_elem').toggleClass( "disabled" );//prop('disabled',false); //document.getElementById("add_elem").disabled =false;
		if(y==1) $('#drop_elem').toggleClass( "disabled" );//prop('disabled',true); //document.getElementById("drop_elem").disabled =true;
		$('#elems tr:last').remove();
	  }
	 
   });   
// wizard editables
$('#test').editable('option', 'validate', function(v) {
if(!v) return 'Required field!';
});

/*Fonctions de validation*/

function not_empty_nbr(el) {
	var val = el.val();
	ret = {
		status: true
	};
	if (!val.length || val<0) {
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
function not_empty_str(el) {
	var val = el.val();
	ret = {
		status: true
	};
	if (!val || 0 === val.length) {
			ret.status = false;
			ret.msg = "remplir le champs!";
	}
	if(/^\s*$/.test(val)){
			ret.status = false;
			ret.msg = "Valeur non valide!";
	}
	
	return ret;
}

	/* wizard */
$(document).ready(function() {

    var options = {submitUrl: "process_gestion.php"}; //submitUrl: "process_gestion.php"
    var wizard = $("#wizard_mod_ajout").wizard(options);
	$("#open-wizard").click(function() {
		wizard.show();
	});

	wizard.on("submit", function(wizard) {
		$.ajax({
			url: "process_gestion.php",
			type: "POST",
			data: wizard.serialize(),
            dataType: 'json',
			success: function(response) {
                if(response.success==true) {
                    wizard.submitSuccess(); // displays the success card
                    wizard.hideButtons(); // hides the next and back buttons
                    wizard.updateProgressBar(0); // sets the progress meter to 0
                }
                else {
                    wizard.submitError(); // display the error card
                    wizard.hideButtons(); // hides the next and back buttons
                }

			},
			error: function() {

				wizard.submitError(); // display the error card
				wizard.hideButtons(); // hides the next and back buttons
			}
		});
	
	});
	
		wizard.on("reset", function(wizard) {
		wizard.setSubtitle("");
	});

	wizard.el.find(".wizard-success .fini").click(function() {
		wizard.reset().close();
		location.reload();
	});

	wizard.el.find(".wizard-success .ajouter_nouv").click(function() {
		wizard.reset();
	});

});
