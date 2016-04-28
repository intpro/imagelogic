<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\PathResolver as PathResolverInterface;

class PathResolver implements PathResolverInterface
{
    /**
     * @return string
     */
    public function getFeaturesDir()
    {
        return public_path() . '/' . config('imagefilelogic.features_dir');
    }

    /**
     * @return string
     */
    public function getImageDir()
    {
        return public_path() . '/' . config('imagefilelogic.image_dir');
    }

    /**
     * @return string
     */
    public function getImageCropDir()
    {
        return public_path() . '/' . config('imagefilelogic.crop_dir');
    }

}
