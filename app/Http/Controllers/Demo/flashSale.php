<?php
$conn = mysqli_connect("192.168.59.123",'root','feixun*123');
if (!$conn) {
    die('数据库连接失败'.mysqli_error($conn));
}
$result = mysqli_select_db($conn,"flash_sale");

if (!$result) {
    die('数据库不能成功连接'.mysqli_error($result));
} else {
    echo "数据库连接成功";
}


