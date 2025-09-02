<?php

namespace Lumio\Security;

use Lumio\Config;

class SignedURLBuilder {

    /**
     * The URL to be signed
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var string
     */
    private string $_url;

    /**
     * Expiration timestamp
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int|null
     */
    private ?int $_expiration = null;

    /**
     * Builder for signed URLs
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $url
     *
     * @return void
     */
    public function __construct(string $url) {
        $this->_url = rtrim($url, '/');
    }

    /**
     * Init the TTL setting
     *
     * @author TB
     * @date 15.5.2025
     *
     * @return TTLBuilder
     */
    public function ttl(): TTLBuilder {
        return new TTLBuilder($this);
    }

    /**
     * Set URL expiration
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $timestamp
     *
     * @return self
     */
    public function expire(int $timestamp): self {
        $this->_expiration = $timestamp;
        return $this;
    }

    /**
     * Get the signed URL
     *
     * @author TB
     * @date 15.5.2025
     *
     * @return string
     */
    public function get(): string {

        $key = ENCRYPTION_SALT;
        $signature_data = $this->_url;

        $exp_obfuscated = '';
        if ($this->_expiration !== null) {
            $exp_obfuscated = self::_base62_encode((string) $this->_expiration);
            $signature_data .= $exp_obfuscated;
        }

        $signature = hash_hmac('sha256', $signature_data, $key);

        $signed = $this->_url . '/';

        if ($this->_expiration !== null) {
            $signed .= '--' . $exp_obfuscated;
        }

        $signed .= '--' . $signature;

        return $signed;
    }

    /**
     * Get the signed URL as a string
     *
     * @author TB
     * @date 15.5.2025
     *
     * @return string
     */
    public function __toString(): string {
        return $this->get();
    }

    /**
     * Encode given integer to a base62 string - timestamp obfuscation
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $value
     *
     * @return string
     */
    private static function _base62_encode(int $value): string {

        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $base = strlen($charset);

        $encoded = '';
        while ($value > 0) {
            $encoded = $charset[$value % $base] . $encoded;
            $value = (int) ($value / $base);
        }

        return $encoded;
    }

}
