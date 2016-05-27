<?php
// Routes
$app->get('/', 'App\Controller\DefaultAction:defaultAction');
$app->get('/object', 'App\Controller\DefaultAction:defaultAction');
$app->get('/object/{key}', 'App\Controller\DefaultAction:getObject');
$app->post('/object', 'App\Controller\DefaultAction:insertObject');