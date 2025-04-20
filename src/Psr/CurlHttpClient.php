<?php

namespace Curlmetry\Psr;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class CurlHttpClient
 *
 * This class provides an HTTP client implementation using cURL for sending HTTP requests.
 * It conforms to the ClientInterface.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class CurlHttpClient implements ClientInterface
{
    /**
     * @param RequestInterface $request
     *
     * @return Response
     * @throws ClientException
     */
    public function sendRequest(RequestInterface $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->getUri());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($request->getMethod() === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getBody());
        }

        $headers = [];
        foreach ($request->getHeaders() as $k => $v) {
            if (is_array($v) || is_object($v)) {
                foreach ($v as $_) {
                    $headers[] = $k . ': ' . (is_array($_) ? implode(', ', $_) : $_);
                }
                continue;
            }
            $headers[] = $k . ': ' . $v;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $body   = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($body, 0, $headerSize);
        $body       = substr($body, $headerSize);
        $status     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error      = curl_error($ch);

        curl_close($ch);

        $headersLines = explode("\r\n", trim($rawHeaders));
        $firstLine    = reset($headersLines);

        $reason = '';
        if (preg_match('#^HTTP/\d\.\d\s+(\d{3})\s+(.+)$#', $firstLine, $matches)) {
            $status = (int) $matches[1];
            $reason = $matches[2];
        }

        $responseHeader = [];
        foreach ($headersLines as $hline) {
            if (strpos($hline, ':') === false) {
                continue;
            }
            list($k, $v) = explode(':', $hline, 2);
            if (!array_key_exists($k, $responseHeader)) {
                $responseHeader[$k] = [];
            }
            $responseHeader[$k][] = $v;
        }

        if ($body === false || $status >= 400) {
            throw new ClientException("HTTP error $status: $error");
        }

        return new Response($status, $headersLines, $body, $reason);
    }
}
