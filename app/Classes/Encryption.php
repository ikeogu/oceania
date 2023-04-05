<?php
namespace App\Classes;
/**
 * Sarawak Pay Encryption Class
 */
class Encryption
{
    const DESKEY_FORMAT_LENGTH = 6;

    /**
     * @param  string  $data  JSON string to be encrypted
     * @param  string  $key   RSA Public Key Path
     * @return string         Encrypted data
     */
    public static function encrypt(string $data, string $key)
    {
        $desKey = random_bytes(24);

        if (openssl_public_encrypt($desKey, $encryptedDesKey, static::getRsaPublicKey($key))) {
            $encryptedData          = openssl_encrypt($data, 'des-ede3', $desKey, OPENSSL_RAW_DATA);
            $encryptedDesKeyLength  = sprintf("%06d", strlen($encryptedDesKey));
            $encryptedMessage       = $encryptedDesKeyLength . $encryptedDesKey . $encryptedData;

            return base64_encode($encryptedMessage);
        }

        return false;
    }

    /**
     * @param  string  $data  Encrypted data to be decrypt
     * @param  string  $key   RSA Private Key Path
     * @return string         Decrypted JSON string
     */
    public static function decrypt(string $data, string $key)
    {
        $encryptedMessage   = base64_decode($data);
        $keyLengthByte      = substr($encryptedMessage, 0, self::DESKEY_FORMAT_LENGTH);
        $encryptedMessage   = substr($encryptedMessage, self::DESKEY_FORMAT_LENGTH);
        $keyLengthInt       = intval($keyLengthByte);
        $encryptedDesKey    = substr($encryptedMessage, 0, $keyLengthInt);
        $encryptedMessage   = substr($encryptedMessage, $keyLengthInt);

        if (openssl_private_decrypt($encryptedDesKey, $decryptedDesKey, static::getRsaPrivateKey($key))) {

            $result = openssl_decrypt($encryptedMessage, 'des-ede3', $decryptedDesKey, OPENSSL_RAW_DATA);

            return $result;
        }

        return false;
    }

    /**
     * @param  string  $data  String data to be sign
     * @param  string  $key   RSA Private Key Path
     * @return string         Decrypted JSON string
     */
    public static function generateSignature($data, $key)
    {
        $sortedData = static::sortData($data);
        openssl_sign($sortedData, $binary_signature, static::getRsaPrivateKey($key), 'SHA256');

        return base64_encode($binary_signature);
    }

    /**
     * @param  string  $data  String data to be verify
     * @param  string  $key   RSA Public Key Path
     * @return boolean        Result
     */
    public static function checkSignature(string $data, string $signature, $key)
    {
        $sortedData = static::sortData($data);

        return openssl_verify($sortedData, base64_decode($signature), static::getRsaPublicKey($key), 'SHA256');
    }

    /**
     * @param  string  $data  Json string data to be verify
     * @param  string  $key   RSA Public Key Path
     * @return boolean        Result
     */
    public static function verifySignature(string $data, string $key)
    {
        $data = json_decode($data, true, 512, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $signature = $data['sign'];
        unset($data['sign']);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return static::checkSignature($data, $signature, $key);
    }


    private static function getRsaPrivateKey(string $filename)
    {
        return openssl_get_privatekey(static::getKey($filename));
    }

    private static function getRsaPublicKey(string $filename)
    {
        return openssl_get_publickey(static::getKey($filename));
    }

    private static function getKey(string $filename)
    {
        $key_path = $filename;
        $fp = fopen($key_path, "r");
        $rsaKey = fread($fp, 8192);
        fclose($fp);

        return $rsaKey;
    }

    private static function sortData(string $data)
    {
        $_data = unpack('C*', $data);
        sort($_data);
        return pack('C*', ...$_data);
    }
}
