<?php

namespace PROXY\Lib\GTM;

use SGS\Config\Config;

class GtmProxy {
    private string $httpEndpoint = 'https://www.googletagmanager.com/gtm.js';
    private string $pathname = '';
    private array $data;

    public function __construct(array $config = []) {
        $this->data = $config ?: [
            'pathnameGtm' => '/gtm.js',
            'allowedContainerIds' => Config::get('GTM_IDS')
        ];
        $this->pathname = $this->data['pathnameGtm'] ?? '/gtm.js';
    }

    private function getRequestPath(): false|array|int|string|null {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function getRequestQueryParameters(): array {

        //parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
        return $_GET;
    }

    private function getRequestHeader($header) {
        $header = str_replace('-', '_', strtoupper($header));
        return $_SERVER['HTTP_' . $header] ?? null;
    }

    private function setResponseBody($body): void {
        echo $body;
    }

    private function setResponseStatus($statusCode): void {
        http_response_code($statusCode);
    }

    private function setResponseHeader($key, $value): void {
        header("$key: $value");
    }

    private function sendHttpGet($url, $callback, $options = []): void {
        $timeout = $options['timeout'] ?? 1500;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; GtmLoader/1.0)');
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $body = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($body === false || $errno !== 0) {
            $this->sendJsonError(502, "cURL Error: $error (Code: $errno)");
            return;
        }

        $headers = [];
        $callback($statusCode, $headers, $body);
    }

    private function returnResponse() {
        exit;
    }

    private function validateGtmId($gtmId): bool {
        return preg_match('/^GTM-[A-Z0-9]{6,}$/', $gtmId) === 1;
    }

    private function sendJsonError($statusCode, $message): void {
        $this->setResponseStatus($statusCode);
        $this->setResponseHeader('Content-Type', 'application/json');
        //$this->setResponseHeader('Content-Type', 'text/html; charset=utf-8');
        $this->setResponseBody(json_encode([
            'error' => true,
            'message' => $message
        ]));
        $this->returnResponse();
    }

    private function handlePreviewMode($gtmContainerId, $gtmDatalayerName, $gtmAuth, $gtmDebug, $gtmPreview): void {
        $url = $this->httpEndpoint . '?id=' . urlencode($gtmContainerId);
        $url .= '&l=' . urlencode($gtmDatalayerName);
        if ($gtmAuth) {
            $url .= '&gtm_auth=' . urlencode($gtmAuth);
        }
        if ($gtmDebug) {
            $url .= '&gtm_debug=' . urlencode($gtmDebug);
        }
        if ($gtmPreview) {
            $url .= '&gtm_preview=' . urlencode($gtmPreview);
        }

        $this->sendHttpGet($url, function($statusCode, $headers, $body) {
            $this->setResponseStatus($statusCode);
            $this->setResponseBody($body);
            foreach ($headers as $key => $value) {
                if (!in_array($key, ['expires', 'date'])) {
                    $this->setResponseHeader($key, $value);
                }
            }
            $this->returnResponse();
        }, ['timeout' => 1500]);
    }

    private function handleNormalMode($gtmContainerId, $gtmDatalayerName): void {
        $url = $this->httpEndpoint . '?id=' . urlencode($gtmContainerId);
        $url .= '&l=' . urlencode($gtmDatalayerName);

        $this->sendHttpGet($url, function($statusCode, $headers, $body) {
            $this->setResponseStatus($statusCode);
            $this->setResponseHeader('content-type', 'text/javascript');
            $this->setResponseBody($body);

            $origin = $this->getRequestHeader('Origin');
            if ($origin) {
                $this->setResponseHeader('Access-Control-Allow-Origin', $origin);
                $this->setResponseHeader('Access-Control-Allow-Credentials', 'true');
            }

            $this->returnResponse();
        });
    }

    public function handleRequest(): void {

        $queryParameters = $this->getRequestQueryParameters();

        if (empty($queryParameters['id'])) {
            throw new \Error("Missing required GTM Container ID", 400);
        }

        $gtmContainerId = $queryParameters['id'];

        if (!$this->validateGtmId($gtmContainerId)) {
            throw new \Error("Invalid GTM Container ID format. Must match GTM-[A-Z0-9]{6,}", 400);
        }

        $gtmAuth = $queryParameters['gtm_auth'] ?? '';
        $gtmPreview = $queryParameters['gtm_preview'] ?? '';
        $gtmDebug = $queryParameters['gtm_debug'] ?? '';
        $gtmDatalayerName = $queryParameters['l'] ?? 'dataLayer';

        if (!in_array($gtmContainerId, $this->data['allowedContainerIds'])) {
            throw new \Error("Unauthorized GTM Container ID", 403);
        }

        if ($gtmAuth || $gtmPreview || $gtmDebug) {
            $this->handlePreviewMode($gtmContainerId, $gtmDatalayerName, $gtmAuth, $gtmDebug, $gtmPreview);
        } else {
            $this->handleNormalMode($gtmContainerId, $gtmDatalayerName);
        }
    }
}
