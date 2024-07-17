/* Messages Modal */


$(".stat-btn").click(function() {
var link="load.php?get=Affect_stat&prof="+this.getAttribute("data-id");
$("#edit .modal-body").load(link);
});

$(".fs").click(function(e) {

    $(".modal").toggleClass("full-screen");
    $(".modal").toggleClass("medium-modal");
});
