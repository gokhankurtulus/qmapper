<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 19.06.2023 Time: 06:31
 */


namespace QMapper\Core;

class Supplier
{
    /**
     * Generates a random string.
     *
     * @param int $digit The length of the generated string (in characters).
     * @return string The generated random string.
     * @throws \Exception If an exception occurs during the generation.
     */
    public static function randomString(int $digit): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterCount = strlen($characters);
        $result = '';

        for ($i = 0; $i < $digit; $i++) {
            $result .= $characters[random_int(0, $characterCount - 1)];
        }

        return $result;
    }

    /**
     * Generates a random hexadecimal string.
     *
     * @param int $digit The length of the generated string (in characters). Should be an even number.
     * @return string The generated random hexadecimal string.
     * @throws \InvalidArgumentException|\Exception If an exception occurs during the generation.
     */
    public static function randomHex(int $digit): string
    {
        if ($digit % 2 !== 0)
            throw new \InvalidArgumentException('length parameter must be an even number.');
        return \bin2hex(\function_exists('openssl_random_pseudo_bytes') ? \openssl_random_pseudo_bytes($digit / 2) : \random_bytes($digit / 2));
    }

    /**
     * Generates a version 4 UUID (Universally Unique Identifier).
     *
     * @return string The generated UUID.
     * @throws \InvalidArgumentException|\Exception If an exception occurs during the generation.
     */
    public static function randomUuidV4(bool $trim = true): string
    {
        if ($trim !== true && $trim !== false) {
            throw new \InvalidArgumentException('trim parameter must be true or false.');
        }

        $data = null;

        if (\function_exists('com_create_guid') === true) { // Windows
            $data = \com_create_guid();
            if ($trim === true) {
                $data = \trim($data, '{}');
            }
        } elseif (\function_exists('openssl_random_pseudo_bytes') === true) { // OSX/Linux
            $data = \openssl_random_pseudo_bytes(16);
            $data[6] = \chr(\ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = \chr(\ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            $data = \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($data), 4));
        } else {
            $data = \random_bytes(16);
            $data[6] = \chr(\ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = \chr(\ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            $data = \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($data), 4));
        }

        return $data;
    }
}