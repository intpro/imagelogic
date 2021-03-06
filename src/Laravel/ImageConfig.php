<?php

namespace Interpro\ImageFileLogic\Laravel;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\ImageConfig as ImageConfigInterface;
use Interpro\ImageFileLogic\Concept\Exception\ImageConfigException;

class ImageConfig implements ImageConfigInterface
{

    private $config;


    public function __construct()
    {
        $this->config = $this->getConfigAll();
    }

    private function imageExcInform($message, $throw_exc)
    {
        if($throw_exc)
        {
            throw new ImageConfigException($message);
        }else{
            Log::info($message);
        }
    }

    /**
     * @param string $color
     * @return bool
     */
    private function validateColor($color)
    {
        preg_match('/(#[a-f0-9]{3}([a-f0-9]{3})?)/i', $color, $matches);
        if (isset($matches[1]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkConfig(&$image_config, $config_name, $throw_exc = true)
    {

        foreach($image_config as $key_1 => $val_1)
        {
            $res_canv = ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'];

            if($key_1 == 'proportions')
            {
                if(is_array($val_1))
                {
                    foreach($val_1 as $pp_key => $pp_value)
                    {
                        if($pp_key === 'width' or $pp_key === 'height')
                        {
                            if(!is_int($pp_value))
                            {
                                $this->imageExcInform('Размеры пропорций (width, height) не заданы целым числовым типом ('.$config_name.'): '.$pp_key, $throw_exc);
                            }
                        }elseif($pp_key === 'color'){

                            if(!(is_string($pp_value) and $this->validateColor($pp_value)))
                            {
                                $this->imageExcInform('Формат строки цвета неправильный (hex) ('.$config_name.'): crop '.$pp_value, $throw_exc);
                            }
                        }
                    }
                }else{
                    $this->imageExcInform('Раздел proportions должен быть массивом('.$config_name.'): '.$key_1, $throw_exc);
                }
            }
            elseif($key_1 == 'sizes')
            {
                if(is_array($val_1))
                {
                    foreach($val_1 as $resize_numb => $size_conf)
                    {
                        if(is_array($size_conf))
                        {
                            $t_is_width = false;
                            $t_is_height = false;
                            $t_is_sufix = false;

                            foreach($size_conf as $key_2 => $val_2)
                            {
                                if($key_2 =='width')
                                {
                                    if(is_int($val_2) or is_null($val_2))
                                    {
                                        $t_is_width = true;
                                    }else{
                                        $this->imageExcInform('Ширина (width) не задана целым числом ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }
                                elseif($key_2 == 'height' or is_null($val_2))
                                {
                                    if(is_int($val_2) or is_null($val_2))
                                    {
                                        $t_is_height = true;
                                    }else{
                                        $this->imageExcInform('Высота (height) не задана целым числом ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }
                                elseif($key_2 == 'sufix')
                                {
                                    if(is_string($val_2))
                                    {
                                        $t_is_sufix = true;
                                    }else{
                                        $this->imageExcInform('Суффикс (sufix) должен быть строкой ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }
                                elseif($key_2 == 'mod')
                                {
                                    if(!is_bool($val_2))
                                    {
                                        $this->imageExcInform('Признак (mod) не задан булевым типом ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }
                                elseif($key_2 == 'absolve')
                                {
                                    if(!is_bool($val_2))
                                    {
                                        $this->imageExcInform('Признак (absolve) не задан булевым типом ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }elseif($key_2 == 'color')
                                {
                                    if(is_string($val_2))
                                    {
                                        $t_is_sufix = true;
                                    }else{
                                        $this->imageExcInform('Цвет (color) должен быть строкой ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                    }
                                }else{
                                    $this->imageExcInform('Неизвестный идентификатор в настройке ресайза изображений ('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                                }
                            }

                            if(!($t_is_width and $t_is_height and $t_is_sufix))
                            {
                                $this->imageExcInform('Ширина, высота и суффикс (width, height, sufix) должны быть в настройке ресайза обязательно('.$config_name.'): resize №'.$resize_numb, $throw_exc);
                            }

                        }else{
                            $this->imageExcInform('Что-то вместо массива в разделе sizes ('.$config_name.'): '.$key_1, $throw_exc);
                        }
                    }
                }else{
                    $this->imageExcInform('Раздел sizes должен быть массивом('.$config_name.'): '.$key_1, $throw_exc);
                }

            }elseif($key_1 == 'mask'){

                if(is_array($val_1))
                {
                    $t_is_file = false;
                    $t_is_position = false;
                    $t_is_x = false;
                    $t_is_y = false;

                    foreach($val_1 as $key_2 => $val_2)
                    {
                        if($key_2 =='file')
                        {
                            if(is_string($val_2))
                            {
                                $t_is_file = true;
                            }else{
                                $this->imageExcInform('Файл в блоке mask должен быть задан строкой ('.$config_name.'): '.$key_1, $throw_exc);
                            }
                        }
                        elseif($key_2 == 'position')
                        {
                            if(is_string($val_2) and in_array($val_2, $res_canv))
                            {
                                $t_is_position = true;
                            }else{
                                $this->imageExcInform('Позиция в блоке mask должен быть задан строкой одним из значений (top-left, top, top-right, left, center, right, bottom-left, bottom, bottom-right) ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }elseif($key_2 == 'x')
                        {
                            if(is_int($val_2))
                            {
                                $t_is_x = true;
                            }else{
                                $this->imageExcInform('Координата X должна быть целым числом ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }elseif($key_2 == 'y')
                        {
                            if(is_int($val_2))
                            {
                                $t_is_y = true;
                            }else{
                                $this->imageExcInform('Координата Y должна быть целым числом ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }else{
                            $this->imageExcInform('Неизвестный идентификатор в настройке mask ('.$config_name.'): '.$key_1, $throw_exc);
                        }
                    }

                    if(!($t_is_file and $t_is_position))
                    {
                        $this->imageExcInform('Файл и позиция (file, position) должны быть в настройке маски обязательно('.$config_name.'): '.$key_1, $throw_exc);
                    }

                    if($t_is_x xor $t_is_y)
                    {
                        $this->imageExcInform('Обе координаты должны быть заполнены или ни одной ('.$config_name.'): '.$key_1, $throw_exc);
                    }


                }else{
                    $this->imageExcInform('Раздел mask должен быть массивом('.$config_name.'): '.$key_1, $throw_exc);
                }

            }elseif($key_1 == 'water'){

                if(is_array($val_1))
                {

                    $t_is_file = false;
                    $t_is_position = false;
                    $t_is_x = false;
                    $t_is_y = false;

                    foreach($val_1 as $key_2 => $val_2)
                    {
                        if($key_2 =='file')
                        {
                            if(is_string($val_2))
                            {
                                $t_is_file = true;
                            }else{
                                $this->imageExcInform('Файл в блоке water должен быть задан строкой ('.$config_name.'): '.$key_1, $throw_exc);
                            }
                        }
                        elseif($key_2 == 'position')
                        {
                            if(is_string($val_2) and in_array($val_2, $res_canv))
                            {
                                $t_is_position = true;
                            }else{
                                $this->imageExcInform('Позиция в блоке water должен быть задан строкой одним из значений (top-left, top, top-right, left, center, right, bottom-left, bottom, bottom-right) ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }elseif($key_2 == 'x')
                        {
                            if(is_int($val_2))
                            {
                                $t_is_x = true;
                            }else{
                                $this->imageExcInform('Координата X должна быть целым числом ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }elseif($key_2 == 'y')
                        {
                            if(is_int($val_2))
                            {
                                $t_is_y = true;
                            }else{
                                $this->imageExcInform('Координата Y должна быть целым числом ('.$config_name.'): '.$key_1, $throw_exc);
                            }

                        }else{
                            $this->imageExcInform('Неизвестный идентификатор в настройке water ('.$config_name.'): '.$key_1, $throw_exc);
                        }
                    }

                    if(!($t_is_file and $t_is_position))
                    {
                        $this->imageExcInform('Файл и позиция (file, position) должны быть в настройке маски обязательно('.$config_name.'): '.$key_1, $throw_exc);
                    }

                    if($t_is_x xor $t_is_y)
                    {
                        $this->imageExcInform('Обе координаты должны быть заполнены или ни одной ('.$config_name.'): '.$key_1, $throw_exc);
                    }


                }else{
                    $this->imageExcInform('Раздел water должен быть массивом('.$config_name.'): '.$key_1, $throw_exc);
                }

            }else{
                $this->imageExcInform('Неизвестный идентификатор в настройке изображений ('.$config_name.'): '.$key_1, $throw_exc);
            }
        }
    }

    private function getConfigAll()
    {
        try
        {
            return config('resize');
        }catch (\Exception $e){

            throw new ImageConfigException('Отсутствует настройка изображений.');
        }
    }

    /**
     * @param string $config_name
     *
     * @return array
     */
    public function getConfig($config_name)
    {
        if(array_key_exists($config_name, $this->config))
        {
            $image_config = $this->config[$config_name];

            $this->checkConfig($image_config, $config_name);

        }else{

            throw new ImageConfigException('Имя настройки изображения не найдено в конфигурации.');
        }

        return $image_config;
    }

    /**
     * @param string $config_name
     *
     * @return bool
     */
    public function configExist($config_name)
    {
        return array_key_exists($config_name, $this->config);
    }

    /**
     * @param string $config_name
     *
     * @return array
     */
    public function getProportions($image_name)
    {
        $conf = $this->getConfig($image_name);

        $pp = ['transform' => true];

        if(array_key_exists('proportions', $conf))
        {
            $pp_conf = $conf['proportions'];

            $width_exist = array_key_exists('width', $pp_conf);
            $height_exist = array_key_exists('height', $pp_conf);

            if($width_exist and $height_exist)
            {
                $pp['width'] = $pp_conf['width'];
                $pp['height'] = $pp_conf['height'];
            }
            elseif($width_exist and !$height_exist)
            {
                $pp['width'] = $pp_conf['width'];
                $pp['height'] = $pp_conf['width'];
            }
            elseif(!$width_exist and $height_exist)
            {
                $pp['width'] = $pp_conf['height'];
                $pp['height'] = $pp_conf['height'];
            }
            elseif(!$width_exist and !$height_exist)
            {
                $pp['width'] = 1;
                $pp['height'] = 1;
                $pp['transform'] = false;
            }

            if(array_key_exists('color', $pp_conf))
            {
                $pp['color'] = $pp_conf['color'];
            }
            else
            {
                $pp['color'] = '#ffffff';
            }
        }
        else
        {
            $pp['width'] = 1;
            $pp['height'] = 1;
            $pp['color'] = '#ffffff';
            $pp['transform'] = false;
        }

        return $pp;
    }

    /**
     * @param string $config_name
     *
     * @param string $sufix
     */
    public function getWidth($image_name, $sufix)
    {
        $conf = $this->getConfig($image_name);

        if(array_key_exists('sizes', $conf))
        {
            foreach($conf['sizes'] as $size)
            {
                if($size['sufix'] == $sufix)
                {
                    if($size['width'])
                    {

                        return $size['width'];
                    }elseif($size['height']){

                        return $size['height'];
                    }else{

                        return 200;
                    }
                }

            }

        }

        return 200;
    }

    /**
     * @param string $config_name
     *
     * @param string $sufix
     */
    public function getHeight($image_name, $sufix)
    {
        $conf = $this->getConfig($image_name);

        if(array_key_exists('sizes', $conf))
        {
            foreach($conf['sizes'] as $size)
            {
                if($size['sufix'] == $sufix)
                {
                    if($size['height'])
                    {

                        return $size['height'];
                    }elseif($size['width']){

                        return $size['width'];
                    }else{

                        return 200;
                    }
                }

            }

        }

        return 200;
    }

    /**
     * @param string $config_name
     *
     * @param string $sufix
     */
    public function getColor($image_name, $sufix)
    {
        $conf = $this->getConfig($image_name);

        if(array_key_exists('sizes', $conf))
        {
            foreach($conf['sizes'] as $size)
            {
                if($size['sufix'] == $sufix)
                {
                    if(array_key_exists('color', $size))
                    {
                        if($size['color'])
                        {

                            return $size['color'];
                        }else{

                            return '#808080';
                        }
                    }
                }

            }

        }
        return '#808080';
    }

}
