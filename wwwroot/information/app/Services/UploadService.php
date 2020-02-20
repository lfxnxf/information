<?php

namespace App\Services;


use App\Exceptions\UploadException;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * 图片上传处理
     * @param $file
     * @return string
     * @throws UploadException
     */
    public function uploadImg($file)
    {
        //上传文件最大大小,单位M
        $maxSize = 10;
        //支持的上传图片类型
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];

        //检查文件是否上传完成
        if (!$file->isValid()) {
            UploadException::throwUploadFailedException($file->getErrorMessage());
        }
        //检测图片类型
        $ext = $file->getClientOriginalExtension();
        if (!in_array(strtolower($ext), $allowedExtensions)) {
            UploadException::throwUploadFailedException('请上传' . implode(',', $allowedExtensions) . '格式的图片');
        }
        //检测图片大小
        if ($file->getClientSize() > $maxSize * 1024 * 1024) {
            UploadException::throwUploadFailedException('图片大小限制' . $maxSize . 'M');
        }
        $disk = Storage::disk('uploadImg');
        $newFile = uniqid() . time() . '.' . $file->getClientOriginalExtension();
        $res = $disk->put($newFile, file_get_contents($file->getRealPath()));
        if ($res) {
            $downloadUrl = env('APP_URL') . '/upload/images/' . date('Ymd') . '/' . $newFile;
            return $downloadUrl;
        } else {
            UploadException::throwUploadFailedException($file->getErrorMessage());
        }
    }
}