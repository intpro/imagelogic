<?php

namespace Interpro\ImageFileLogic\Laravel;

use Illuminate\Support\Facades\Log;
use Interpro\ImageFileLogic\Concept\CropConfig as CropConfigInterface;
use Interpro\ImageFileLogic\Concept\Exception\CropConfigException;

class CropConfig implements CropConfigInterface
{
    private $config;


    public function __construct()
    {
        $this->config = $this->getConfigAll();
    }

    private function cropExcInform($message, $log = false)
    {
        if($log)
        {
            Log::info($message);
        }
        throw new CropConfigException($message);
    }

    public function checkConfig(&$crop_config, $config_name)
    {
        foreach($crop_config as $crop_name => $val_1)
        {
            $t_is_width = false;
            $t_is_height = false;
            $t_is_man = false;
            $t_is_target = false;

            $t_is_color = true;

            foreach($val_1 as $key_2 => $val_2)
            {
                if($key_2 === 'width')
                {
                    if(is_int($val_2) or is_null($val_2))
                    {
                        $t_is_width = true;
                    }else{
                        $this->cropExcInform('Ширина (width) не задана целым числом ('.$config_name.'): crop '.$crop_name);
                    }
                }
                elseif($key_2 === 'height' or is_null($val_2))
                {
                    if(is_int($val_2) or is_null($val_2))
                    {
                        $t_is_height = true;
                    }else{
                        $this->cropExcInform('Высота (height) не задана целым числом ('.$config_name.'): crop '.$crop_name);
                    }
                }
                elseif($key_2 === 'man' or is_null($val_2))
                {
                    if(is_string($val_2))
                    {
                        $t_is_man = true;
                    }else{
                        $this->cropExcInform('Образец для кропа (man) должен быть строкой ('.$config_name.'): crop '.$crop_name);
                    }
                }
                elseif($key_2 === 'target' or is_null($val_2))
                {
                    if(is_string($val_2))
                    {
                        $t_is_target = true;
                    }else{
                        $this->cropExcInform('Цель для кропа (target) должна быть строкой ('.$config_name.'): crop '.$crop_name);
                    }
                }
                elseif($key_2 === 'color')
                {
                    if(!$this->validateColor($val_2))
                    {
                        $t_is_color = false;
                    }
                }
            }

            if(!$t_is_width)
            {
                $this->cropExcInform('Ширина (width) должна быть указана ('.$config_name.'): crop '.$crop_name);
            }

            if(!$t_is_height)
            {
                $this->cropExcInform('Высота (height) должна быть указана ('.$config_name.'): crop '.$crop_name);
            }

            if(!$t_is_man)
            {
                $this->cropExcInform('Образец для кропа (man) должен быть указан ('.$config_name.'): crop '.$crop_name);
            }

            if(!$t_is_target)
            {
                $this->cropExcInform('Цель для кропа (target) должна быть указана ('.$config_name.'): crop '.$crop_name);
            }

            if(!$t_is_color)
            {
                $this->cropExcInform('Формат строки цвета неправильный (hex) ('.$config_name.'): crop '.$crop_name);
            }

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

    public function getConfigAll()
    {
        $config = config('crop');

        if(!$config)
        {
            $config = [];
        }

        return $config;
    }

    /**
     * @param string $image_name
     *
     * @return array
     */
    public function getConfig($image_name)
    {
        if(array_key_exists($image_name, $this->config))
        {
            $image_config = $this->config[$image_name];

            $this->checkConfig($image_config, $image_name);

        }else{

            return [];
        }

        return $image_config;
    }

    /**
     * @param string $config_name
     *
     * @return bool
     */
    public function configExist($image_name)
    {
        return array_key_exists($image_name, $this->config);
    }

    /**
     * @param string $config_name
     *
     * @param string $crop_name
     */
    public function getWidth($image_name, $crop_name)
    {
        return $this->getField($image_name, $crop_name, 'width', 'ширина');
    }

    /**
     * @param string $config_name
     *
     * @param string $crop_name
     */
    public function getHeight($image_name, $crop_name)
    {
        return $this->getField($image_name, $crop_name, 'height', 'ширина');
    }

    /**
     * @param string $config_name
     *
     * @param string $crop_name
     */
    public function getMan($image_name, $crop_name)
    {
        return $this->getField($image_name, $crop_name, 'man', 'образец для кропа');
    }

    /**
     * @param string $config_name
     *
     * @param string $crop_name
     */
    public function getTarget($image_name, $crop_name)
    {
        return $this->getField($image_name, $crop_name, 'target', 'цель для кропа');
    }

    /**
     * @param string $image_name
     *
     * @param string $crop_name
     *
     * @return string
     */
    public function getColor($image_name, $crop_name)
    {
        $conf = $this->getConfig($image_name);

        if(array_key_exists($crop_name, $conf))
        {
            if(array_key_exists('color', $conf[$crop_name]))
            {
                return $conf[$crop_name]['color'];
            }
        }

        return '#ffffff';
    }

    /**
     * @param string $config_name
     *
     * @param string $crop_name
     *
     * @param string $field_name
     *
     * @param string $field_syn
     *
     */
    private function getField($image_name, $crop_name, $field_name, $field_syn)
    {
        $conf = $this->getConfig($image_name);

        if(array_key_exists($crop_name, $conf))
        {
            if(array_key_exists($field_name, $conf[$crop_name]))
            {
                return $conf[$crop_name][$field_name];
            }
        }

        $this->cropExcInform('Отсутствует '.$field_syn.' ('.$field_name.') в настройке кропа '.$crop_name.' картинки '.$image_name);
    }



}
