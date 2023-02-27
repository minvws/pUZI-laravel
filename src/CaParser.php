<?php

namespace MinVWS\PUZI\Laravel;

class CaParser
{
    /**
     * Get the CA certificates from the given path
     * @param ?string $path
     * @return array
     */
    public static function getCertsFromFile(?string $path): array
    {
        if (empty($path)) {
            return [];
        }

        $fileContent = @file_get_contents($path);
        if ($fileContent === false) {
            throw new \RuntimeException("Could not read CA certificates from $path");
        }

        $caCerts = preg_split('/-----BEGIN CERTIFICATE-----/', $fileContent);
        if ($caCerts === false) {
            return [];
        }

        // remove empty first element
        array_shift($caCerts);

        foreach ($caCerts as &$cert) {
            $cert = trim($cert);
            $cert = substr($cert, 0, strpos($cert, '-----END CERTIFICATE-----') ?: 0);
            $cert = str_replace("\n", '', $cert);
        }

        return $caCerts;
    }
}
