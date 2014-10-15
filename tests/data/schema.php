<?php

return array(
    'create' => array(
        'users' => array(
            'id'    => 'auto',
            'email' => 'varchar(80)'
        )
    ),
    'update' => array(
        array('table' => 'users', 'add' => 'password', 'type' => 'varchar(180)')
    ),
    'seed'  => array(
        array('table' => 'users', 'truncate' => true, 'values' => array(
            'email' => 'admin@domain.com',
            'password' => 'admin::hash'
            )
        ),
        array('table' => 'users', 'values' => array(
            'email' => 'other@domain.com',
            'password' => 'other::hash'
            )
        )
    )
);