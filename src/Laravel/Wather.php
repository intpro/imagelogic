<?php

namespace Interpro\ImageFileLogic\Laravel;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class Wather
{
    private $pathResolver;
    private $x = 0;
    private $y = 0;
    private $position = 'center';
    private $water_path = 'images/features/water.png';
    private $active = false;

    public function __construct()
    {
        $this->pathResolver = App::make('Interpro\ImageFileLogic\Concept\PathResolver');

        $water = config('imagefilelogic.crop_water');

        $this->active = true;

        if(is_array($water))
        {
            if(array_key_exists('path', $water))
            {
                $this->water_path = public_path($water['path']);
            }

            $x=0;
            $y=0;

            if(array_key_exists('x', $water) and array_key_exists('y', $water))
            {
                $this->x = $water[$x];
                $this->y = $water[$y];
            }

            $this->position = 'center';

            if(array_key_exists('position', $water))
            {
                $this->position = $water['position'];
            }

            if(!File::exists(public_path($this->water_path)))
            {
                $this->active = false;
            }
        }
        else
        {
            $this->active = false;
        }

    }

    private function insertWater($half_path)
    {
        $wather = Image::make(public_path($this->water_path));

        foreach (glob($half_path.'*.*') as $file)
        {
            if(is_dir($file) || $file=='.' || $file=='..') continue;

            $source_img = Image::make($file);

            $source_img->insert($wather, $this->position, $this->x, $this->y);

            $source_img->save($file, 100);
        }
    }

    public function wather($image_prefix)
    {
        if($this->active)
        {
            $images_dir   = $this->pathResolver->getImageDir();

            $image_path = $images_dir.'/'.$image_prefix;

            $this->insertWater($image_path);

            $crop_dir   = $this->pathResolver->getImageCropDir();

            $crop_path = $crop_dir.'/'.$image_prefix;

            $this->insertWater($crop_path);
        }
    }
}
