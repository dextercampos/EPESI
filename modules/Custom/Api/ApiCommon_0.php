<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
class Custom_ApiCommon extends ModuleCommon
{
    private const METHOD = 'AES-256-CBC';

    public static function decrypt(string $encrypted, ?string $key = null): string
    {
        $key = $key ?? self::getEncryptionKey();

        $payload = \json_decode(\base64_decode($encrypted), true);

        $iv = \base64_decode($payload['encodedIv']);

        $result = \openssl_decrypt($payload['encrypted'] ?? '', self::METHOD, $key, 0, $iv);

        if ($result === false) {
            return '';
        }

        return $result;
    }

    public static function encrypt(string $clear, ?string $key = null): string
    {
        $key = $key ?? self::getEncryptionKey();
        $iv = \random_bytes(openssl_cipher_iv_length(self::METHOD));

        $encrypted = \openssl_encrypt($clear, self::METHOD, $key, 0, $iv);
        $encodedIv = \base64_encode($iv);

        return \base64_encode(\json_encode(\compact('encrypted', 'encodedIv')));
    }

    public static function getApiKey(): string
    {
        return \md5('api' . INSTALLATION_ID . EPESI . EPESI_VERSION . 'key');
    }

    private static function getEncryptionKey(): string
    {
        return \md5('encryption' . EPESI . INSTALLATION_ID . EPESI_VERSION . 'key');
    }
}
