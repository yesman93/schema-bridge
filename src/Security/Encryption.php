<?php

namespace Lumio\Security;

class Encryption {

    /**
     * Cipher algorithm - AES-256-CBC
     *
     * @var string
     */
    const _AES_128_CBC = 'aes-128-cbc';

    /**
     * Cipher algorithm - AES-192-CBC
     *
     * @var string
     */
    const _AES_192_CBC = 'aes-192-cbc';

    /**
     * Cipher algorithm - AES-256-CBC
     *
     * @var string
     */
    const _AES_256_CBC = 'aes-256-cbc';

    /**
     * Default cipher algorithm
     *
     * @var string
     */
    private const _DEFAULT_METHOD = self::_AES_256_CBC;

    /**
     * Encryption salt
     *
     * @var string
     */
    private const IV_SALT = 'lumio_iv_salt';

    /**
     * Encrypt a string using the argon2 algorithm
     *
     * @param string $string
     *
     * @return string
     */
    public static function argon2(string $string) : string {

        return password_hash($string, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 17, // 128 MB
            'time_cost'   => 4, // 4 iterations
            'threads'     => 2, // 2 CPU threads, if available
        ]);
    }

    /**
     * Encrypt a string
     *
     * @param string $string
     * @param string|null $key
     *
     * @return string
     */
    public static function encrypt(string $string, ?string $key = null) : string {

        if (empty($string)) {
            return '';
        }

        $key = $key ?? SSL_ENC_KEY;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::_DEFAULT_METHOD));

        $encrypted = openssl_encrypt($string, self::_DEFAULT_METHOD, $key, 0, $iv);

        if ($encrypted === false) {
            return '';
        }

        $iv_obfuscated = self::xor_encrypt($iv, self::IV_SALT);

        return $encrypted . ':' . base64_encode($iv_obfuscated);
    }

    /**
     * Decrypt a string
     *
     * @param string $encrypted_string
     * @param string|null $key
     *
     * @return string
     */
    public static function decrypt(string $encrypted_string, ?string $key = null) : string {

        if (empty($encrypted_string)) {
            return '';
        }

        $key = $key ?? SSL_ENC_KEY;
        [$ciphertext, $iv_obfuscated] = explode(':', $encrypted_string) + [null, null];

        if (empty($ciphertext) || empty($iv_obfuscated)) {
            return '';
        }

        $iv = self::xor_decrypt($iv_obfuscated, self::IV_SALT);

        return (string) openssl_decrypt($ciphertext, self::_DEFAULT_METHOD, $key, 0, $iv);
    }

    /**
     * Lightweight XOR "encryption" for simple obfuscation
     *
     * @param string $string
     * @param string $salt
     *
     * @return string
     */
    public static function xor_encrypt(string $string, string $salt): string {

        $salt_length = strlen($salt);
        $output = '';

        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            $output .= chr(ord($string[$i]) ^ ord($salt[$i % $salt_length]));
        }

        return $output;
    }

    /**
     * XOR decryption (symmetric with xor_encrypt)
     *
     * @param string $string
     * @param string $salt
     *
     * @return string
     */
    public static function xor_decrypt(string $string, string $salt): string {
        return self::xor_encrypt($string, $salt);
    }

}
