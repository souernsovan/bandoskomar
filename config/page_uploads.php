<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Page save script time (seconds)
    |--------------------------------------------------------------------------
    |
    | Large multi-image saves (home/platform/product sections) can exceed PHP's
    | default max_execution_time while moving and persisting files. Increase via
    | PAGE_UPLOAD_MAX_EXECUTION in .env if needed.
    |
    | Also ensure the web stack allows large bodies and long requests:
    | - PHP: post_max_size, upload_max_filesize, max_file_uploads (default 20)
    | - nginx: client_max_body_size, proxy_read_timeout / fastcgi_read_timeout
    |
    */
    'max_execution_seconds' => max(60, (int) env('PAGE_UPLOAD_MAX_EXECUTION', 600)),

];
