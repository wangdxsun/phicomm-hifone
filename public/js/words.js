/**
 * Created by wei07.wang on 2017/3/27.
 */
$(document).ready(function(){
    $(".modify_info").click(function () {
        var info=$(this).data("name");
        var infoArray=info.split(",");
        var id=infoArray[0];
        var find=infoArray[1];
        var substitute=infoArray[2];

        $("#word-id").val(id);
        $("#word-find").val(find);
        $("#word-substitute").val(substitute);
        $("#myModal").modal('show');
    });

    $(".btn-default").click(function(){
        $("#myModal").modal('hide');
    });
    $(".head_portrait").click(function () {
        $("#import").click();
    });


});
function  commitForm() {
    $("#importExcel").submit();
}
