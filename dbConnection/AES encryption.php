<?php
//THE KEY FOR ENCRYPTION AND DECRYPTION, CAN BE ANYTHING
$key = 'qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';

// Check if the function doesn't exist before declaring it
if (!function_exists('encryptthis')) {
    //ENCRYPT FUNCTION
    function encryptthis($data, $key)
    {
        if (!empty($data)) {
            $encryption_key = base64_decode($key);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
            return base64_encode($encrypted . '::' . $iv);
        } else {
            return null;
        }
    }
}

// Check if the function doesn't exist before declaring it
if (!function_exists('decryptthis')) {
    //DECRYPT FUNCTION
    function decryptthis($data, $key)
    {
        if (!empty($data)) {
            $encryption_key = base64_decode($key);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            if ($iv === null) {
                return "Invalid data format";
            }
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        } else {
            return null;
        }
    }
}
?>