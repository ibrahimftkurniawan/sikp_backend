<?php
/*
## File: Rekon.php
## File Created: Tuesday, 28th February 2023 2:43:20 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use App\Models\Custom_model;
use CodeIgniter\API\ResponseTrait;

class Rekon extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }

    public function get_outstanding_byrek($rek = '') {
        $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK WHERE A.NO_REK = ?";
        $param_to_bind = [
            ['name' => "A.NO_REK", 'value' => $rek, 'type' => "DB2_PARAM_IN"]
        ];
        $get = $this->prod->query_bind($sql, $param_to_bind);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data[0]);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }

        } else {
            $response = respon($get->code, "Error SELECT MASTER.LOAN_MASTER. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $this->respond($response);
    }

    public function get_htxloan_byrek($rek = '') {
        $sql = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = ? GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR UNION SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK+TUNGG_POKOK) POKOK, SALDO_AKHIR FROM MASTER.TX_HARIAN WHERE POKOK+TUNGG_POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = ? GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR DESC";
        $param_to_bind = [
            ['name' => "NO_REK1", 'value' => $rek, 'type' => "DB2_PARAM_IN"],
            ['name' => "NO_REK2", 'value' => $rek, 'type' => "DB2_PARAM_IN"]
        ];

        $get = $this->prod->query_bind($sql, $param_to_bind);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }

        } else {
            $response = respon($get->code, "Error SELECT MASTER.HTX_LOAN_NONPRK. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);

    }

    public function get_transaksi_setoran() {
        $sql = "SELECT C.NO_REK, B.PLAFOND, MAX(B.KOLEKTIBILITY) KOLEKTIBILITY, A.TGL_TX, SUM(A.POKOK) POKOK, MIN(A.SALDO_AKHIR) SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK A JOIN MASTER.LOAN_MASTER B ON A.KD_CAB||A.NO_REK = B.KD_CAB||B.NO_REK JOIN MASTER.LOAN_MASTER_SIKP C ON A.KD_CAB||A.NO_REK = C.NO_REK WHERE A.POKOK > 0 AND LEFT(A.NO_REK, 4) IN ('0423', '0424', '0525') AND A.DB_KR = 'K' AND A.TGL_TX = (SELECT LAST_DATE FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999') GROUP BY A.KD_CAB||A.NO_REK, B.PLAFOND, A.TGL_TX ORDER BY C.NO_REK, A.TGL_TX";
        
        $get = $this->prod->query($sql);
        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }

        } else {
            $response = respon($get->code, "Error SELECT MASTER.HTX_LOAN_NONPRK. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function get_transaksi_setoran_byrek() {
        $rules = [
            'no_rek' => [
                'rules' => "required", 'errors' => ['required' => "Nomor Rekening harus diisi"]
            ],
            'tgl_tx' => [
                'rules' => "required", 'errors' => ['required' => "Tanggal Transaksi harus diisi"]
            ],
        ];

        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sql = "SELECT A.KD_CAB||A.NO_REK AS NO_REK, B.PLAFOND, MAX(B.KOLEKTIBILITY) KOLEKTIBILITY, A.TGL_TX, SUM(A.POKOK) POKOK, MIN(A.SALDO_AKHIR) SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK A JOIN MASTER.LOAN_MASTER B ON A.KD_CAB||A.NO_REK = B.KD_CAB||B.NO_REK WHERE A.POKOK > 0 AND A.KD_CAB||A.NO_REK = ? AND A.TGL_TX > ? GROUP BY A.KD_CAB||A.NO_REK, B.PLAFOND, A.TGL_TX ORDER BY NO_REK, TGL_TX";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["no_rek"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REK", 'value' => $post["tgl_tx"], 'type' => "DB2_PARAM_IN"]
            ];
    
            $get = $this->prod->query_bind($sql, $param_to_bind);
            if ($get->code == "00") {
                $data = $this->prod->db2->fetch_assoc($get->data);
                if (count($data) > 0) {
                    $response = respon("00", "Success", $data);
                } else {
                    $response = respon("10", "Data Tidak Ditemukan");
                }
    
            } else {
                $response = respon($get->code, "Error SELECT MASTER.HTX_LOAN_NONPRK. " . $get->message);
                $this->status = FALSE;
            }
        }


        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Rekon.php */