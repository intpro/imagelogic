<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;
use Intervention\Image\Facades\Image;

class ResizeImageAction extends ImageAction
{
    private $width;
    private $height;
    private $sufix;
    private $mod;
    private $absolve;
    private $pathResolver;
    private $report;

    /**
     * @param string $width
     *
     * @param string $height
     *
     * @param string $sufix
     *
     * @param string $mod
     *
     * @return void
     */
    public function __construct(
        $width,
        $height,
        $sufix,
        $mod,
        $absolve = false
    )
    {
        $this->width        = $width;
        $this->height       = $height;
        $this->sufix        = $sufix;
        $this->mod          = $mod;
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');
        $this->report       = App::make('Interpro\ImageFileLogic\Concept\Report');
    }

    public function applyFor(ImageItem $imageItem)
    {

        $images_dir = $this->pathResolver->getImageDir();

        $image_path = $images_dir.'/'.$imageItem->getName();
        $mod_image_path = $images_dir.'/mod_'.$imageItem->getName();

        if($this->mod)
        {
            if(file_exists($mod_image_path))
            {
                $source_img = Image::make($mod_image_path);
            }else
            {
                throw new ImageFileSystemException('Не найден файл картинки '.$mod_image_path);
            }
        }else{
            if(file_exists($image_path))
            {
                $source_img = Image::make($image_path);
            }else
            {
                throw new ImageFileSystemException('Не найден файл картинки '.$image_path);
            }
        }

        $resized_name = $imageItem->getNameWoExt().'_'.$this->sufix.'.'.$imageItem->getExt();

        $resized_path = $images_dir.'/'.$resized_name;

        $absolve = $this->absolve;

        $img = Image::make($source_img)->resize($this->width, $this->height,
            function ($constraint) use ($absolve) {
            if(!$absolve){
                $constraint->aspectRatio();
            }
        });

        $img->save($resized_path, 100);

        chmod($resized_path, 0777);

        Log::info('Добавлена маска к картинке: ' . $image_path);

        $this->report->setImageResize($imageItem->getNameWoExt(), $this->sufix, $resized_name);

        $this->next($imageItem);

    }

}
