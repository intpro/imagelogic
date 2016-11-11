<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Laravel\Wather;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;

class WaterImageAction extends ImageAction
{
    private $water;

    /**
     * @return void
     */
    public function __construct(

    )
    {
        $this->water = new Wather();
    }

    public function applyFor(ImageItem $imageItem)
    {

        $this->water->watherOriginal($imageItem);

        Log::info('Добавлен водяной знак к картинке оригиналу: ' . $imageItem->getNameWoExt());

        $this->next($imageItem);

    }

}
