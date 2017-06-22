/**
 * Created on 16/12/05.
 *
 协定协议:FXJSBridge://class/method?params;
 params是一串json字符串
 */

(function () {
    var doc = document;
    var win = window;
    var ua = win.navigator.userAgent;
    var JS_BRIDGE_PROTOCOL_SCHEMA = "FXJSBridge";
    var increase = 1;
    var FXJSBridge = win.FXJSBridge || (win.FXJSBridge = {});

    var ExposeMethod = {

        callMethod: function (clazz, method, param, callback) {

            // 没有回调 不用添加port参数
            if (callback !== null ) {
                var port = PrivateMethod.generatePort();
                // 将port添加到参数中
                param = PrivateMethod.addPort2Param(port,param);
                if (typeof callback !== 'function') {
                    callback = null;
                }
                PrivateMethod.registerCallback(port, callback);
            }

            PrivateMethod.callNativeMethod(clazz, method, param);
        },

        onComplete: function (result) {
            PrivateMethod.onNativeComplete(result);
        },

        onLogOut: function () {
            //当收到app要求注销时，先检查一下是否为关联账号，如果已关联就退出
            $.post('/login/checkBind.php').done(function(result) {
                if (result.error == 0) {
                    $.post('/login/logout.php').done(function() {
                        location.reload();
                    });
                }
            });
        }
    };

    var PrivateMethod = {
        callbacks: {},
        addPort2Param: function (port,param) {
            // param为空 重新创建
            if (param == null) {
                param = {};
            }
            param["port"]=Number(port);
            return param;
        },
        registerCallback: function (port, callback) {
            if (callback) {
                PrivateMethod.callbacks[port] = callback;
            }
        },
        getCallback: function (port) {
            var call = {};
            if (PrivateMethod.callbacks[port]) {
                call.callback = PrivateMethod.callbacks[port];
            } else {
                call.callback = null;
            }
            return call;
        },
        unRegisterCallback: function (port) {
            if (PrivateMethod.callbacks[port]) {
                delete PrivateMethod.callbacks[port];
            }
        },
        onNativeComplete: function (result) {
            var resultJson = PrivateMethod.str2Json(result);
            var port = resultJson["port"];

            var callback = PrivateMethod.getCallback(port).callback;
            PrivateMethod.unRegisterCallback(port);
            if (callback) {
                //执行回调
                callback && callback(resultJson);
            }
        },
        generatePort: function () {
            return Math.floor(Math.random() * (1 << 50)) + '' + increase++;
        },
        str2Json: function (str) {
            if (str && typeof str === 'string') {
                try {
                    return JSON.parse(str);
                } catch (e) {
                    return {
                        status: {
                            code: 1,
                            msg: 'params parse error!'
                        }
                    };
                }
            } else {
                return str || {};
            }
        },
        json2Str: function (param) {
            if (param && typeof param === 'object') {
                return JSON.stringify(param);
            } else {
                return param || '';
            }
        },
        callNativeMethod: function (clazz, method, param) {
            var jsonStr = "";
            if (param !== null) {
                jsonStr = PrivateMethod.json2Str(param);
            }
            if (PrivateMethod.isAndroid()) {

                var uri = JS_BRIDGE_PROTOCOL_SCHEMA + "://" + clazz + "/" + method + "?" + jsonStr;
                win.prompt(uri, "");
            }
            if (PrivateMethod.isIos()) {
                var url = JS_BRIDGE_PROTOCOL_SCHEMA + "://" + method + "?" + jsonStr;
                PrivateMethod.loadURL(url);
            }
        },
        // iOS使用，发起URL请求
        loadURL: function (url) {
            var iFrame;
            iFrame = doc.createElement("iframe");
            iFrame.setAttribute("src", url);
            iFrame.setAttribute("style", "display:none;");
            iFrame.setAttribute("height", "0px");
            iFrame.setAttribute("width", "0px");
            iFrame.setAttribute("frameborder", "0");
            doc.body.appendChild(iFrame);
            // 发起请求后这个iFrame就没用了，所以把它从dom上移除掉
            iFrame.parentNode.removeChild(iFrame);
            iFrame = null;
        },
        isAndroid: function () {
            var tmp = ua.toLowerCase();
            var android = tmp.indexOf("android") > -1;
            return !!android;
        },
        isIos: function () {
            var tmp = ua.toLowerCase();
            var ios = tmp.indexOf("iphone") > -1;
            return !!ios;
        }
    };
    for (var index in ExposeMethod) {
        if (ExposeMethod.hasOwnProperty(index)) {
            if (!Object.prototype.hasOwnProperty.call(FXJSBridge, index)) {
                FXJSBridge[index] = ExposeMethod[index];
            }
        }
    }
})();