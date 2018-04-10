<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Jenssegers\Agent\Facades\Agent;
use Hifone\Models\Thread;
use Hifone\Exceptions\HifoneException;

if (!function_exists('back_url')) {
    /**
     * Create a new back url.
     *
     * @param string|null $route
     * @param array       $parameters
     * @param int         $status
     * @param array       $headers
     *
     * @return string
     */
    function back_url($route = null, $parameters = [], $status = 302, $headers = [])
    {
        $url = app('url');

        if ($route !== null && $url->previous() === $url->full()) {
            return $url->route($route, $parameters, $status, $headers);
        }

        return $url->previous();
    }
}

if (!function_exists('set_active')) {
    /**
     * Set active class if request is in path.
     *
     * @param string $path
     * @param array  $classes
     * @param string $active
     *
     * @return string
     */
    function set_active($path, array $classes = [], $active = 'active')
    {
        if (Request::is($path)) {
            $classes[] = $active;
        }
        $class = e(implode(' ', $classes));

        return empty($classes) ? '' : "class=\"{$class}\"";
    }
}

if (!function_exists('getFirstImageUrl')) {
    function getFirstImageUrl($body) {
        //去掉表情，但是又要允许alt等其他属性
        preg_match_all('/<img[^>|class="face"]*src=["\']{1}([^"\'>]*)["\'][^>]*>/i', $body, $images);
        $imgUrls = [];
        if (count($images) > 0) {
            foreach ($images[1] as $k => $v) {
                $imgUrls[] = $v;
            }
        }
        $imgUrl = array_first($imgUrls, function($key,$value) {
            if (!(Str::contains($value, 'icon_apk')) && !(Str::contains($value, 'icon_bin')) && !(Str::contains($value, 'icon_word'))) {
                return $value;
            }
            return '';
        }, '');
        return $imgUrl;
    }
}

if (!function_exists('thread_filter')) {
    /**
     * Create a node url by filter.
     *
     * @param string|null $filter
     *
     * @return string
     */
    function thread_filter($filter)
    {
        $node_id = Request::segment(2);
        $node_append = '';
        if ($node_id) {
            $link = URL::to(is_numeric($node_id) ? 'node' : 'go', $node_id).'?filter='.$filter;
        } else {
            $query_append = '';
            $query = Input::except('filter', '_pjax');
            if ($query) {
                $query_append = '&'.http_build_query($query);
            }
            $link = URL::to('thread').'?filter='.$filter.$query_append.$node_append;
        }
        $selected = Input::get('filter') ? (Input::get('filter') == $filter ? ' class="selected"' : '') : '';

        return 'href="'.$link.'"'.$selected;
    }
}

if (!function_exists('cdn')) {
    /**
     * Create a new cdn url.
     *
     * @param string|null $filepath
     *
     * @return string
     */
    function cdn($filepath = '')
    {
        if (Config::get('setting.site_cdn')) {
            return Config::get('setting.site_cdn').$filepath;
        } else {
            return Config::get('app.url').$filepath;
        }
    }
}

if (!function_exists('option_is_selected')) {
    /**
     * Check if option is selected and output selected else output an empty string.
     *
     * @param array $array
     *
     * @return string
     */
    function option_is_selected(array $array)
    {
        $resource = $array[0];
        $haystack = $array[1];
        $currentResource = isset($array[2]) ? $array[2] : '';

        return (old($haystack) == $resource->id) || ($currentResource && $currentResource->$haystack == $resource->id)
            ? 'selected' : '';
    }
}

if (!function_exists('checkbox_is_active')) {
    /**
     * Check if checkbox is selected and output checked else output an empty string.
     *
     * @param string $haystack
     * @param $resource
     *
     * @return string
     */
    function checkbox_is_active($haystack, array $resource)
    {
        return (old($haystack) == '1') || ($resource && $resource->$haystack == 1) ? 'checked' : '';
    }
}

if (!function_exists('admin_link')) {
    function admin_link($title, $path, $id = '')
    {
        return '<a href="'.admin_url($path, $id).'" target="_blank">'.$title.'</a>';
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path, $id = '')
    {
        return env('APP_URL')."/admin/$path".($id ? '/'.$id : '');
    }
}

if (!function_exists('isH5')) {
    function isH5()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        return strpos(strtolower($ua), 'iphone') || strpos(strtolower($ua), 'android');
    }
}

if (!function_exists('isApp')) {
    function isApp()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        return strpos($ua, 'PhiWifiNative');
    }
}

if (!function_exists('error')) {
    function error($message = "")
    {
        return [
            'code' => 1,
            'msg' => $message
        ];
    }
}

if (!function_exists('success')) {
    function success($msg) {
        return response()->json(['msg' => $msg]);
    }
}

if (!function_exists('curlGet')) {
    function curlGet($url, $header = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (! empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

if (!function_exists('curlPost')) {
    function curlPost($url, $data, $header = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        if(! empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

if (!function_exists('curl_get')) {
    function curl_get($url ,$header=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if(!empty($header)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        $output = curl_exec($ch);
        curl_close($ch);

        //打印获得的数据
        return $output;
    }
}

if (!function_exists('curl_form_post')) {
    function curl_form_post($url,$data,$header=null,$method='post')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($method=='post'&&empty($header)){
            curl_setopt($ch, CURLOPT_POST, 1);
        }else if($method=='post'&&!empty($header)){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
            if (!empty($header)) {
                $new_header = array("X-HTTP-Method-Override: $method",$header);
            } else {
                $new_header = array("X-HTTP-Method-Override: $method");
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $new_header);
        }else{
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
            if (!empty($header)) {
                $new_header = array("X-HTTP-Method-Override: $method",$header);
            } else {
                $new_header = array("X-HTTP-Method-Override: $method");
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $new_header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);

        //打印获得的数据
        return $output;
    }
}

if (!function_exists('get_request_agent')) {
    function get_request_agent()
    {
        if (Agent::match('PhiWifiNative') && Agent::match('iPhone')) {
            $agent = Thread::IOS;
        } elseif (Agent::match('PhiWifiNative') && Agent::match('Android')) {
            $agent = Thread::ANDROID;
        } elseif (Agent::match('iPhone') || Agent::match('Android')) {
            $agent = Thread::H5;
        } else {
            $agent = Thread::WEB;
        }

        return $agent;
    }
}

if (!function_exists('get_app_version')) {
    function get_app_version()
    {
        $preArr = explode("PhiWifi/", $_SERVER['HTTP_USER_AGENT'], 2);
        $pre = array_get($preArr, 1);
        if (is_null($pre)) {
            throw new HifoneException('UserAgent格式不正确');
        }

        $middleArr = explode(".", $pre, 4);
        $middle0 = array_get($middleArr, 0);
        $middle1 = array_get($middleArr, 1);
        $middle2 = array_get($middleArr, 2);
        $middle2 = substr($middle2, 0, 1);
        if (is_null($middle0) || is_null($middle1) || is_null($middle2)
        || !is_numeric($middle0) || !is_numeric($middle1) || !is_numeric($middle2)) {
            throw new HifoneException('UserAgent格式不正确');
        }
        $version = $middle0 . "." . $middle1 . "." . $middle2;

        return $version;
    }
}

function correct_image_orientation($target) {
    $exif = @exif_read_data($target);
    if($exif && isset($exif['Orientation']) && $exif['Orientation'] != 1) {
        switch ($exif['Orientation']) {
            case 3: $deg = 180; break;
            case 6: $deg = 270; break;
            case 8: $deg = 90; break;
            default: $deg = 0;
        }
        if ($deg > 0) {
            $img = imagecreatefromjpeg($target);
            ini_set('memory_limit', '256M');
            $img = imagerotate($img, $deg, 0);
            imagejpeg($img, $target);
        }
    }
}

function getClientIp() {
    $ip = "unknown";
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_ip"])) {
            $ip = $_SERVER["HTTP_CLIENT_ip"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_ip')) {
            $ip = getenv('HTTP_CLIENT_ip');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }
    if(trim($ip)=="::1"){
        $ip="127.0.0.1";
    }
    return $ip;
}

function getChars($utf8_str)
{
    $s = $utf8_str;
    $len = strlen($s);
    if ($len == 0) return array();
    $chars = array();
    for ($i = 0; $i < $len; $i++) {
        $c = $s[$i];
        $n = ord($c);
        if (($n >> 7) == 0) {       //0xxx xxxx, asci, single
            $chars[] = $c;
        } else if (($n >> 4) == 15) {     //1111 xxxx, first in four char
            if ($i < $len - 3) {
                $chars[] = $c . $s[$i + 1] . $s[$i + 2] . $s[$i + 3];
                $i += 3;
            }
        } else if (($n >> 5) == 7) {  //111x xxxx, first in three char
            if ($i < $len - 2) {
                $chars[] = $c . $s[$i + 1] . $s[$i + 2];
                $i += 2;
            }
        } else if (($n >> 6) == 3) {  //11xx xxxx, first in two char
            if ($i < $len - 1) {
                $chars[] = $c . $s[$i + 1];
                $i++;
            }
        }
    }
    return $chars;
}