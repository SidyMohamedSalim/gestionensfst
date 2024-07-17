$(".fs").click(function(e) {

    $(".modal:not(.wizard-modal)").toggleClass("full-screen");
    $(".modal:not(.wizard-modal)").toggleClass("medium-modal");
    ;
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




    });

});
