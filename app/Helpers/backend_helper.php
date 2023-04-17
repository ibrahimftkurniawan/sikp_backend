<?php
/*
## File: backend_helper.php
## File Created: Friday, 10th February 2023 10:24:27 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

if (!function_exists('enkripsi')) {
    function enkripsi($string) {
        $secret_key = 'B@nK$uLtR4#135#!';
        $secret_iv = '0123456789012345';
        $encrypt_method = 'aes-256-cbc';

        $key = hash("sha256", $secret_key);
        $iv = substr(hash("sha256", $secret_iv), 0, 16);
        $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($result);
        return $output;
    }
}

if (!function_exists('dekripsi')) {
    function dekripsi($string) {
        $secret_key = 'B@nK$uLtR4#135#!';
        $secret_iv = '0123456789012345';
        $encrypt_method = 'aes-256-cbc';

        $key = hash("sha256", $secret_key);
        $iv = substr(hash("sha256", $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}

if (!function_exists('to_blob')) {
    function to_blob($str) {
        $bin = "";
        for ($i = 0, $j = strlen($str); $i < $j; $i++)
            $bin .= decbin(ord($str[$i])) . " ";
        return trim($bin);
    }
}

if (!function_exists('to_str')) {
    function to_str($bin) {
        $char = explode(' ', $bin);
        $userStr = '';
        foreach ($char as $ch)
            $userStr .= chr(bindec($ch));

        return trim($userStr);
    }
}
// add by anak baru

if (!function_exists('get_timestamp')) {
    function get_timestamp($jenis) {
        date_default_timezone_set('Asia/Makassar');
        switch ($jenis) {
            case 1: //Timestamp
                $waktu = date('Y-m-d H:i:s');
                break;
            case 2: // Tanggal
                $waktu = date('Y-m-d');
                break;
            case 3: // Jam
                $waktu = date('H:i:s');
                break;
            case 4: // Tahun
                $waktu = date('Y');
                break;
            case 5: // JAM 12 - Tambahan 02-09-2021
                $waktu = date('H');
                break;
            case 6: // Menit - Tambahan 02-09-2021
                $waktu = date('i');
                break;
            default:
                $waktu = date('Y-m-d H:i:s');
                break;
        }

        return $waktu;
    }
}

if (!function_exists('date_differ')) {
    function date_differ($tglawal, $tglakhir, $option) {
        $awal = new DateTime($tglawal);
        $akhir = new DateTime($tglakhir);
        $diff = $awal->diff($akhir);

        if ($option == 1) { // Tahun
            $hasil = $diff->y;
        } elseif ($option == 2) { // Bulan
            $hasil = $diff->m;
        } elseif ($option == 3) { // Hari
            $hasil = $diff->m;
        } elseif ($option == 4) { // Jam
            $hasil = $diff->h;
        } elseif ($option == 5) { // Menit
            $hasil = $diff->i;
        } elseif ($option == 6) { // Detik
            $hasil = $diff->s;
        } elseif ($option == 9) { // Total Hari
            $hasil = $diff->days;
        } else {
            $hasil = $diff->days;
        }
        return $hasil;
    }
}

if (!function_exists('get_hex')) {
    function get_hex($password) {
        $key = '0E13I5';
        $fr = substr($password, 0, 2);
        $bk = substr($password, 2);

        $newPass = $fr . "" . $key . "" . $bk;
        $hashmd5 = md5($newPass);

        $split_str = str_split($hashmd5, 16);

        $keyN = hex2binary($split_str[0]);
        $chip = hex2binary($split_str[1]);
        $desenc = desEncrypt($keyN, $chip);
        $bin2hex = strtoupper(binary2hex($desenc));

        $bin = str_replace("9B", "", $bin2hex); //Added By IFTK

        return $bin2hex;
    }
}

if (!function_exists('encrypt')) {
    function encrypt($password) {
        $key = '0E13I5';
        $fr = substr($password, 0, 2);
        $bk = substr($password, 2);

        $newPass = $fr . "" . $key . "" . $bk;
        $hashmd5 = md5($newPass);

        $split_str = str_split($hashmd5, 16);

        $keyN = hex2binary($split_str[0]);
        $chip = hex2binary($split_str[1]);
        $desenc = desEncrypt($keyN, $chip);
        $bin2hex = binary2hex($desenc);
        $pass = hex2str($bin2hex);

        return $pass;
    }
}


if (!function_exists('encrypt_pien')) {
    function encrypt_pien($password) {
        $key = '013eI5eVr';

        $fr = substr($password, 0, 2);
        $bk = substr($password, 2);

        $newPass = $fr . "" . $key . "" . $bk;
        $hashmd5 = md5($newPass);

        $split_str = str_split($hashmd5, 16);

        $keyN = hex2binary($split_str[0]);
        $chip = hex2binary($split_str[1]);
        $desenc = desEncrypt($keyN, $chip);
        $bin2hex = binary2hex($desenc);
        $pass = hex2str($bin2hex);

        return $pass;
    }
}

/* Added By IFTK 10 November 2022 */
if (!function_exists('send_data')) {
    function send_data($url, $header = NULL, $method, $json = FALSE, $param) {
        if ($method == 'GET') {
            $param_type = ($json == TRUE) ? "json" : "query";
        } else {
            $param_type = ($json == TRUE) ? "json" : "form_params";
        }
        
        $data = (!empty($header)) ? [$param_type => $param, 'headers' => $header] : [$param_type => $param];

        try {
            $client = new Client();
            $send = $client->request($method, $url, $data);
            $hasil = $send->getBody()->getContents();
            $decode = json_decode($hasil, TRUE);
            if (!is_array($decode)) {
                $hasil = ['code' => "99", 'message' => "Format Response Tidak Valid"];
            } else {
                $hasil = $decode;
            }

        } catch (ConnectException $e) {
            $hasil = ['code' => "89", 'message' => "Cannot Connect To URL"];
        } catch (ServerException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $hasil = ['code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase()];

                if ($_SERVER["CI_ENVIRONMENT"] == 'development') {
                    $return = json_decode($response->getBody()->getContents());
                    $hasil["data"] = ['file' => $return->file, 'line' => $return->line, 'error' => $return->message];
                }
            } else {
                $hasil = ['code' => "89", 'message' => "No Response From Host (ServerException)"];
            }
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $hasil = ['code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase()];
            } else {
                $hasil = ['code' => '99', 'message' => 'No Response From Host (ClientException)'];
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $hasil = ['code' => $response->getStatusCode(), 'message' => $response->getReasonPhrase()];
            } else {
                $hasil = ['code' => '99', 'message' => 'No Response From Host (RequestException)'];
            }
        }

        return (array) $hasil;
    }
}

if (!function_exists('respon')) {
    function respon(string $errcode = '99', string $message = '', $data = NULL) {
        $response = [
            'code' => $errcode,
            'message' => $message,
            'data' => $data
        ];
        
        return $response;
    }

}

if (!function_exists('penyebut')) {

    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

}


if (!function_exists('terbilang')) {

    function terbilang($nilai) {
        if ($nilai < 0) {
            $hasil = "minus " . trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }
        return $hasil;
    }

}

if (!function_exists('rupiah')) {

    function rupiah($rupiah) {
        return number_format($rupiah, 0, '.', ',');
    }

}