<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        'certificate' =>config_path('pem/dateablePush.pem'),
        'passPhrase'  =>'dateable',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyDQ3RXnqBB6vHJ6vZ0pNWm75Um56PPbNJE',
        'service'     =>'gcm'
    )

);