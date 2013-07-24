<?php

return array(
    'databases'=>array(
        'client_test1'=>array(
            'dbname'=>'klinai_test_db1',
            'host'=>'http://127.0.0.1:5984',
            'create'=>true,
        ),
        'client_test2'=>array(
            'dbname'=>'klinai_test_db2',
            'host'=>'http://127.0.0.1:5984',
            'create'=>true,
        ),
        'not_exists_database'=>array(
            'dbname'=>'klinai_not_exists_db',
            'host'=>'http://127.0.0.1:5984',
            'create'=>false,
        ),
    ),
);