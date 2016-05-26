<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\Action\ImageAction as ImageActionInterface;
use Interpro\ImageFileLogic\Concept\ActionChainFactory as ActionChainFactoryInterface;
use Interpro\ImageFileLogic\Concept\CropConfig;
use Interpro\ImageFileLogic\Concept\Exception\ImageConfigException;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\ImageConfig as ImageConfigInterface;
use Interpro\ImageFileLogic\Laravel\Action\CleanImageAction;
use Interpro\ImageFileLogic\Laravel\Action\DeleteImageAction;
use Interpro\ImageFileLogic\Laravel\Action\MaskImageAction;
use Interpro\ImageFileLogic\Laravel\Action\ResizeImageAction;
use Interpro\ImageFileLogic\Laravel\Action\WaterImageAction;

class ActionChainFactory implements ActionChainFactoryInterface
{
    private $imageConfig;

    public function __construct(ImageConfigInterface $imageConfig)
    {
        $this->imageConfig = $imageConfig;
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
                if(array_key_exists('mask', $config))
                {
                    $mask_config = & $config['mask'];

                    $_x = array_key_exists('x', $mask_config) ? $mask_config['x'] : null;
                    $_y = array_key_exists('y', $mask_config) ? $mask_config['y'] : null;

                    $maskAction = new MaskImageAction($mask_config['file'], $mask_config['position'], $_x, $_y);
                    $parentAction->succeedWith($maskAction);
                    $parentAction = $maskAction;
                }

                if(array_key_exists('water', $config))
                {
                    $water_config = & $config['water'];

                    $_x = array_key_exists('x', $water_config) ? $water_config['x'] : null;
                    $_y = array_key_exists('y', $water_config) ? $water_config['y'] : null;

                    $waterAction = new WaterImageAction($water_config['file'], $water_config['position'], $_x, $_y);
                    $parentAction->succeedWith($waterAction);
                    $parentAction = $waterAction;
                }

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
            }

        }catch (ImageConfigException $imconfexc){

            throw new ImageFileSystemException('Ошибка в конфигурации картинок: '.$imconfexc->getMessage());
        }

        return $parentAction;
    }

}
