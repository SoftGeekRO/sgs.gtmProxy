<?php

use SGS\Router\RouteBuilder;

// Initialize RouteBuilder
RouteBuilder::initialize();

return [
    'routes' => [
        RouteBuilder::get('/', ['GTM', 'index'])->SetName('home')->middleware([]),
        RouteBuilder::get('/gtm.js', ['GTM', 'index'])->SetName('gtm.index')->middleware([]),
        RouteBuilder::get('/gtm.php', ['GTM', 'index'])->SetName('gtm.index')->middleware([]),
        RouteBuilder::get('/analytics.js', ['Analytics', 'index'])->SetName('analytics.index')->middleware([]),
    ]
];
