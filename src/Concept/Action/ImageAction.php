<?php

namespace Interpro\ImageFileLogic\Concept\Action;

use Interpro\ImageFileLogic\Concept\Item\ImageItem;

abstract class ImageAction
{
    protected $successor;

    abstract public function applyFor(ImageItem $imageItem);

    public function succeedWith(ImageAction $successor)
    {
        $this->successor = $successor;
    }

    public function next(ImageItem $imageItem)
    {
        if($this->successor)
        {
            $this->successor->applyFor($imageItem);
        }

    }
}
