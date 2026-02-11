<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Binary Path
    |--------------------------------------------------------------------------
    |
    | Path to the Go binary. By default, it looks in the package's bin directory.
    | You can override this to point to a custom location.
    |
    */
    'binary_path' => env('GO_TOOLS_BINARY_PATH'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Repository
    |--------------------------------------------------------------------------
    |
    | The GitHub repository used for downloading release binaries.
    |
    */
    'github_repo' => 'melkmeshi/laravel-go-tools',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | The version of the Go binary to download. Use 'latest' to always
    | download the most recent release.
    |
    */
    'version' => 'latest',

    /*
    |--------------------------------------------------------------------------
    | OSRM Server URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the OSRM routing server.
    |
    */
    'osrm_url' => env('OSRM_URL', 'http://router.project-osrm.org'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum execution time in seconds for Go binary calls.
    |
    */
    'timeout' => env('GO_TOOLS_TIMEOUT', 30),

];
