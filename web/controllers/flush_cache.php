<?php

apc_clear_cache('user');

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
header('Status: 302 Found', true, 302);
header('Location: '.$redirect, true, 302);
