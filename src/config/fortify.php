<?php

return [
    'guard' => 'web',

    'features' => [
        \Laravel\Fortify\Features::registration(),
        \Laravel\Fortify\Features::resetPasswords(),
    ],
];
