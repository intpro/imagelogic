<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\Action\ImageAction as ImageActionInterface;
use Interpro\ImageFileLogic\Concept\ActionChainFactory as ActionChainFactoryInterface;
use Interpro\ImageFileLogic\Concept\CropConfig;
use Interpro\ImageFileLogic\Concept\Exception\ImageConfigException;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\ImageConfig as ImageConfigInterface;
use Interpro\ImageFileLogic\Laravel\Action\CleanImageAction;
use Interpro\ImageFileLogic\Laravel\Action\CropImageAction;
use Interpro\ImageFileLogic\Laravel\Action\DeleteImageAction;
use Interpro\ImageFileLogic\Laravel\Action\MaskImageAction;
use Interpro\ImageFileLogic\Laravel\Action\ResizeImageAction;
use Interpro\ImageFileLogic\Laravel\Action\TotalWaterImageAction;
use Interpro\ImageFileLogic\Laravel\Action\WaterImageAction;

class ActionChainFactory implements ActionChainFactoryInterface
{
    private $imageConfig;
    private $cropConfig;

    public function __construct(ImageConfigInterface $imageConfig, CropConfig $cropConfig)
    {
        $this->imageConfig = $imageConfig;
        $this->cropConfig = $cropConfig;
    }

    /**
     * @param string $name
     *
     * @return ImageActionInterface
     */
    public function buildChain(ImageActionInterface $headAction, $name, $config_name)
    {
        $parentAction = $headAction;

        try{

            $config = $this->imageConfig->getConfig($config_name);
            $all_crops_config = $this->cropConfig->getConfig($config_name);

            if($name == 'clear')
            {
                $deleteAction = new DeleteImageAction();
                $parentAction->succeedWith($deleteAction);
                $parentAction = $deleteAction;
            }

            if($name == 'update' or $name == 'refresh')
            {
                $cleanAction = new CleanImageAction();
                $parentAction->succeedWith($cleanAction);
                $parentAction = $cleanAction;
            }

            if($name == 'update' or 'refresh')
            {
                if(array_key_exists('sizes', $config))
                {
                    foreach($config['sizes'] as $npp => $size_config)
                    {
                        $mod     = array_key_exists('mod', $size_config) ? $size_config['mod'] : false;
                        $absolve = array_key_exists('absolve', $size_config) ? $size_config['absolve'] : false;

                        $resizeAction = new ResizeImageAction($size_config['width'], $size_config['height'], $size_config['sufix'], $mod, $absolve);
                        $parentAction->succeedWith($resizeAction);
                        $parentAction = $resizeAction;
                    }
                }

                foreach($all_crops_config as $crop_name => $crop_config)
                {
                    $color = '#ffffff';

                    if(array_key_exists('color', $crop_config))
                    {
                        $color = $crop_config['color'];
                    }

                    $cropAction = new CropImageAction($crop_config['width'], $crop_config['height'], $crop_name, $crop_config['target'], $color);
                    $parentAction->succeedWith($cropAction);
                    $parentAction = $cropAction;
                }

                $wmAction = new TotalWaterImageAction();
                $parentAction->succeedWith($wmAction);
                $parentAction = $wmAction;

            }

        }catch (ImageConfigException $imconfexc){

            throw new ImageFileSystemException('Ошибка в конфигурации картинок: '.$imconfexc->getMessage());
        }

        return $parentAction;
    }

}
