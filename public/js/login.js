function isApp() {
    var ua = navigator.userAgent.toLowerCase();
    var index = ua.indexOf("phiwifi");
    var version = ua.substr(index+8, 6).trim();
    if (version.indexOf("v") == 0) {
        version = version.substr(1);
    }
    return index > -1 && version >= "4.2.0";
}

function phicommLoginCallback(result) {
    if (result.status.code == 0) {
        if (result.data.token) {
            phicommLogin(result.data.token, true);
        } else {
            showAlertBox('登录失败');
        }
    }
}

function phicommLogin(token, register) {
    $.post("/phicomm/login", {phicommToken: token}).done(function(result) {
        if (result.error > 0) {
            showAlertBox(result.message);
        } else {
            if(result.data.bind == 1) {
                //如果已经关联过账号就跳转到首页
                location.href = '/';
            } else {
                if (register) {
                    location.href = '/login/registerDiscuz.html';
                }
            }
        }
    });
}

function bindPhicommCallback(result) {
    if (result.status.code == 0) {
        if (result.data.token) {
            Cookies.set('phicommToken', result.data.token, {path: "/"});
            bindPhicomm();
        } else {
            showAlertBox('登录失败');
        }
    }
}

function bindPhicomm() {
    $.post("/login/bindPhicomm.php").done(function(result) {
        if (result.error > 0) {
            showAlertBox(result.message, 2000, function() {
                location.href = '/forum.php';
            });
        } else {
            location.href = '/forum.php';
        }
    });
}

function forcePhicommLogin(callback) {
    FXJSBridge.callMethod('JsInvokeJavaScope', 'requestTokenFromNative', {
        'data': {
            'isRefresh': 1,
            'hasOldToken': 0,
            'token': ""
        }
    }, callback);
}

function register() {
    if (isApp()) {
        forcePhicommLogin(phicommLoginCallback);
    } else {
        location.href = '/login/registerPhicomm.html';
    }
}

function autoLogin() {
    if ((isApp())) {
        FXJSBridge.callMethod('JsInvokeJavaScope', 'requestTokenFromNative', {
            data: {
                isRefresh: 0,
                hasOldToken: 0,
                token: ""
            }
        }, autoLoginCallback);
    }
}

function autoLoginCallback(result) {
    if (result.status.code == 0) {
        if (result.data.token) {
            phicommLogin(result.data.token, false);
        }
    }
}

function login() {
    if ((isApp())) {
        $.post("/login/checkDiscuzLogin.php").done(function(result) {
            if (result.error == 1) {
                FXJSBridge.callMethod('JsInvokeJavaScope', 'requestTokenFromNative', {
                    'data': {
                        'isRefresh': 0,
                        'hasOldToken': 0,
                        'token': ""
                    }
                }, loginCallback);
            }
        });
    } else {
        location.href = "/login/login.html";
    }
}

function loginCallback(result) {
    if (result.status.code == 0) {
        if (result.data.token) {
            Cookies.set('phicommToken', result.data.token, {path: "/"});
            phicommLogin(true);
        } else {
            location.href = '/login/login.html';
        }
    } else {
        location.href = '/login/login.html';
    }
}

function logoutConfirm() {
    if (isApp()) {
        //检查当前登录的社区账号和云账号是否已关联，如果已关联则一起退出，否则只退出社区账号
        $.post('/login/checkBind.php').done(function(result) {
            if (result.error == 0) {
                FXJSBridge.callMethod('JsInvokeJavaScope', 'requestNativeLogOut', null, null);
            } else {
                clearCookies();
                $.post('/login/logout.php').done(function() {
                    location.reload(true);
                });
            }
        });
    } else {
        $.post('/login/logout.php').done(function() {
            location.reload(true);
        });
    }
}

function clearCookies(){
    var keys = document.cookie.match(new RegExp("([^ ;][^;]*)(?=(=[^;]*)(;|$))", "gi"));
    for (var i in keys){
        document.cookie = keys[i] + "=;expires=Mon, 26 Jul 1997 05:00:00 GMT; path=/;";
    }
}
