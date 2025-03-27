<?php

namespace PROXY\Controllers;

use PROXY\Lib\GTM\GtmProxy;
use SGS\Controller\AppController;

class GTM extends AppController {

    public function index($id): void {
        $gtmLoader = new GtmProxy();
        $gtmLoader->handleRequest();
    }

}