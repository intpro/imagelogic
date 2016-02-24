<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateImageAction extends ImageAction
{
    private $req_file;
    private $pathResolver;
    private $report;

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $req_file
     *
     * @return void
     */
    public function __construct(UploadedFile $req_file)
    {
        $this->req_file  = $req_file;
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');
        $this->report       = App::make('Interpro\ImageFileLogic\Concept\Report');
    }

    public function applyFor(ImageItem $imageItem)
    {
        $images_dir = $this->pathResolver->getImageDir();

        $temp_name      = $this->req_file->getClientOriginalName();;
        $ext            = $this->req_file->getClientOriginalExtension();

        $imageItem->setExt($ext);

        $new_image_name    = $imageItem->getName();

        $uploadflag = $this->req_file->move(
            $images_dir,
            $new_image_name
        );

        if(!$uploadflag)
        {
            throw new ImageFileSystemException('Не удалось переписать временный файл '.$temp_name.' по новому пути '.$new_image_name);
        }

        $image_path = $images_dir.'/'.$new_image_name;

        Log::info('Загружена картинка: ' . $image_path);

        $this->report->setImageResize($imageItem->getNameWoExt(), 'original', $new_image_name);

        //Следующая операция в цепи
        $this->next($imageItem);

    }



}
