<?php

namespace App\Services;

use App\Models\ContentModel;
use Illuminate\Support\Facades\Log;

class ContentService
{
    protected $contentModel;

    public function __construct(ContentModel $contentModel)
    {
        $this->contentModel = $contentModel;
    }

    /**
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function add($params)
    {
        $params['create_at'] = time();
        $params['modify_at'] = time();
        try{
            $this->contentModel->create($params);
            return true;
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}