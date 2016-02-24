<?php

namespace Interpro\ImageFileLogic\Laravel;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\ImageConfig as ImageConfigInterface;
use Interpro\ImageFileLogic\Concept\Exception\ImageConfigException;

class ImageConfig implements ImageConfigInterface
{
    private function imageExcInform($message, $throw_exc)
    {
        if($throw_exc)
        {
            throw new ImageConfigException($message);
        }else{
            Log::info($message);
        }
    }

    public function checkConfig(&$image_config, $config_name, $throw_exc = true)
    {

        foreach($image_config as $key_1 => $val_1)
        {
            $res_canv = ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'];

            if($key_1 == 'sizes')
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
        $config = $this->getConfigAll();

        if(array_key_exists($config_name, $config))
        {
            $image_config = $config[$config_name];

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
        $config = $this->getConfigAll();

        return array_key_exists($config_name, $config);
    }

}
