<?php
header('Content-Type: application/json');
echo json_encode([
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size'       => ini_get('post_max_size'),
    'max_execution_time'  => ini_get('max_execution_time'),
    'memory_limit'        => ini_get('memory_limit'),
    'loaded_ini'          => php_ini_loaded_file(),
    'scanned_ini_files'   => php_ini_scanned_files(),
    'sapi'                => php_sapi_name(),
]);
