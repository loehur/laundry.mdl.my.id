<?php

class Enc
{
    function username($hp)
    {
        return md5(md5(md5($hp + 8117686252)));
    }

    function enc($text)
    {
        if (isset($_SESSION['secure']['encryption'])) {
            if ($_SESSION['secure']['encryption'] <> "j499uL0v3ly&N3lyL0vEly_F0r3ver") {
                $newText = crypt(md5($text), md5($text . "FALSE")) . md5(md5($text)) . crypt(md5($text), md5("FALSE"));
                return $newText;
            } else {
                //TRUE
                $newText = crypt(md5($text), md5($text . "j499uL0v3ly&N3lyL0vEly_F0r3ver")) . md5(md5($text)) . crypt(md5($text), md5("saturday_10.06.2017_12.45"));
                return $newText;
            }
        } else {
            $newText = crypt(md5($text), md5($text . "FALSE")) . md5(md5($text)) . crypt(md5($text), md5("FALSE"));
            return $newText;
        }
    }

    function enc_2($encryption)
    {
        $ciphering = "AES-128-CTR";
        $options = 0;

        $encryption_iv = '1234567891011121';
        $encryption_key = "j499uL0v3ly&N3lyL0vEly_F0r3ver";

        $encryption = openssl_encrypt(
            $encryption,
            $ciphering,
            $encryption_key,
            $options,
            $encryption_iv
        );
        return $encryption;
    }

    function dec_2($encryption)
    {
        $ciphering = "AES-128-CTR";
        $options = 0;

        $decryption_iv = '1234567891011121';
        $decryption_key = "j499uL0v3ly&N3lyL0vEly_F0r3ver";

        $decryption = openssl_decrypt(
            $encryption,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv
        );

        return $decryption;
    }
}
