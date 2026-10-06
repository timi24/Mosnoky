<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // Le disque "public" (utilisé partout dans l'app pour les photos
        // produits) bascule automatiquement entre stockage local et Supabase
        // Storage selon la variable d'environnement PUBLIC_DISK_DRIVER.
        // - En local (ton PC) : PUBLIC_DISK_DRIVER absent -> reste en "local"
        // - Sur Render : PUBLIC_DISK_DRIVER=s3 -> utilise Supabase Storage,
        //   qui est permanent (ne disparaît jamais au redéploiement).
        'public' => [
            'driver' => env('PUBLIC_DISK_DRIVER', 'local'),
            // Important : "root" ne doit servir qu'en local. Avec le pilote S3,
            // ce chemin serveur s'ajoutait à tort dans l'adresse des photos.
            'root' => env('PUBLIC_DISK_DRIVER', 'local') === 's3'
                ? ''
                : storage_path('app/public'),
            // Important : l'adresse pour ENVOYER les fichiers (endpoint S3 ci-dessous)
            // est différente de l'adresse pour les AFFICHER publiquement.
            // Supabase affiche les fichiers via /storage/v1/object/public/<bucket>/...
            'url' => env('PUBLIC_DISK_DRIVER', 'local') === 's3'
                ? rtrim((string) env('SUPABASE_PROJECT_URL'), '/').'/storage/v1/object/public/'.env('SUPABASE_S3_BUCKET')
                : rtrim((string) env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,

            // Ces 5 lignes ne servent que si PUBLIC_DISK_DRIVER=s3
            // (connexion S3-compatible vers Supabase Storage)
            'key' => env('SUPABASE_S3_KEY'),
            'secret' => env('SUPABASE_S3_SECRET'),
            'region' => env('SUPABASE_S3_REGION', 'us-east-1'),
            'bucket' => env('SUPABASE_S3_BUCKET'),
            'endpoint' => env('SUPABASE_S3_ENDPOINT'),
            'use_path_style_endpoint' => true,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
