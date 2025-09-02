<?php

namespace Lumio\Security;

use Lumio\Config;

class URLSigner {

    /**
     * Entry point to build signed URL
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $url
     *
     * @return SignedURLBuilder
     */
    public static function sign(string $url): SignedURLBuilder {
        return new SignedURLBuilder($url);
    }

    /**
     * Validate given signed URL
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $signed_url
     *
     * @return bool
     */
    public static function is_valid(string $signed_url): bool {

        $key = ENCRYPTION_SALT;
        $parts = explode('--', $signed_url);

        if (count($parts) < 2) {
            return false;
        }

        $base = $parts[0];
        $obf_exp = null;
        $sig = end($parts);

        if (count($parts) === 3) {
            $obf_exp = $parts[1];
        }

        $signature_data = $base;
        if ($obf_exp !== null) {
            $signature_data .= $obf_exp;
        }

        $valid_signature = hash_hmac('sha256', $signature_data, $key);
        if (!hash_equals($valid_signature, $sig)) {
            return false;
        }

        if ($obf_exp !== null) {

            $expiration = (int) self::_base62_decode($obf_exp);
            if (time() > $expiration) {
                return false;
            }
        }

        return true;
    }

    /**
     * Decode given base62 encoded string - timestamp de-obfuscation
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $encoded
     *
     * @return int
     */
    private static function _base62_decode(string $encoded): int {

        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $base = strlen($charset);
        $decoded = 0;

        for ($i = 0; $i < strlen($encoded); $i++) {
            $decoded = $decoded * $base + strpos($charset, $encoded[$i]);
        }

        return $decoded;
    }

}
