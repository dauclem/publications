<?php

apcu_clear_cache();

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
header('Status: 302 Found', true, 302);
header('Location: '.$redirect, true, 302);
