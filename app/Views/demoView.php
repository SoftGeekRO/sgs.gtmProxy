<?php

namespace SGS\Core\Views;

use SGS\Cache\Annotations\CacheAnnotation;

#[CacheAnnotation(ttl: 120)] // CacheAnnotation this class for 120 seconds
class ExampleView {
    public function render(array $data): string
    {
        extract($data);
        ob_start();
        include __DIR__ . '/example.php';
        return ob_get_clean();
    }
}
