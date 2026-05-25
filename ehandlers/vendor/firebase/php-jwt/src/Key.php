<?php

/**
 * LITE MODIFICATION — DO NOT sync from upstream without review.
 *
 * This file is a hand-backported, PHP 5.6/7.x-compatible variant of the
 * stock firebase/php-jwt v6.11.1 src/Key.php. The upstream original uses
 * PHP 8.0-only syntax that would fatal on Lite's minimum PHP target.
 *
 * Differences from stock v6.11.1:
 *   - Constructor property promotion removed (explicit property
 *     declarations + manual assignment in the constructor body).
 *   - ': string' return type on getAlgorithm() removed.
 *   - OpenSSLAsymmetricKey / OpenSSLCertificate instanceof checks
 *     removed (those classes do not exist before PHP 8.0).
 *
 * Rest of the firebase/php-jwt library (other 11 files) is stock and
 * may be synced from upstream normally.
 *
 * REVERT THIS BACKPORT when Lite's minimum PHP requirement reaches 8.0:
 * replace this file with the stock v6.11.1 Key.php. The removed OpenSSL
 * instanceof checks are a security-relevant input validation that
 * should be restored once PHP 8.0 is the floor.
 *
 * Upstream reference: e107inc/e107, e107_handlers/vendor/firebase/php-jwt/src/Key.php
 */

namespace Firebase\JWT;

use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;
use TypeError;
 

class Key
{
    /** @var string|resource */
    private $keyMaterial;

    /** @var string */
    private $algorithm;

    /**
     * @param string|resource $keyMaterial
     * @param string $algorithm
     */
    public function __construct($keyMaterial, $algorithm)
    {
        $this->keyMaterial = $keyMaterial;
        $this->algorithm = $algorithm;

        if (
            !is_string($keyMaterial)
            && !is_resource($keyMaterial)
        ) {
            throw new TypeError('Key material must be a string or resource');
        }

        if (empty($keyMaterial)) {
            throw new InvalidArgumentException('Key material must not be empty');
        }

        if (empty($algorithm)) {
            throw new InvalidArgumentException('Algorithm must not be empty');
        }
    }

    /**
     * Return the algorithm valid for this key
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return string|resource
     */
    public function getKeyMaterial()
    {
        return $this->keyMaterial;
    }
}

?>
