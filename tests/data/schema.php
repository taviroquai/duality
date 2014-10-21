<?php

return array(
    'create' => array(
        'users' => array(
            'id'    => 'auto',
            'email' => 'varchar(80)'
        )
    ),
    'update' => array(
        array('table' => 'users', 'add' => 'password', 'type' => 'varchar(180)'),
        array('table' => 'users', 'add' => 'password2', 'type' => 'varchar(180)'),
        array('table' => 'users', 'modify' => 'password2', 'type' => 'varchar(120)'),
        array('table' => 'users', 'drop' => 'password2')
    ),
    'seed'  => array(
        array('table' => 'users', 'truncate' => true, 'values' => array(
            'id'        => '1::int',
            'email'     => 'admin@domain.com',
            'password'  => 'admin::hash'
            )
        ),
        array('table' => 'users', 'values' => array(
            'id'        => '2::int',
            'email'     => 'other@domain.com',
            'password'  => 'other::hash'
            )
        )
    )
);