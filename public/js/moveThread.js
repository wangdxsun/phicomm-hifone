$(document).ready(function(){

    $("#cancel").click(function(){
        $("#moveThreadModal").modal('hide');
    });
    // $("#commit").click(function(){
    //     $("#chooseSubNode").submit();
    //     $("#batchMoveThread").submit();
    // });
});
function moveThread() {
    $("#moveThreadModal").modal('show');
}