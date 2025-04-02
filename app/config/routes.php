<?php

use SGS\Router\RouteBuilder;

// Initialize RouteBuilder
RouteBuilder::initialize();

return [
    'routes' => [
        RouteBuilder::get('/', ['GTM', 'index'])->SetName('home')->middleware([]),
        RouteBuilder::get('/sgs-data-layer.js', ['GTM', 'index'])->SetName('gtm.index')->middleware([]),
        RouteBuilder::get('/sgs-data-layer.php', ['GTM', 'index'])->SetName('gtm.index')->middleware([]),
        RouteBuilder::get('/analytics.js', ['Analytics', 'index'])->SetName('analytics.index')->middleware([]),
    ]
];
