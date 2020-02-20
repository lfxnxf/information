<?php

namespace App\Http\Controllers\Admin;

use App\Services\ContentService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContentController extends Controller
{
    protected $contentService = null;

    protected $uploadService = null;

    public function __construct(UploadService $uploadService, ContentService $contentService)
    {
        $this->uploadService = $uploadService;
        $this->contentService = $contentService;
    }

    public function add(Request $request)
    {
        $params = [
            'title'          => $request->input('title', ''),
            'content'        => $request->input('content', ''),
            'type'           => $request->input('type', ''),
            'create_user_id' => $request->input('user_id', ''),
            'source'         => $request->input('source', 0),
            'category'       => $request->input('category', 0),
        ];
        //上传图片
        $headImg = $this->uploadService->uploadImg($request->file('img_head'));
        $params['head_img'] = $headImg;
        $this->contentService->add($params);
    }
}
