<?php

return array(

    'ios' => array(
        'environment' =>'production',
        'certificate' =>config_path('pem/dateablePush.pem'),
        'passPhrase'  =>'mireapp',
        'service'     =>'apns'
    ),
    'android' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyDQ3RXnqBB6vHJ6vZ0pNWm75Um56PPbNJE',
        'service'     =>'gcm'
    )

);