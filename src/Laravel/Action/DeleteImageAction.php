<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;

class DeleteImageAction extends ImageAction
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

        $prefix = $imageItem->getNameWoExt();

        foreach (glob($images_dir.'/'.$prefix.'*.*') as $file) {

            if(is_dir($images_dir.'/'.$file) || $file=='.' || $file=='..') continue;

            unlink($file);

            Log::info('Удаление картинки: ' . $file);
        }

        //По идее следующего не должно быть
        $this->next($imageItem);

    }

}
