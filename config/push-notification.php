<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        'certificate' =>storage_path('app/pem/dateablePush.pem'),
        'passPhrase'  =>'dateable',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyDQ3RXnqBB6vHJ6vZ0pNWm75Um56PPbNJE',
        'service'     =>'gcm'
    )

);