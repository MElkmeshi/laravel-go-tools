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
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum execution time in seconds for Go binary calls.
    |
    */
    'timeout' => env('GO_TOOLS_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | DB Compare Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum execution time in seconds for the db compare command.
    | This is higher than the default timeout because comparing large
    | datasets across databases can take several minutes.
    |
    */
    'db_compare_timeout' => env('GO_TOOLS_DB_COMPARE_TIMEOUT', 300),

];
