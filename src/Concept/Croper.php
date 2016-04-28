<?php

namespace Interpro\ImageFileLogic\Concept;

interface Croper{

    /**
     * @param string $target_name
     * @param string $result_name
     * @param string $target_x1
     * @param string $target_y1
     * @param string $target_x2
     * @param string $target_y2
     *
     * @return void
     */
    public function crop($target_name, $result_name, $target_x1, $target_y1, $target_x2, $target_y2);

}
