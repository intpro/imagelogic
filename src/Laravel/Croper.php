<?php

namespace Interpro\ImageFileLogic\Laravel;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\Croper as CroperInterface;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\PathResolver as PathResolverInterface;
use Intervention\Image\Facades\Image;

class Croper implements CroperInterface
{

    private $pathResolver;

    public function __construct(PathResolverInterface $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    /**
     * @param string $target_name
     * @param string $result_name
     * @param string $target_x1
     * @param string $target_y1
     * @param string $target_x2
     * @param string $target_y2
     * @param string $bg_color
     *
     * @return void
     */
    public function crop($target_name, $result_name, $target_x1, $target_y1, $target_x2, $target_y2, $bg_color = '#ffffff')
    {
        $image_dir = $this->pathResolver->getImageDir();
        $crop_dir = $this->pathResolver->getImageCropDir();

        $target_path = $image_dir.'/'.$target_name;
        $path_ext = 'jpg';
        $target_exist = false;

        foreach (glob($target_path.'*.*') as $file)
        {
            $inf = pathinfo($file);
            $path_ext = $inf['extension'];
            $target_exist = true;
        }

        if(!$target_exist)
        {
            Log::info('Нет файла по пути (с любым расширением) :'.$target_path);
            return;
        }

        $target_path = $target_path.'.'.$path_ext;
        $result_path = $crop_dir.'/'.$result_name.'.'.$path_ext;

        $img = Image::make($target_path);

        //Нехороший подсчет кропа, делаем инкастыляцию (наводимся по центру заново):

        $width = $target_x2-$target_x1;
        $height = $target_y2-$target_y1;

        $target_width = $img->getWidth();
        $target_height = $img->getHeight();

        if($target_width < $width)
        {
            $target_x1 = 0;
        }

        if($target_height < $height)
        {
            $target_y1 = 0;
        }

        $canvas_width = $width;
        $canvas_height = $height;

        $real_crop_width = min($img->getWidth(), $width);
        $real_crop_height = min($img->getHeight(), $height);

        $img->crop($real_crop_width, $real_crop_height, $target_x1, $target_y1);

        //$img->save($result_path, 100);

        Image::canvas($canvas_width, $canvas_height, $bg_color)->insert($img, 'center')->save($result_path, 100);

        chmod($result_path, 0777);

        Log::info('Кроп от '.$target_path.': ' . $result_path);

    }

}
