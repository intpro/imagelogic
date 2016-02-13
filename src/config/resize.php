<?php

return [

    'someblock_firstgroup_somepict' =>[
        'sizes' => [['width'=>600, 'height'=>null, 'sufix'=>'primary', 'mod'=>true], ['width'=>240, 'height'=>null, 'sufix'=>'secondary'], ['width'=>60, 'height'=>null, 'sufix'=>'preview']],
        'mask' => ['file'=>'mask.png', 'position'=>'center'],
        'water' => ['file'=>'water.png', 'position'=>'right-bottom', 'x'=>25, 'y'=>25],
    ],
    'someblock_secondgroup_somepict' =>[
        'sizes' => [['width'=>400, 'height'=>null, 'sufix'=>'primary', 'mod'=>true], ['width'=>300, 'height'=>null, 'sufix'=>'secondary', 'mod'=>true], ['width'=>60, 'height'=>null, 'sufix'=>'preview']],
        'mask' => ['file'=>'mask.png', 'position'=>'center']
    ]

];