<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;
use Interpro\ImageFileLogic\Concept\PathResolver;

class WaterImageAction extends ImageAction
{

    private $file_wm;
    private $position;
    private $_x;
    private $_y;
    private $pathResolver;

    /**
     * @param string $file_mask
     *
     * @param string $position
     *
     * @param string $_x
     *
     * @param string $_y
     *
     * @return void
     */
    public function __construct(
        $file_wm,
        $position,
        $_x,
        $_y
    )
    {
        $this->file_wm      = $file_wm;
        $this->position     = $position;
        $this->_x           = $_x;
        $this->_y           = $_y;
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');
    }

    public function applyFor(ImageItem $imageItem)
    {

        $features_dir = $this->pathResolver->getFeaturesDir();
        $images_dir   = $this->pathResolver->getImageDir();

        $image_path = $images_dir.'/'.$imageItem->getName();
        $mod_image_path = $images_dir.'/mod_'.$imageItem->getName();

        if(file_exists($mod_image_path))
        {
            $source_img = Image::make($mod_image_path);

        }elseif(file_exists($image_path))
        {
            $source_img = Image::make($image_path);
        }else
        {
            throw new ImageFileSystemException('Не найден файл картинки '.$image_path);
        }

        $filepath_mask = $features_dir .'/'. $this->file_wm;

        $mask = Image::make($filepath_mask);

        $source_img->insert($mask, $this->position, $this->_x, $this->_y);

        $source_img->save($mod_image_path, 100);


        Log::info('Добавлен водяной знак к картинке: ' . $image_path);

        $this->next($imageItem);

    }

}
