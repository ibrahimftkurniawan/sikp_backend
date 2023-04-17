<?php
/*
## File: Encryption.php
## File Created: Friday, 10th February 2023 10:40:40 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Libraries;

class Encryption {

    var $skey = 'IniAdalahKeyDariEbissWeb';

    public function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($data, $key = null) {
        $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
        $iv_size = openssl_cipher_iv_length("AES-256-CBC-HMAC-SHA256");
        $hash = hash('sha256', $salt . $key . $salt);
        $iv = substr($hash, strlen($hash) - $iv_size);
        $key = substr($hash, 0, 32);
        $encrypted = base64_encode(openssl_encrypt($data, "AES-256-CBC-HMAC-SHA256", $key, OPENSSL_RAW_DATA, $iv));

        return trim($this->safe_b64encode($encrypted));
    }

    public function decode($data, $key = null) {
        $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
        $iv_size = openssl_cipher_iv_length("AES-256-CBC-HMAC-SHA256");
        $hash = hash('sha256', $salt . $key . $salt);
        $iv = substr($hash, strlen($hash) - $iv_size);
        $key = substr($hash, 0, 32);
        $decrypted = openssl_decrypt(base64_decode($this->safe_b64decode($data)), "AES-256-CBC-HMAC-SHA256", $key, OPENSSL_RAW_DATA, $iv);
        $decrypted = rtrim($decrypted, "\0");
        // echo $decrypted;
        // die();
        return $decrypted;
    }

    function write_php_ini($array, $file) {
        $res = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval)
                    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
            } else
                $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
        }
        $this->safefilerewrite($file, implode("\r\n", $res));
    }

    function safefilerewrite($fileName, $dataToSave) {
        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime(TRUE);
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite)
                    usleep(round(rand(0, 100) * 1000));
            } while ((!$canWrite) and ( (microtime(TRUE) - $startTime) < 5));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

}