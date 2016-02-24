<?php

namespace Interpro\ImageFileLogic;

use URL;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class ImageFileLogic {

    public static function getMkFileMode(){
        return 0777;
    }

    public static function getConfig($variant){

        $config = config('resize.'.$variant);
        if (!$config){
            $config = ['sizes'=>
                [
                    ['width'=>75, 'height'=>75, 'sufix'=>'preview']
                ],
                'preview_sufix' => 'preview'
            ];
        }

        return $config;
    }

    public static function createResized($mod, $filename, $width, $height, $sufix) {

        if ($filename) {

            $mkfile_mode = static::getMkFileMode();

            $filepath = public_path() . '/images/' . ($mod ? 'mod_' . $filename : $filename);

            $inf = pathinfo($filepath);
            $newfilename = $inf['filename'] . '_' .$sufix . '.' . $inf['extension'];
            $newfilepath = public_path() . '/images/' . $newfilename;

            if (file_exists($filepath)) {

                $img = Image::make($filepath)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $img->save($newfilepath, 100);

                chmod($newfilepath, $mkfile_mode);

                Log::info('Создана картинка: ' . $newfilepath);

                return $newfilename;
            }
        }
    }

    public static function removeForPrefix($prefix) {

        $dir = public_path() . '/images';

        Log::info('Удаление по префиксу: ' . $prefix);

        foreach (glob($dir.'/'.$prefix.'*.*') as $file) {

            if(is_dir($dir.'/'.$file) || $file=='.' || $file=='..') continue;

            unlink($file);

            Log::info('Удаление картинки: ' . $file);
        }

    }

    public function storeImage($entity, $id, $image, $include_filename=true) {

        $baseurl = URL::to('/');

        $prefix = $entity.'_'.$id;
        $prefix = $entity.'_'.$id;

        $mkfile_mode = static::getMkFileMode();

        $resp_arr = array();

        $dir = public_path() . '/images';

        $imagename = $image->getClientOriginalName();
        $imagesize = $image->getSize();

        if($include_filename){
            $newimagename = $prefix.'_'.$imagename;
        }else{
            $extension = $image->getClientOriginalExtension();
            $newimagename = $prefix.'.'.$extension;
        }

        $image_path = $dir.'/'.$newimagename;

        $uploadflag = $image->move($dir, $newimagename);

        if ($uploadflag) {

            Log::info('Загружена картинка: ' . $image_path);

            $result = [];

            $config = static::getConfig($entity);

            //Наложение маски и водяного знака на загружаемое изображение
            try {
                $filepath_features = public_path() . '/images/features/';
                $mod_image_path = $dir.'/mod_'.$newimagename;

                $plus_mask = array_key_exists('mask', $config);
                $plus_water = array_key_exists('water', $config);

                if($plus_mask)
                {
                    $filepath_mask = $filepath_features . $config['mask']['file'];

                    $masked_img = Image::make($image_path);

                    $mask = Image::make($filepath_mask);

                    $x_coord = array_key_exists('x', $config['mask']) ? $config['mask']['x'] : null;
                    $y_coord = array_key_exists('y', $config['mask']) ? $config['mask']['y'] : null;

                    $masked_img->insert($mask, $config['mask']['position'], $x_coord, $y_coord);

                    $masked_img->save($mod_image_path, 100);
                }

                if($plus_water)
                {
                    $filepath_water = $filepath_features . $config['water']['file'];

                    $watered_img = Image::make($plus_mask ? $mod_image_path : $image_path);

                    $water = Image::make($filepath_water);

                    $x_coord = array_key_exists('x', $config['water']) ? $config['water']['x'] : null;
                    $y_coord = array_key_exists('y', $config['water']) ? $config['water']['y'] : null;

                    $watered_img->insert($water, $config['water']['position'], $x_coord, $y_coord);

                    $watered_img->save($mod_image_path, 100);
                }

            } catch(Exception $exception) {
                Log::warning('Maybe no mask or water image.');
            }
            //-----------------------------------------------------------

            $chmodyes = false;

            $chmodyes = chmod($image_path, $mkfile_mode);

            $inf = pathinfo($image_path);

            //Создание файлов в папке resized
            $sizes = $config['sizes'];

            $result['original'] = $inf['filename'].'.'.$inf['extension'];

            foreach($sizes as $size) {

                $mod = array_key_exists('mod', $size) ? $size['mod'] : false;

                $result[$size['sufix']] = static::createResized(
                    $mod,
                    $newimagename,
                    $size['width'],
                    $size['height'],
                    $size['sufix']
                );
            }

            $resp_arr['status'] = 'OK';
            $resp_arr['prefix'] = $prefix;
            $resp_arr['sizes']  = $result;

        } else {

            $resp_arr['status'] = 'Ошибка в правах доступа к дирректории картинок при записи!';

        }

        return $resp_arr;
    }


}

