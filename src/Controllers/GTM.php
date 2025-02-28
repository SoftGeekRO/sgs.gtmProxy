<?php

namespace SGS\Controllers;

use SGS\GTM\GtmProxy;

class GTM {
    public function index(): void {
        $gtmLoader = new GtmProxy();
        $gtmLoader->handleRequest();
    }
}