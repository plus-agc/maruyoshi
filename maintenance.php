<?php
header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 86400');
header('Content-Type: text/html; charset=UTF-8');
readfile(__DIR__ . '/maintenance.html');
exit;
