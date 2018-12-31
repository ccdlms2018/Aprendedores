<?php
$deps = array(
    // 'controllers/init',
    'models/seq-tasks',
    'views/seq-tasks',
    'admins',
    'assets'
);

foreach( $deps as $dep ) include_once( $dep . '.php' );
