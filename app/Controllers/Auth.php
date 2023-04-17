<?php
/*
## File: Auth.php
## File Created: Saturday, 11th February 2023 11:05:10 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;
use App\Controllers\Tools;


class Auth extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
        $this->tools = new Tools();
    }
    

    public function login() {
        $rules = [
            'KD_USER' => [
                'rules' => "required|min_length[5]",
                'errors' => [
                    'required' => "Kode User harus diisi",
                    'min_length' => "Kode User minimal 5 karakter"
                ]
            ],
            'PASSWORD' => [
                'rules' => "required|min_length[5]",
                'errors' => [
                    'required' => "Password harus diisi",
                    'min_length' => "Password minimal 5 karakter"
                ]
            ]

        ];

        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $hex_ori = get_hex($post["PASSWORD"]);
            $sqluser = "SELECT KD_USER, NAMA_USER, KD_CAB, HEX(PASSWORD) PASSWORD, WWN_GROUP_1, WWN_GROUP_2, WWN_GROUP_3, WWN_GROUP_4, WWN_GROUP_5, WWN_GROUP_6, WWN_GROUP_7, WWN_GROUP_8, WWN_GROUP_9, WWN_GROUP_10, PASSWORD_EXPIRED, USER_AKTIF, '$hex_ori' AS HEX_ORI  FROM MASTER.USER_LIST WHERE KD_USER = ?";
            $param_to_bind = [
                ['name' => "KD_USER", 'value' => $post["KD_USER"], 'type' => "DB2_PARAM_IN"],
            ];
            $get = $this->prod->query_bind($sqluser, $param_to_bind);

            if ($get->code == "00") {
                $data = $this->prod->db2->fetch_assoc($get->data);
                if (count($data) > 0) {
                    $wwn1 = $data[0]["WWN_GROUP_1"];
                    $wwn2 = $data[0]["WWN_GROUP_2"];
                    $wwn3 = $data[0]["WWN_GROUP_3"];
                    $wwn4 = $data[0]["WWN_GROUP_4"];
                    $wwn5 = $data[0]["WWN_GROUP_5"];
                    $wwn6 = $data[0]["WWN_GROUP_6"];
                    $wwn7 = $data[0]["WWN_GROUP_7"];
                    $wwn8 = $data[0]["WWN_GROUP_8"];
                    $wwn9 = $data[0]["WWN_GROUP_9"];
                    $wwn10 = $data[0]["WWN_GROUP_10"];
                    $passdb = $data[0]["PASSWORD"];
                    $passuser = substr($data[0]["HEX_ORI"], 0, strlen($passdb));
                    $opendate = $this->tools->get_open_date();
                    
                    if ($passdb == $passuser) {
                        /* Cek User Aktif */
                        if ($data[0]["USER_AKTIF"] != "1") {
                            $response = respon("10", "User Anda Tidak Aktif", $data[0]);
                            GOTO akhir;
                        }
                        /* Cek Password Expired*/
                        if ($data[0]["PASSWORD_EXPIRED"] < $opendate) {
                            $response = respon("10", "Password Anda Telah Expired", $data[0]);
                            GOTO akhir;
                        }

                        $sess = array();
                        if ($wwn1 == "00" || $wwn2 == "00" || $wwn3 == "00" || $wwn4 == "00" || $wwn5 == "00" || $wwn6 == "00" || $wwn7 == "00" || $wwn8 == "00" || $wwn9 == "00" || $wwn10 == "00") {
                            $sess = ['kd_user' => $data[0]["KD_USER"], 'nama_user' => $data[0]["NAMA_USER"], 'kd_cab' => $data[0]["KD_CAB"], 'login' => TRUE];
                        } elseif ($data[0]["KD_CAB"] == "000" && ($wwn1 == "04" || $wwn2 == "04" || $wwn3 == "04" || $wwn4 == "04" || $wwn5 == "04" || $wwn6 == "04" || $wwn7 == "04" || $wwn8 == "04" || $wwn9 == "04" || $wwn10 == "04")) {
                            $sess = ['kd_user' => $data[0]["KD_USER"], 'nama_user' => $data[0]["NAMA_USER"], 'kd_cab' => $data[0]["KD_CAB"], 'login' => TRUE];
                        } elseif ($wwn1 == "21" || $wwn2 == "21" || $wwn3 == "21" || $wwn4 == "21" || $wwn5 == "21" || $wwn6 == "21" || $wwn7 == "21" || $wwn8 == "21" || $wwn9 == "21" || $wwn10 == "21") {
                            $sess = ['kd_user' => $data[0]["KD_USER"], 'nama_user' => $data[0]["NAMA_USER"], 'kd_cab' => $data[0]["KD_CAB"] , 'login' => TRUE];
                        } 
                        
                        if (count($sess) > 0) {
                            $response = respon("00", "Selamat, Anda Berhasil Login", $sess);
                        } else {
                            $response = respon("10", "Maaf Anda Tidak Memiliki Wewenang Mengakses Aplikasi Ini", $sess);
                        }

                    } else {
                        $response = respon("10", "User atau Password Tidak Susuai", []);
                    }

                } else {
                    $response = respon("10", "User atau Password Tidak Susuai", []);
                }

            } else {
                $response = respon($get->code, "Error SELECT MASTER.USER_LIST. <br>" . str_replace('"', "'", $get->message));
                $this->status = FALSE;
            }
        }

        akhir:
        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $this->respond($response);
    }

}

 /* End Of File Auth.php */