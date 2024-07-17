/* Messages Modal */

$(".fs").click(function(e) {

    $(".modal:not(.wizard-modal)").toggleClass("full-screen");
    $(".modal:not(.wizard-modal)").toggleClass("medium-modal");
    ;
});

$(".affect-btn").click(function() {
var link="load.php?get=Affect&id="+this.getAttribute("data-id");
$("#edit .modal-body").load(link);
});
$(".edit_aff").click(function() {
var t=this.getAttribute("data-type");
var idE=this.getAttribute("data-elemd");
var link="load.php?get=Edit_Affect&idE="+idE+"&t="+t;//+"&p="+this.getAttribute("data-periode")

//alert(link);

$("#edit_aff .modal-body").load(link, function() {
	
	var grp_load='load.php?type=free_grp_aff&ED='+idE+'&t='+t;
	var new_grp_link='process_gestion.php?t='+t;
		$('.aff_grp').editable({
		type: 'select',
		url: new_grp_link,
		placement: 'right',
		source: grp_load,
		title: 'Nbr de groupes?',
		success: function(response) {
    if(response) return response;
	else location.reload();
    }
		});
	if(document.getElementById('free_grp').value==0) document.getElementById('new_elem').disabled =true;

$('#new_elem').click(function(e) {
		var x= parseInt(document.getElementById('free_grp').value);
		if (!x) return 0;
		
		y=x-1;
	
	
		document.getElementById("new_elem").style.display = "none";
		document.getElementById("save-btn").style.display = "block";
		
		var html='<tr><td><span class="myeditable" id="new_aff_prof_'+t+'" data-type="select2" data-name="new_aff_prof"></span></td><td><span class="myeditable" id="new_aff_grp" data-name="new_aff_grp"></span></td></tr>';
		$('#aff_table').append(html);
		document.getElementById('free_grp').value=y;
		
		//x editable
		
	/*	 $('.myeditable').editable({
				url: 'process_gestion.php', 
				
				}); */
		//////////////////////////	
			//////////////////////////	
			var prof_load;
			
		//	if(t=="cours") prof_load="load.php?type=enseignant_edit&t="+t+"&status=1&v=0";
			prof_load="load.php?type=enseignant_edit&v=1&t="+t;
			
			var grp_load='load.php?type=free_grp&ED='+idE+'&t='+t;
				
		$('#new_aff_grp').editable({
		type: 'select',
		url: 'process_gestion.php',
		placement: 'right',
		source: grp_load,
        sourceCache: false,
		title: 'Nbr de groupes?'
		});
		var prof_id="#new_aff_prof_"+t;
//    alert(prof_id);
/*		$(prof_id).editable({
		type: 'select',
        sourceCache: false,
        tpl: '<select></select><input type="checkbox" class="prof_check"/>',
		url: 'process_gestion.php',
		placement: 'right',
		source: prof_load,
		title: 'Enseignant?'
		});
*/
    $(prof_id).editable({
        type: 'select2',
    //    tpl: '<input type="hidden">',
        url: 'process_gestion.php',
        placement: 'right',


    //    source: prof_load ,
        title: 'Enseignant?',
        emptytext: 'None',
        select2: {

            placeholder: "Selectionnez un professeur..",
            blurOnChange: true,
            width: '230px',
            openOnEnter: false,
            id: function (e) {
                return e.id;
            },
            ajax: {
                url: prof_load,
                dataType: 'json',
                data: function (term, page) {
                    return { q: term };
                },
                results: function (data, page) {
                    return { results: data };
                }
            },
            formatResult: function (res) {
                return res.text;
            },
            formatSelection: function (res) {
                return res.text;
            },
            initSelection: function (element, callback) {
                return $.get(prof_load, { query: element.val() }, function (data) {
                    callback(data);
                }, 'json'); //added dataType
            }
        }

    });
    ///////////////////////////////////////////
			
			e.stopPropagation();
		//	$('#new_aff_prof').editable('show');
			$(prof_id).editable('show');


		//automatically show next editable
		$('.myeditable').on('save.newuser', function(){
		var that = this;
		setTimeout(function() {
			$(that).closest('td').next().find('.myeditable').editable('show');
		}, 200);
		});	
		//////////
		e.stopPropagation();
		
		////// save button
		$('#save-btn').click(function() {
	//	var module=this.getAttribute("data-mod");
		var link='process_gestion.php?new=affect_elem_mod&idE='+idE+'&t='+t;
	
   $('.myeditable').editable('submit', { 
       url: link, 
       ajaxOptions: {
           dataType: 'json' //assuming json response
       },           
       success: function(data, config) {
           if(data && data.id) {  //record created, response like {"id": 2}
               //set pk
               $(this).editable('option', 'pk', data.id);
			//   document.getElementById("new_elem").style.display = "block";
               //remove unsaved class
               $(this).removeClass('editable-unsaved');
               //show messages
               var msg = 'Affectation reussite! Rechargement de la page...';
               $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
			   setTimeout(function() { // var to="module.php?mod="+module;				   window.location.assign(to);
						location.reload(); }, 1000);
               $('#save-btn').hide(); 
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
           $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
       }
   });
   
   
});
/////////////////
			
			
			
		});

    //new share

    $(".ajouter_partage").on("click", function(e) {
        // e.preventDefault();
        var wait = $(this).data("wait");
        if(wait == 1) return 0;
        $(this).data("wait", "1");
        var btn = $(this);
        var aff_id= parseInt(btn.data("affect_id"));

        btn.button("loading");
        btn.hide();
        $("#save-btn1").show();

//        alert(" ");

        var html='<br><span class="myeditable" id="new_partage_prof_'+aff_id+'" data-type="select2" data-name="new_partage_prof"></span>';
        btn.parent().append(html);

        var prof_load;

        //	if(t=="cours") prof_load="load.php?type=enseignant_edit&t="+t+"&status=1&v=0";
        prof_load="load.php?type=enseignant_edit&v=1&t="+t;


        var prof_id="#new_partage_prof_"+aff_id;

        $(prof_id).editable({
            type: 'select2',
            //    tpl: '<input type="hidden">',
            url: 'process_gestion.php',
            placement: 'right',


            //    source: prof_load ,
            title: 'Enseignant?',
            emptytext: 'None',
            select2: {

                placeholder: "Selectionnez un professeur..",
                blurOnChange: true,
                width: '230px',
                openOnEnter: true,
                id: function (e) {
                    return e.id;
                },
                ajax: {
                    url: prof_load,
                    dataType: 'json',
                    data: function (term, page) {
                        return { q: term };
                    },
                    results: function (data, page) {
                        return { results: data };
                    }
                },
                formatResult: function (res) {
                    return res.text;
                },
                formatSelection: function (res) {
                    return res.text;
                },
                initSelection: function (element, callback) {
                    return $.get(prof_load, { query: element.val() }, function (data) {
                        callback(data);
                    }, 'json'); //added dataType
                }
            }

        });
        ///////////////////////////////////////////

        e.stopPropagation();
        //	$('#new_aff_prof').editable('show');
        $(prof_id).editable('show');


        //automatically show next editable
        $('.myeditable').on('save.newuser', function(){
            var that = this;
            /*
            setTimeout(function() {
                $(that).closest('td').next().find('.myeditable').editable('show');
            }, 200);
            */
        });
        //////////
        e.stopPropagation();

        ////// save button
        $('#save-btn1').click(function() {
            //	var module=this.getAttribute("data-mod");
            var link='process_gestion.php?new=partage&aff_id='+aff_id;

            $('.myeditable').editable('submit', {
                url: link,
                ajaxOptions: {
                    dataType: 'json' //assuming json response
                },
                success: function(data, config) {
                    if(data && data.id) {  //record created, response like {"id": 2}
                        //set pk
                        $(this).editable('option', 'pk', data.id);
                        //   document.getElementById("new_elem").style.display = "block";
                        //remove unsaved class
                        $(this).removeClass('editable-unsaved');
                        //show messages
                        var msg = 'Affectation reussite! Rechargement de la page...';
                        $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
                        setTimeout(function() { // var to="module.php?mod="+module;				   window.location.assign(to);
                            location.reload(); }, 1000);
                        $('#save-btn1').hide();
                        btn.hide();
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
                    $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
                }
            });


        });

    });



    //end new_share


    //supprimer_partage
    $(".supprimer_partage").on("click", function(e) {
         e.preventDefault();
        var wait = $(this).data("wait");
        if (wait == 1) return 0;
        $(this).data("wait", "1");
        var btn = $(this);
        var aff_id = parseInt(btn.data("aff_id"));

        var ens_id = parseInt(btn.data("ens_id"));

        btn.button("loading");
        $.ajax({
            // type: "POST",
            type: "GET",
            contentType: "application/json; charset=utf-8",
            url: "process_gestion.php",
            data: "delete=affect_partage&ens_id="+ens_id+"&aff_id="+aff_id,
            error: function () {
                btn.button("reset");
                alert("Problème lors de l\'envoi de la requette!");
                btn.data("wait", "0");
            },
            dataType: "json",
            success: function (msg) {

                btn.button("reset");
                btn.data("wait", "0");

                if(msg.msg)
                    alert(msg.msg);
                else
                    btn.parent().hide();



            }
        });

    });
    //end supprimer partage



});

});


/*fonction load edit-aff*/
/*
function load_edit(elem)
{
//	alert(elem.getAttribute("data-elem-id")+"  "+elem.getAttribute("data-nature")+"  "+elem.getAttribute("data-prof"));
	var link="load.php?get=Affect3&id="+elem.getAttribute("data-elem-id")+"&nature="+elem.getAttribute("data-nature")+"&prof="+elem.getAttribute("data-prof");

	$("#edit-aff .modal-body").load(link);
}
*/

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
$('#vacataire').change(function() {
    load_enseignant();
});


/*fonction pour charger la liste des enseignants*/
function load_enseignant(){/*
function formatCss(object) {
//container.element.className += "label";
//object.className=object.className + "label";
 //return "<span class='label'></span>" + object.text;
 var lab= "label ";
 //if(object.callback({return result.results['charge']})=="0") lab+="label-success";
 //else
 return "label";
}*/
function format(object,container) {
$(object.element).text('test');
return object.text;
}
var link="load.php?type=enseignant&status=1&v=";
if(document.getElementById('vacataire').checked)
	link+=1;
	else
	link+=0;
$('#sel_enseignant').select2({

      placeholder: "Selectionnez un professeur..",
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
	//	formatResult: format,
	//	formatResultCssClass: formatCss
		//formatSelection: format
    });
}




$('#sel_filiere').select2({

      placeholder: "Selection d'une filière",
	  allowClear: true,
	  blurOnChange: true,
	  openOnEnter: false
    });
	
	/* card 2 content*/
function load_affect(link)
{
    $("#element").load(link);
/*	
	var hrs=document.getElementById("charge").getAttribute("data-hrs-aff");
	var chrg=document.getElementById("charge").getAttribute("data-charge");
	
	html = '<div class="progress progress-'+label_2(hrs,chrg)+'" style="margin: 5px;"><div class="bar" style="width: '+(+hrs/+chrg)*100+'%;"></div>    </div></div>';
    
	document.getElementById('progress_bar').innerHTML = html; */
}
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
/* fonction label*/
function label(x,y) {
if(x==0) return " ";
else
if(+x>(+y- +y*0.05) && +x<(+y+ +y*0.05)) return "success";
else if(+x>0 && +x<=(+y*0.8)) return "info";
else if(+x>(+y*0.8) && +x<(+y- +y*0.05)) return "warning";
else return "important";

}
function label_2(x,y) {

if(x>=(+y- +y*0.05) && +x<=(+y+ +y*0.05)) return "success";
else if(+x>0 && +x<=(+y*0.8)) return "info";
else if(+x>(+y*0.8) && +x<(+y- +y*0.05) ) return "warning";
else return "danger";

}
/* wizard */
 $(function() {
    var options = {width: "750px", increaseHeight: 100}; //submitUrl: "process_gestion.php"
    var wizard = $("#wizard_affect").wizard(options);
	$("#open-wizard").click(function() {
		wizard.show();
	});
	
	wizard.cards["card3"].on("selected", function(card) {
	//var res="load.php?get=Affect2&id="+$("#sel_mod").select2("val")+"&prof="+$("#sel_enseignant").select2("val");
   // $("#element3").load(res);
   var html='<div><h3>Les affectations:</h3><table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">';
   var hrs_cr=0,hrs_td=0,hrs_tp=0;
   for (var i=1;i<=document.getElementById("i").value;i++)
	{
		
	//	var id="Checkbox"+i;

		
	//	if (document.getElementById(id).checked)
	//	{
			html+='<tr><th>'+document.getElementById('elem-link'+i).innerHTML+'</th>';
			var cr=document.getElementById("elem"+i+"_cours").value;
			var td=document.getElementById("elem"+i+"_TD").value;
			var tp=document.getElementById("elem"+i+"_TP").value;
			
			html+="<td>";
			var x=0;
			if(cr.length && cr!=0) {
			var id_1="th_cours_"+i;
				x=document.getElementById(id_1).getAttribute("data-grp");
				
				hrs_cr+=cr*(+document.getElementById("th_cours_"+i).getAttribute("data-hrs"));
				
				html+="<span>Cours: "+cr+"/"+x+"</span><br/>";}
			if(td.length && td!=0) {
				x=document.getElementById("th_TD_"+i).getAttribute("data-grp");
				hrs_td+=td*document.getElementById("th_TD_"+i).getAttribute("data-hrs");
				html+="<span>TD: "+td+"/"+x+"</span><br/>";}
			if(tp.length && tp!=0) {
				x=document.getElementById("th_TP_"+i).getAttribute("data-grp");
				hrs_tp+=tp*document.getElementById("th_TP_"+i).getAttribute("data-hrs");
				html+="<span>TP: "+tp+"/"+x+"</span><br/>";}
			
			html+="</td></tr>";
	//	}
	}
	html+='</table>'
	html+='<h4>Apès l\'affectation:</h4>';
	html+='<div class="alert alert-info">'+document.getElementById('proffeseur').innerHTML+'<br/>'+document.getElementById('charge').innerHTML;
	var hrs=document.getElementById("charge").getAttribute("data-hrs-aff");
	var chrg=document.getElementById("charge").getAttribute("data-charge");
	//alert('hrs='+hrs+'  hrs_cr='+hrs_cr+' hrs_td'+hrs_td+' hrs_tp'+hrs_tp);
	var total= +hrs + (+hrs_cr*1.5) + (+hrs_td*1) + (+hrs_tp*(3/4));
//	var total= +hrs + +hrs_cr + (+hrs_td) + (+hrs_tp);
	//alert(hrs_cr +'  '+total);
	//alert (total);
	html+='  ==> <span class="label label-'+label(total,chrg)+'">'+total+'/'+chrg+'</span>';
	html += '<br/><div class="progress progress-'+label_2(total,chrg)+'" style="margin: 5px;"><div class="bar" style="width: '+(+total/+chrg)*100+'%;"></div>    </div></div>';
    
	
	document.getElementById('element3').innerHTML = html;
	});
	
	
	//*****
	wizard.cards["card1"].on("loaded", function(card) {
	var s=$("#semestre").val();
	var link="load.php?type=module&c=naff&s="+s;
	load_modules(link);
	
		
	load_enseignant();
	});
	wizard.cards["card1"].on("validate", function(card) {
    var val1 = $("#sel_mod").val();
    var val2 = $("#sel_enseignant").val();
	var err=0;
    if (val1 == "") {
        card.wizard.errorPopover( card.el.find("#mod"), "Il faut choisir un module!");
        err=1;
    }
	if (val2 == "") {
        card.wizard.errorPopover( card.el.find("#prof"), "Il faut choisir un enseignant!");
        err=1;
    }
	if(err) return false;
    return true;
	});
	//card 2
	wizard.cards["card2"].on("selected", function(card) {
	var link="load.php?get=Affect2&id="+$("#sel_mod").select2("val")+"&prof="+$("#sel_enseignant").select2("val");
 /*   $("#element").load(res);
	
	var hrs=document.getElementById("charge").getAttribute("data-hrs-aff");
	var chrg=document.getElementById("charge").getAttribute("data-charge");
	
	html = '<div class="progress progress-'+label_2(hrs,chrg)+'" style="margin: 5px;"><div class="bar" style="width: '+(+hrs/+chrg)*100+'%;"></div>    </div></div>';
    
	document.getElementById('progress_bar').innerHTML = html;
*/	load_affect(link);

	document.getElementById("element").setAttribute("data-on", "1");
//	change_box();
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
	});
	
		wizard.on("reset", function(wizard) {
		wizard.setSubtitle("");
		wizard.el.find("#nbr").val("");
		var s=$("#semestre").val();
		var link="load.php?type=module&c=naff&s="+s;
		load_modules(link);
		load_enseignant();
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

/* Recharger les Module lors du changement du semestre */
 $("#semestre").change(function(){
   var s=$(this).val();
   var link="load.php?type=module&c=naff&s="+s;

   load_modules(link);
$('#sel_mod').val("");
});

$("#sel_mod").on("change", function(e) { 

//alert(document.getElementById("element").getAttribute("data-on"));
if(document.getElementById("element").getAttribute("data-on") == "1")
{
	var link="load.php?get=Affect2&id="+$("#sel_mod").select2("val")+"&prof="+$("#sel_enseignant").select2("val");
	load_affect(link); 
}
});
$("#sel_enseignant").on("change", function(e) { 
//alert ($('#sel_enseignant').val());
$("#prof_stat").load("load.php?get=prof_stat&id="+$('#sel_enseignant').val());
var x=document.getElementById("element").getAttribute("data-on");

if( x == "1")
{
	var link="load.php?get=Affect2&id="+$("#sel_mod").select2("val")+"&prof="+$("#sel_enseignant").select2("val");
	load_affect(link); 
}
});


