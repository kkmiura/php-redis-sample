<?php

$endpoint = 'redis';

$ipAddresses = gethostbynamel($endpoint);

print_r($ipAddresses);
