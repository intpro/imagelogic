<?php

namespace Interpro\ImageFileLogic\Concept;

interface ImageLogicAgent
{
    /**
     * @param string $image_name
     * @param string $sufix
     *
     * @return array
     */
    public function getPlaceholder($image_name, $sufix);

}
