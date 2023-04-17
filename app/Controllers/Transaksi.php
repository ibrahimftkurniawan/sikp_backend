<?php
/*
## File: Transaksi.php
## File Created: Moday, 13rd February 2023 8:50:16 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;

class Transaksi extends BaseController {
    
    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }
    
    public function index() {
        
    }

    public function get_rekening() {
        $rules = [
            'NO_REK' => [
                'rules' => "required|min_length[14]",
                'errors' => [
                    'required' => "Nomor Nasabah harus diisi",
                    'min_length' => "Nomor Nasabah minimal 14 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sql = "SELECT A.KD_CAB||A.NO_REK AS NO_REK, A.NO_NSB, A.PLAFOND, A.NOMOR_PK, A.TANGGAL_PK, A.TGL_JT, A.SEKTOR_EKONOMI, B.NO_IDENTITAS FROM MASTER.LOAN_MASTER A, MASTER.NASABAH B WHERE A.KD_CAB||A.NO_REK = ? AND A.NO_NSB = B.NO_NSB";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];
            $getnsb = $this->prod->query_bind($sql, $param_to_bind);

            if ($getnsb->code == "00") {
                $data = $this->prod->db2->fetch_assoc($getnsb->data);
                if (count($data) > 0) {
                    $response = respon("00", "Success", $data[0]);
                } else {
                    $response = respon("10", "Data Debitur Tidak Ditemukan");
                }
            } else {
                $response = respon($getnsb->code, "Error SELECT MASTER.LOAN_MASTER. <br>" . str_replace('"', "'", $getnsb->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function insert_htx() {
        $rules = [
            'NOREK' => [
                'rules' => "required|min_length[14]",
                'errors' => [
                    'required' => "Nomor Rekening harus diisi",
                    'min_length' => "Nomor Rekening minimal 14 karakter"
                ]
            ],
            'TGL_TRANSAKSI' => [
                'rules' => "required",
                'errors' => ['required' => "Tanggal Transaksi harus diisi"]
            ],
            'LIMIT' => [
                'rules' => "required",
                'errors' => ['required' => "Plafond/Limit harus diisi"]
            ],
            'OUTSTANDING' => [
                'rules' => "required",
                'errors' => ['required' => "Outstanding harus diisi"]
            ],
            'ANGS_POKOK' => [
                'rules' => "required",
                'errors' => ['required' => "Angsuran Pokok harus diisi"]
            ],
            'KOLEKTIBILITY' => [
                'rules' => "required",
                'errors' => ['required' => "Kolektibility harus diisi"]
            ],
        ];

        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sql = "INSERT INTO MASTER.TRANSAKSI_SIKP (NOREK, TGL_TRANSAKSI, TGL_PELAPORAN, LIMIT, OUTSTANDING, ANGS_POKOK, KOLEKTIBILITY) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $param_to_bind = [
                ['name' => "NOREK", 'value' => $post["NOREK"], 'type' => "DB2_PARAM_IN"],
                ['name' => "TGL_TRANSAKSI", 'value' => $post["TGL_TRANSAKSI"], 'type' => "DB2_PARAM_IN"],
                ['name' => "TGL_PELAPORAN", 'value' => $post["TGL_TRANSAKSI"], 'type' => "DB2_PARAM_IN"],
                ['name' => "LIMIT", 'value' => $post["LIMIT"], 'type' => "DB2_PARAM_IN"],
                ['name' => "OUTSTANDING", 'value' => $post["OUTSTANDING"], 'type' => "DB2_PARAM_IN"],
                ['name' => "ANGS_POKOK", 'value' => $post["ANGS_POKOK"], 'type' => "DB2_PARAM_IN"],
                ['name' => "KOLEKTIBILITY", 'value' => $post["KOLEKTIBILITY"], 'type' => "DB2_PARAM_IN"],
            ];
            $insert = $this->prod->query_bind($sql, $param_to_bind);
            if ($insert->code == "00") {
                $response = respon("00", "Berhasil Menambahkan Data Transaksi SIKP", $post);
            } else {
                $response = respon($insert->code, "Error INSERT MASTER.TRANSAKSI_SIKP. <br>" . str_replace('"', "'", $insert->message));
                $this->status = FALSE;
            }
        }

        
        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function hapus() {
        $rules = [
            'NOREK' => [
                'rules' => "required|max_length[15]",
                'errors' => [
                    'required' => "Nomor Rekening harus diisi",
                    'max_length' => "Nomor Rekening maksimal 15 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else { 
            $sql = "DELETE FROM MASTER.TRANSAKSI_SIKP WHERE NOREK = ?";
            $param_to_bind = [
                ['name' => "NOREK", 'value' => $post["NOREK"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Menghapus Data Transaksi");
            } else {
                $response = respon($update->code, "Error Delete MASTER.TRANSAKSI_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }

        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Transaksi.php */