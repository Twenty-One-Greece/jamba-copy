<?php

 return array(
    'type' => 'select',
    'heading' => 'Visibility',
    'default' => '',
    'options' => array(
        ''   => 'Always visible',
        'hide-for-medium'  => 'Only for Desktop',
        'show-for-small'   =>  'Only for Mobile',
        'show-for-medium hide-for-small' =>  'Only for Tablet',
        'show-for-medium'   =>  'Hide for Dekstop',
        'hide-for-small'   =>  'Hide for Mobile',
    ),
);