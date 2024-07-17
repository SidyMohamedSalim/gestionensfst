/**
 * javascript pour la pages des fiches de souhaits (fiches_souhaits.php)
 * Date: 28/10/13
 * Time: 22:30
 */

$('#sel_filiere').select2({

    placeholder: "Selection d'une filière",
    allowClear: true,
    blurOnChange: true,
    openOnEnter: false
});

// badge des souhaits
function get_badge(x,y) {
    x= parseInt(x);
    y= parseInt(y);
    var clss="badge";
    if(y==0) clss="badge ";
    else if(x==0) clss="badge  badge-warning";
    else if(x < y) clss="badge  badge-info";
//    else if(y==0) clss="badge ";
    else if(x==y) clss="badge  badge-success";
else clss="badge  badge-important";
    return clss;
}

//Ajax
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
var x=0;

// Eliminer un choix dans la fiche
$(".drop_wish").click(function(event) {
//    event.stopPropagation();
    if(x==0)
    {
        x=1;
        var th=this;
        var t=this.getAttribute("data-type");
        var idE=this.getAttribute("data-elemd");
        var prof=this.getAttribute("data-prof");

        var f=document.getElementById("fiche");
        var fiche= f.getAttribute("data-id");

//    var xmlhttp;

        var url="process_gestion.php?new=fiche_elem_mod&idE="+idE+"&fiche="+fiche+"&t="+t+"&prof="+prof+"&what=drop";
//    alert(url);
        loadXMLDoc(url,function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                var msg=JSON.parse(xmlhttp.responseText);

                if(msg.errors)
                {
                    alert(msg.errors);
                }
                else if(msg.success)
                {
                    var span=$(th).parent().children("span");
                    span.html(msg.success.choosed + "/" + msg.success.total);
                    span.removeClass(function() { return $( this ).attr( "class" );  });

                    span.addClass(get_badge(msg.success.choosed,msg.success.total));
                }
                x=0;
            }

        });
    }

 //   window.location.reload();
});

// Augementer un choix dans la fiche
$(".add_wish").on("click",function(event) {
 //   event.stopImmediatePropagation();
//    $(this).off();
 //  console.log("here x="+x);
    if(x==0)
    {
        x=1;
        var th=this;

        var t=this.getAttribute("data-type");
        var idE=this.getAttribute("data-elemd");
        var prof=this.getAttribute("data-prof");

        var f=document.getElementById("fiche");
        var fiche= f.getAttribute("data-id");
//    alert( $(this).parent());
        //   var xmlhttp;

        var url="process_gestion.php?new=fiche_elem_mod&idE="+idE+"&fiche="+fiche+"&t="+t+"&prof="+prof+"&what=add";
//    alert(url);
        loadXMLDoc(url,function()
        {

            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                var msg=JSON.parse(xmlhttp.responseText);
                //  alert(xmlhttp.responseText);

                if(msg.errors)
                {
                    alert(msg.errors);
                }
                else if(msg.success)
                {
                    var span=$(th).parent().children("span");
                    span.html(msg.success.choosed + "/" + msg.success.total);
                    span.removeClass(function() { return $( this ).attr( "class" );  });

                    span.addClass(get_badge(msg.success.choosed,msg.success.total));
                }
                x=0;
            }

        });

    }




});