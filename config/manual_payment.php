<?php

/*
 * Numéros affichés aux clients pour payer par Orange Money / Moov Money.
 *
 * Par défaut on utilise les numéros de contact de la boutique (ceux du site).
 * Pour les changer SANS toucher au code : Render > Environment, variables
 *   MOBILE_MONEY_ACCOUNT_NAME, MOBILE_MONEY_ORANGE_NUMBER, MOBILE_MONEY_MOOV_NUMBER
 */
return [
    'account_name' => env('MOBILE_MONEY_ACCOUNT_NAME', 'Mosnoky'),
    'orange_number' => env('MOBILE_MONEY_ORANGE_NUMBER', '+226 74 91 91 33'),
    'moov_number' => env('MOBILE_MONEY_MOOV_NUMBER', '+226 71 62 21 04'),
];