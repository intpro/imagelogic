<?php

namespace Interpro\ImageFileLogic\Concept;

interface PathResolver{

    /**
     * @return string
     */
    public function getFeaturesDir();

    /**
     * @return string
     */
    public function getImageDir();

}
