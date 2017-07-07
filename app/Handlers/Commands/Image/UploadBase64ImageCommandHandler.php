<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Image;

use Auth;
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Events\Image\ImageWasUploadedEvent;
use Intervention\Image\ImageManagerStatic as Image;

class UploadBase64ImageCommandHandler
{
    public function handle(UploadBase64ImageCommand $command)
    {
        $file = $command->file;
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)){
            $extension = $result[2];
            if (!in_array($extension, $allowed_extensions)) {
                throw new \Exception('只支持 png, jpg, jpeg 和 gif');
            }
            $folderName = '/uploads/images/'.date('Ym', time()).'/'.date('d', time()).'/'.Auth::user()->id;
            $destinationPath = public_path().'/'.$folderName;
            is_dir($destinationPath) || mkdir($destinationPath, 0777, true);
            $safeName = str_random(10).'.'.$extension;
            $newFile = $destinationPath.'/'.$safeName;
            file_put_contents($newFile, base64_decode(str_replace($result[1], '', $file)));
            //correct_image_orientation($newFile);//IOS拍照直接上传的图片需要旋转90度

            $data['filename'] = request()->getSchemeAndHttpHost().$folderName.'/'.$safeName;
            $data['localFile'] = $newFile;
            event(new ImageWasUploadedEvent($data));

            return $data;
        } else {
            throw new \Exception('文件错误');
        }
    }
}
