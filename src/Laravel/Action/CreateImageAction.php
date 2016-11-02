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
    private $imageConfig;

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
        $this->imageConfig  = App::make('Interpro\ImageFileLogic\Concept\ImageConfig');
    }

    private function WOId($image_name)
    {
        $pos = strrpos($image_name, '_');

        return substr($image_name, 0, $pos);
    }

    public function applyFor(ImageItem $imageItem)
    {
        $images_dir = $this->pathResolver->getImageDir();

        $temp_name      = $this->req_file->getClientOriginalName();;
        $ext            = $this->req_file->getClientOriginalExtension();

        $imageItem->setExt($ext);

        $new_image_name    = $imageItem->getName();

        $this->req_file->move(
            $images_dir,
            $new_image_name
        );

//        if(!$uploadflag)
//        {
//            throw new ImageFileSystemException('Не удалось переписать временный файл '.$temp_name.' по новому пути '.$new_image_name);
//        }

        $image_path = $images_dir.'/'.$new_image_name;

        $image_config_name = $this->WOId($new_image_name);

        $proportion = $this->imageConfig->getProportions($image_config_name);

        if($proportion['transform'])
        {
            //Делаем картинку пропорциональной
            $img = Image::make($image_path);

            $width = $img->getWidth();
            $height = $img->getHeight();

            if(($width/$height) > $proportion['width']/$proportion['height'])
            {
                $new_width = $width;
                $new_height = (int) ($width * ($proportion['height']/$proportion['width']));
            }
            else
            {
                $new_width = (int) ($height * ($proportion['width']/$proportion['height']));
                $new_height = $height;
            }

            Image::canvas($new_width, $new_height, $proportion['color'])->insert($img, 'center')->save($image_path, 100);
        }

        Log::info('Загружена картинка: ' . $image_path);

        $this->report->setImageResize($imageItem->getNameWoExt(), 'original', $new_image_name);

        //Следующая операция в цепи
        $this->next($imageItem);

    }



}
