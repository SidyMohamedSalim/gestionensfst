/* number only */
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

/* x-editable*/
$.fn.editable.defaults.emptytext="Vide";
/*
$('#enable').click(function() {
       $('#liste .editable').editable('toggleDisabled');
   });    
   */
  /*  $('#nouv_annee').editable('option', 'validate', function(v) {
	if(!v) return 'Required field!';
	success: location.reload();
	});
	*/
	// annee courrante
	$('.annee').editable({
    type: 'select',
	placement: 'bottom',
    url: 'process_gestion.php',
	source: 'load.php?type=annee',
    title: 'Annee Univ. courrante?',
        ajaxOptions: {
            dataType: 'json' //assuming json response
        },
        success: function(response, newValue) {
            if(!response.succes) return response.mssg;
            else location.reload();
        }
        ,
        error: function(response, newValue) {
            if(response.status === 500)
                return 'Service unavailable. Please try later.';
        }

    });


	/*desactivate edition at first*/
//	$('#liste .editable').editable('toggleDisabled');

/* checkbox activation $(".collapse").collapse() */
/*
$('#null_M').change(
    function(){
        if ($(this).is(':checked')){
            $('#dept_M').prop('disabled',true);
        }
        else {
            $('#dept_M').prop('disabled',false);
        }
    });

 */
 
/* tooltip */
//$('.tip_top').tooltip();

            var table2,table1;
			/* Table initialisation */
			$(document).ready(function() {

                var responsiveHelper = undefined;
                var breakpointDefinition = {
                    tablet: 1024,
                    phone : 480
                };

                if( typeof message1 === 'undefined' )
                    message1="";
                if( typeof message2 === 'undefined' )
                    message2="";

                if( typeof length === 'undefined' )
                    var length=10;

                var http = new XMLHttpRequest();

                var tableElement='#liste1';
                if ($(tableElement).length != 0) {
                    table1 = $(tableElement).DataTable( {
                        "iDisplayLength": window.length,
                        /*
                         "oTableTools": {
                         "aButtons": [
                         "xls",
                         {
                         "sExtends": "pdf",
                         "sPdfMessage": message1
                         },
                         "print"
                         ]},
                         */
                        bAutoWidth     : false,

                        fnPreDrawCallback: function () {
                            // Initialize the responsive datatables helper once.
                            if (!responsiveHelper) {
                                responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                            }
                        },
                        fnRowCallback    : function (nRow) {
                            responsiveHelper.createExpandIcon(nRow);
                        },
                        fnDrawCallback   : function (oSettings) {
                           //  alert('gg');
                            responsiveHelper.respond();
                        }
                    } );

                    $(tableElement).on( 'length.dt', function ( e, settings, len ) {

                        http.open("GET", "process_gestion.php?ePP="+len, true);
                        /*
                         http.onreadystatechange = function() {//Call a function when the state changes.
                         if(http.readyState == 4 && http.status == 200) {
                         alert(http.responseText);
                         }
                         }
                         */
                        http.send(null);
                    } );
                }



                tableElement='#liste2';
                if ($(tableElement).length != 0) {
                    responsiveHelper = undefined;

                    table2 = $(tableElement).DataTable( {
                        /*
                         "oTableTools": {
                         "aButtons": [
                         "xls",
                         {
                         "sExtends": "pdf",
                         "sPdfMessage": message2
                         },
                         "print"
                         ]}
                         */
                        "iDisplayLength": window.length,
                        bAutoWidth     : false,
                        fnPreDrawCallback: function () {
                            // Initialize the responsive datatables helper once.
                            if (!responsiveHelper) {
                                responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                            }
                        },
                        fnRowCallback    : function (nRow) {
                            responsiveHelper.createExpandIcon(nRow);
                        },
                        fnDrawCallback   : function (oSettings) {
                            // alert('gg');
                            responsiveHelper.respond();
                        }
                    } );

                    $(tableElement).on( 'length.dt', function ( e, settings, len ) {
                        http.open("GET", "process_gestion.php?ePP="+len, true);
                        http.send(null);
                    } );
                }
                //hide columns

                $('a.toggle-vis').on( 'click', function (e) {
                    e.preventDefault();

                    if ($("#liste1").length != 0) {
                        // Get the column API object
                        var column1 = table1.column( $(this).attr('data-column') );
                        // Toggle the visibility
                        column1.visible( ! column1.visible() );
                    }
                    if ($("#liste2").length != 0) {
                        // Get the column API object
                        var column2 = table2.column( $(this).attr('data-column') );
                        // Toggle the visibility
                        column2.visible( ! column2.visible() );
                    }

                    $( this ).children().toggleClass("label-info");
                } );



                //themes, change CSS with JS


                $('#themes a[data-value="'+current_theme+'"]').find('i').addClass('icon-ok');

                $('#themes a').click(function(e){
                    e.preventDefault();
                    current_theme=$(this).attr('data-value');
                //    $.cookie('current_theme',current_theme,{expires:365});
                    http.open("GET", "process_gestion.php?theme="+current_theme, true);
                    http.send(null);
                    switch_theme(current_theme);
                    $('#themes i').removeClass('icon-ok');
                    $(this).find('i').addClass('icon-ok');
                });


                function switch_theme(theme_name)
                {
                    $('#bs-css').attr('href','styles/bootstrap-'+theme_name+'.min.css');
                }
            } );


/*
$(document).ready(function(){
    //themes, change CSS with JS
//default theme(CSS) is cerulean, change it if needed
    var current_theme = $.cookie('current_theme')==null ? 'cerulean' :$.cookie('current_theme');
    switch_theme(current_theme);

    $('#themes a[data-value="'+current_theme+'"]').find('i').addClass('icon-ok');

    $('#themes a').click(function(e){
        e.preventDefault();
        current_theme=$(this).attr('data-value');
        $.cookie('current_theme',current_theme,{expires:365});
        switch_theme(current_theme);
        $('#themes i').removeClass('icon-ok');
        $(this).find('i').addClass('icon-ok');
    });


    function switch_theme(theme_name)
    {
        $('#bs-css').attr('href','styles/bootstrap-'+theme_name+'.css');
    }

});
*/
