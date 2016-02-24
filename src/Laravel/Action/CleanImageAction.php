<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;

class CleanImageAction extends ImageAction
{
    private $pathResolver;

    /**
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

        $image_path = $images_dir.'/'.$imageItem->getName();

        if (!file_exists($image_path))
        {
            throw new ImageFileSystemException('Не найден файл картинки '.$image_path);
        }

        $prefix = $imageItem->getNameWoExt();

        foreach (glob($images_dir.'/'.$prefix.'*.*') as $file) {

            if(is_dir($file) || $file=='.' || $file=='..' || $file==$image_path) continue;

            unlink($file);

            Log::info('Удаление картинки: ' . $file);
        }

        $this->next($imageItem);


    }

}
