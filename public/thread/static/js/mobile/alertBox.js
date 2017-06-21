function showAlertBox(message, time,callback) {
    $('<div class="shadow"><div class="tipBox1"><span class="contentText"></span></div></div>').appendTo('body');
    $('.contentText').text(message);
    var hh = window.innerHeight;
    var shadow = $('.shadow');
    var tipBox1 = $('.tipBox1');
    var thx = tipBox1.height();
    var mhx = (hh - thx) / 2;
    tipBox1.css('marginTop', mhx);
    time = time || 2000;
    setTimeout(function () {
        shadow.remove();
        tipBox1.remove();
        $('.contentText').empty();
        if (typeof callback == "function"){
            callback();
        }
    }, time);
}

function showConfirmBox(message, title, btnCancel, btnOk, cancelCallback, okCallback) {
    $('<div class="shadow">' +
        '<div class="tipBox2">' +
        '<div class="contentBox">' +
        '<span class="titleTs"></span>' +
        '<span class="contentTs"></span>' +
        '</div><div class="cancel"></div>' +
        '<div class="okBtn"></div></div></div>').appendTo('body');
    $('.contentTs').text(message);
    $('.titleTs').text(title);

    var hh = window.innerHeight;
    var shadow = $('.shadow');
    var tipBox2 = $('.tipBox2');
    var cancelBtn = $('.cancel');
    var okBtn = $('.okBtn');
    var th = tipBox2.height();
    var mh = (hh - th) / 2;
    cancelBtn.text(btnCancel);
    okBtn.text(btnOk);
    tipBox2.css('marginTop', mh);

    okBtn.click(function() {
        shadow.remove();
        tipBox2.remove();
        if (typeof okCallback == "function"){
            okCallback();
        }
    });

    cancelBtn.click(function () {
        shadow.remove();
        tipBox2.remove();
        if (typeof okCallback == "function"){
            cancelCallback();
        }
    });
}