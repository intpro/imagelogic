<?php

namespace Interpro\ImageFileLogic\Laravel\Action;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Laravel\Wather;
use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\Item\ImageItem;

class TotalWaterImageAction extends ImageAction
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
        $this->water->wather($imageItem->getConfigName());

        Log::info('Добавлен водяной знак к картинке: ' . $imageItem->getConfigName());

        $this->next($imageItem);
    }

}
