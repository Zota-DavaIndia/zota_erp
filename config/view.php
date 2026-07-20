<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    /*
     | custom_views is an optional override directory (UltimatePOS
     | feature): views placed there take precedence over the core
     | resources/views. It is only added when it actually exists —
     | otherwise Blade's view-path scan (e.g. during `view:cache` /
     | `optimize`) throws DirectoryNotFoundException on a missing
     | directory. Kept first so overrides still win when present.
     | (config/backup.php already guards this same path the same way.)
     */
    'paths' => array_values(array_filter([
        file_exists(base_path('custom_views')) ? base_path('custom_views') : null,
        resource_path('views'),
    ])),

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
