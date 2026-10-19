<?php

/*
 * Numéros affichés aux clients pour payer par Orange Money / Moov Money.
 * Les valeurs se règlent dans Render > Environment (jamais dans le code) :
 *   MOBILE_MONEY_ACCOUNT_NAME   ex. Mosnoky
 *   MOBILE_MONEY_ORANGE_NUMBER  ex. 74919133
 *   MOBILE_MONEY_MOOV_NUMBER    ex. 71622104
 */
return [
    'account_name' => env('MOBILE_MONEY_ACCOUNT_NAME', 'Mosnoky'),
    'orange_number' => env('MOBILE_MONEY_ORANGE_NUMBER'),
    'moov_number' => env('MOBILE_MONEY_MOOV_NUMBER'),
];
