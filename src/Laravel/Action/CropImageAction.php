<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;
use Intervention\Image\Facades\Image;

class CropImageAction extends ImageAction
{
    private $width;
    private $height;
    private $sufix;
    private $target;
    private $color;
    private $pathResolver;
    private $imageConfig;
    private $report;

    /**
     * @param string $width
     *
     * @param string $height
     *
     * @param string $sufix
     *
     * @param string $target_resize
     *
     * @return void
     */
    public function __construct(
        $width,
        $height,
        $sufix,
        $target_resize,
        $color = '#ffffff'
    )
    {
        $this->width        = $width;
        $this->height       = $height;
        $this->sufix        = $sufix;
        $this->target       = $target_resize;
        $this->color        = $color;
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');
        $this->report       = App::make('Interpro\ImageFileLogic\Concept\Report');
        $this->imageConfig  = App::make('Interpro\ImageFileLogic\Concept\ImageConfig');
    }

    public function applyFor(ImageItem $imageItem)
    {
        $images_dir = $this->pathResolver->getImageDir();
        $crop_dir = $this->pathResolver->getImageCropDir();

        $image_path = $images_dir.'/'.$imageItem->getName();

        if(file_exists($image_path))
        {
            $source_img = Image::make($image_path);
        }else
        {
            throw new ImageFileSystemException('Не найден файл картинки '.$image_path);
        }

        $crop_name = $imageItem->getNameWoExt().'_'.$this->sufix.'.'.$imageItem->getExt();

        $crop_path = $crop_dir.'/'.$crop_name;



        $resizes = $this->imageConfig->getConfig($imageItem->getConfigName());

        $find = false;

        $resize_config = null;

        foreach($resizes['sizes'] as $resize)
        {
            if($resize['sufix'] === $this->target)
            {
                $find = true;
                $resize_config = $resize;
            }
        }

        if(!$find)
        {
            throw new ImageFileSystemException('Не найден ресайз в конфигурации по имени '.$this->target.' для картинки '.$imageItem->getConfigName());
        }

        $absolve = array_key_exists('absolve', $resize_config) ? $resize_config['absolve'] : false;

        $img = Image::make($source_img)->resize($resize_config['width'], $resize_config['height'],
            function ($constraint) use ($absolve) {
                if(!$absolve){
                    $constraint->aspectRatio();
                }
            });

        $target_width = $img->getWidth();
        $target_height = $img->getHeight();

        $target_x1 = $target_width/2 - $this->width/2;
        $target_y1 = $target_height/2 - $this->height/2;

        if($target_width < $this->width)
        {
            $target_x1 = 0;
        }

        if($target_height < $this->height)
        {
            $target_y1 = 0;
        }

        $canvas_width = $this->width;
        $canvas_height = $this->height;

        $real_crop_width = min($target_width, $this->width);
        $real_crop_height = min($target_height, $this->height);

        $img->crop($real_crop_width, $real_crop_height, $target_x1, $target_y1);

        Image::canvas($canvas_width, $canvas_height, $this->color)->insert($img, 'center')->save($crop_path, 100);


        chmod($crop_path, 0777);

        Log::info('Выполнен кроп картинки: ' . $crop_path);

        $this->report->setImageCrop($imageItem->getNameWoExt(), $this->sufix, $crop_name);

        $this->next($imageItem);

    }

}
