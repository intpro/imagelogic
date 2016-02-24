<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;

class ExistImageAction extends ImageAction
{
    private $pathResolver;

    /**
     *
     *
     * @return void
     */
    public function __construct()
    {
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');
    }

    public function applyFor(ImageItem $imageItem)
    {
        $images_dir   = $this->pathResolver->getImageDir();

        $image_path = $images_dir.'/'.$imageItem->getNameWoExt();

        $finded = false;

        foreach (glob($image_path.'*.*') as $file)
        {
            $inf = pathinfo($file);
            $imageItem->setExt($inf['extension']);

            Log::info('Существует картинка: ' . $file);

            $finded = true;
        }

        if(!$finded)
        {
            throw new ImageFileSystemException('Нет файла по пути с любым расширением '.$image_path);
        }

        $this->next($imageItem);

    }

}
