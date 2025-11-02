<?php
// db_init.php - optional quick initializer that creates DB/tables and default images if missing.
// Run once: http://localhost/Internet_programming_621/Semester_2/0.Assignment/insta_mock/db_init.php

require_once __DIR__ . '/config.php';

// read db.sql and execute
$sqlFile = __DIR__ . '/db.sql';
if (file_exists($sqlFile)) {
    $commands = file_get_contents($sqlFile);
    if ($commands) {
        if ($mysqli->multi_query($commands)) {
            do {
                if ($res = $mysqli->store_result()) $res->free();
            } while ($mysqli->more_results() && $mysqli->next_result());
        }
    }
}

// create uploads dir and default images (if not present)
$uploads = __DIR__ . '/uploads';
if (!is_dir($uploads)) mkdir($uploads, 0755, true);

// write default_profile.png from base64 (small placeholder)
$default = base64_decode('<BASE64_DEFAULT>');
file_put_contents($uploads . '/default_profile.png', $default);

// sample post images
$sample1 = base64_decode('<BASE64_SAMPLE_1>');
file_put_contents($uploads . '/sample1.png', $sample1);

echo "DB init complete. Created uploads and default images. If you see errors, check file permissions.";
