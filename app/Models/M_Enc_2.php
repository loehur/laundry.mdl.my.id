<?php

class M_Enc_2
{
    function e($string)
    {
        $key = "MDL3313";
        $ciphering = "AES-128-CTR";
        $options = 0;
        $crypt_iv = '1234567891011121';
        $crypt = openssl_encrypt(
            $string,
            $ciphering,
            $key,
            $options,
            $crypt_iv
        );
        return $crypt;
    }

    function d($string)
    {
        $key = "MDL3313";
        $ciphering = "AES-128-CTR";
        $options = 0;
        $crypt_iv = '1234567891011121';
        $crypt = openssl_decrypt(
            $string,
            $ciphering,
            $key,
            $options,
            $crypt_iv
        );
        return $crypt;
    }
}
