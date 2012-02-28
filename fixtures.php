<?php


$fixtures = array(
    'model_example' => array(
        array('Fixture test', 'this is a simple fixture test for the model_example table')
    ),
    'user' => array(
        array('testUser', sha1(time()), 'test@test.fr'),
        array('admin', sha1('admin'), 'admin@test.fr')
    )
);