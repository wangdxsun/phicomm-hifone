/**
 * Created by wei07.wang on 2017/3/27.
 */
$(document).ready(function(){
    $(".modify_info").click(function () {
        var info=$(this).data("name");
        var infoArray=info.split(",");
        var id=infoArray[0];
        var type=infoArray[1];
        var word=infoArray[2];
        var status=infoArray[3];
        var replacement=infoArray[4];

        $("#word-id").val(id);
        $("#word-type").val(type);
        $("#word-word").val(word);
        $("#word-status").val(status);
        $("#word-replacement").val(replacement);
        $("#myModal").modal('show');
    });

    $(".btn-default").click(function(){
        $("#myModal").modal('hide');
    });

    $(".head_import").click(function () {
        $("#import").click();
    });

    $(".head_check").click(function () {
        $("#check").click();
    });

});

function  commitForm() {
    $("#importExcel").submit();
}

function commitCheckForm() {
    $("#checkExcel").submit();
}
