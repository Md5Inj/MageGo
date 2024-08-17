<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;

class HttpClient
{
    /**
     * Do get request and return response
     *
     * @param string $url
     * @return string
     * @throws Exception
     */
    function get(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(
                "Error occurred while doing request to '$url'. cURL error: "
                . curl_error($ch) . "\n"
            );
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Download file
     *
     * @param string $url
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    function downloadFile(string $url, string $destination): bool
    {
        $content = $this->get($url);
        if (empty($content)) {
            return false;
        }

        return file_put_contents($destination, $content) !== false;
    }
}
