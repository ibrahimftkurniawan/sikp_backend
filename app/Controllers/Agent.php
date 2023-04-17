<?php
/*
## File: Agent.php
## File Created: Tuesday, 28th February 2023 11:45:03 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;

class Agent extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
        $this->tools = new Tools();
    }
    
    public function index() {
        
    }

    public function get_system_host() {
        $sql = "SELECT OPEN_DATE, NEXT_DATE, LAST_DATE, EXPORT_IMPORT_ATM FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999'";
        $get = $this->prod->query($sql);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data[0]);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.SYSTEM_HOST. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function update_sts_tx($sts = '1') {
        $sqlupdate = "UPDATE MASTER.LOAN_MASTER_SIKP SET STS_TX = ?";
        $param_to_bind = [
            ['name' => "STS_TX", 'value' => $sts, 'type' => "DB2_PARAM_IN"]
        ];
        $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
        if ($update->code == "00") {
            $response = respon("00", "Berhasil Mengubah Status Transaksi ($sts)");
        } else {
            $response = respon($update->code, "Error UPDATE MASTER.LOAN_MASTER_SIKP. " . $update->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function update_ststx_byrek($rek = '', $sts = '1') {
        $sqlupdate = "UPDATE MASTER.LOAN_MASTER_SIKP SET STS_TX = ? WHERE NO_REK = ?";
        $param_to_bind = [
            ['name' => "STS_TX", 'value' => $sts, 'type' => "DB2_PARAM_IN"],
            ['name' => "NO_REK", 'value' => $rek, 'type' => "DB2_PARAM_IN"]
        ];
        $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
        if ($update->code == "00") {
            $response = respon("00", "Berhasil Mengubah Status Transaksi ($sts)");
        } else {
            $response = respon($update->code, "Error UPDATE MASTER.LOAN_MASTER_SIKP. " . $update->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function get_transaksi_pencairan() {
        $sql = "SELECT C.NO_REK, B.PLAFOND, MAX(B.KOLEKTIBILITY) KOLEKTIBILITY, A.TGL_TX, SUM(A.POKOK) POKOK, MIN(A.SALDO_AKHIR) SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK A JOIN MASTER.LOAN_MASTER B ON A.KD_CAB||A.NO_REK = B.KD_CAB||B.NO_REK JOIN MASTER.LOAN_MASTER_SIKP C ON A.KD_CAB||A.NO_REK = C.NO_REK WHERE C.STS_TX = '0' AND A.SALDO_AKHIR = B.PLAFOND AND LEFT(A.NO_REK, 4) IN ('0423', '0424', '0525') AND A.DB_KR = 'D' AND A.TGL_TX = (SELECT LAST_DATE FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999') GROUP BY C.NO_REK, B.PLAFOND, A.TGL_TX ORDER BY C.NO_REK, A.TGL_TX";
        
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

    public function get_transaksi_setoran() {
        $sql = "SELECT C.NO_REK, B.PLAFOND, MAX(B.KOLEKTIBILITY) KOLEKTIBILITY, A.TGL_TX, SUM(A.POKOK) POKOK, MIN(A.SALDO_AKHIR) SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK A JOIN MASTER.LOAN_MASTER B ON A.KD_CAB||A.NO_REK = B.KD_CAB||B.NO_REK JOIN MASTER.LOAN_MASTER_SIKP C ON A.KD_CAB||A.NO_REK = C.NO_REK WHERE C.STS_TX = '0' AND A.POKOK > 0 AND LEFT(A.NO_REK, 4) IN ('0423', '0424', '0525') AND A.DB_KR = 'K' AND A.TGL_TX = (SELECT LAST_DATE FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999') GROUP BY C.NO_REK, B.PLAFOND, A.TGL_TX ORDER BY C.NO_REK, A.TGL_TX";
        
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

    public function get_outstanding_byrek($rek = '') {
        $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR, B.KOLEKTIBILITY FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK WHERE A.NO_REK = ?";
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

}

 /* End Of File Agent.php */