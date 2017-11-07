$(document).ready(function(){

    $("#cancel").click(function(){
        $("#moveThreadModal").modal('hide');
    });
});
function moveThread() {
    $("#moveThreadModal").modal('show');
}