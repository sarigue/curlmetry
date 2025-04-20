<?php

namespace Curlmetry\Test\Tools;

use Curlmetry\Exporter\JaegerExporter;

class JaegerDebugExporter extends JaegerExporter
{
    /** @var resource|string|null  */
    private $output = null;

    /**
     * @param resource|string $handleOrFile
     *
     * @return $this
     */
    public function setOutput($handleOrFile)
    {
        $this->output = $handleOrFile;
        return $this;
    }

    protected function send($payload)
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ];

        $result = '';
        $result .= '=== JAEGER HTTP Export ===' . PHP_EOL;
        $result .= 'POST ' . $this->endpoint . PHP_EOL;
        $result .= implode(PHP_EOL, $headers) . PHP_EOL . PHP_EOL;
        $result .= $json . PHP_EOL;
        $result .= '=======================' . PHP_EOL;

        if ($this->output && is_string($this->output)) {
            $h = fopen($this->output, 'w+');
            fwrite($h, $result);
            fclose($h);
        } else {
            fwrite($this->output && is_resource($this->output) ?: STDOUT, $result);
        }

        return true;
    }
}
