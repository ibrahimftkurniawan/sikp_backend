<?php
/*
## File: Tools.php
## File Created: Saturday, 11th February 2023 2:51:26 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;
use App\Models\Custom_model;
use CodeIgniter\API\ResponseTrait;

class Tools extends BaseController {
    
    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }

    public function get_open_date() {
        $opendate = get_timestamp(2);

        $sql = "SELECT OPEN_DATE FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999'";
        $get = $this->prod->query($sql);
        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $opendate = $data[0]["OPEN_DATE"];
            } else {
                $opendate = "";
            }
        } else {
            $opendate = "";
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $opendate;
    }

    public function get_system_host() {
        $systemhost = array();

        $sql = "SELECT * FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999'";
        $get = $this->prod->query($sql);
        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $systemhost = $data[0];
            }
        } else {
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $systemhost;
    }
    
    public function index() {
        
    }

    // Tools Darurat Rekonsiliasi

    // public function export_rek_prod() {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $file = new \CodeIgniter\Files\File(WRITEPATH . 'files/rek_sikp.csv');
    //     $sql = "SELECT KD_CAB||NO_REK AS NOREK, TRIM(NAMA_SINGKAT), PLAFOND, KD_STATUS FROM MASTER.LOAN_MASTER WHERE KD_CAB||NO_REK IN (SELECT NO_REK FROM MASTER.LOAN_MASTER_SIKP)";
    //     $get = $dbsultra->query($sql);

    //     if ($get->code == "00") {
    //         $data = $dbsultra->db2->fetch_assoc($get->data);
    //         if ($file->isWritable()) {
    //             $csv = $file->openFile('w');
    //             foreach ($data as $row) {
    //                 $csv->fputcsv($row);
    //             }
    //         }
    //     }
    // }

    // public function get_akad_core() {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $post = $this->request->getPost();
    //     $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK";
    //     if (!empty($post["periode"])) {
    //         $sql .= "  WHERE B.TGL_BUKA_REK = ?";
    //         $param_to_bind = [
    //             ['name' => "TGL_BUKA_REK", 'value' => $post["periode"], 'type' => "DB2_PARAM_IN"]
    //         ];
    //         $get = $dbsultra->query_bind($sql, $param_to_bind);
    //     } else {
    //         $get = $dbsultra->query($sql);
    //     }
        

    //     if ($get->code == "00") {
    //         $data = $dbsultra->db2->fetch_assoc($get->data);
    //         if (count($data) > 0) {
    //             $response = respon("00", "Success", $data);
    //         } else {
    //             $response = respon("10", "Data Tidak Ditemukan");
    //         }
    //     } else {
    //         $response = respon($get->code, "Error SELECT MASTER.LOAN_MASTER <br> " . $get->message);
    //         $this->status = FALSE;
    //     }

    //     $dbsultra->db2->is_commit($this->status, $dbsultra->db2->connection);
    //     return $this->respond($response);
    // }

    // public function get_akad_byrek($rek) {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK WHERE A.NO_REK = '$rek'";
    //     $get = $dbsultra->query($sql);

    //     if ($get->code == "00") {
    //         $data = $dbsultra->db2->fetch_assoc($get->data);
    //         if (count($data) > 0) {
    //             $response = respon("00", "Success", $data[0]);
    //         } else {
    //             $response = respon("10", "Data Tidak Ditemukan");
    //         }
    //     } else {
    //         $response = respon($get->code, "Error SELECT MASTER.LOAN_MASTER <br> " . $get->message);
    //         $this->status = FALSE;
    //     }

    //     $dbsultra->db2->is_commit($this->status, $dbsultra->db2->connection);

    //     return $this->respond($response);
    // }

    // public function get_outstanding_core() {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $post = $this->request->getPost();
    //     $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK";
    //     if (!empty($post["no_rek"])) {
    //         $rek = json_decode(base64_decode($post["no_rek"]));
    //         $sql .= "  WHERE A.NO_REK IN $rek";
    //     }

    //     $get = $dbsultra->query($sql);
        

    //     if ($get->code == "00") {
    //         $data = $dbsultra->db2->fetch_assoc($get->data);
    //         if (count($data) > 0) {
    //             $response = respon("00", "Success", $data);
    //         } else {
    //             $response = respon("10", "Data Tidak Ditemukan", $sql);
    //         }
    //     } else {
    //         $response = respon($get->code, "Error SELECT MASTER.LOAN_MASTER <br> " . $get->message);
    //         $this->status = FALSE;
    //     }

    //     $dbsultra->db2->is_commit($this->status, $dbsultra->db2->connection);

    //     return $this->respond($response);
    // }

    // public function get_calon_core() {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $post = $this->request->getPost();
    //     $sql = "SELECT C.NO_REK, A.NO_REGISTER, A.MULAI_USAHA, A.ALAMAT_USAHA, A.IJIN_USAHA, A.MODAL_USAHA, A.JML_PEKERJA, A.IS_LINKAGE, A.LINKAGE, A.URAIAN_AGUNAN, A.IS_SUBSIDIZED, A.SUBSIDI_SEBELUMNYA, A.JML_KREDIT, A.PENDIDIKAN, A.PEKERJAAN, A.KODE_WILAYAH, A.KODE_POS, B.NAMA_NSB, B.TGL_LAHIR, B.ALAMAT, B.JNS_KELAMIN, (CASE WHEN B.STS_KAWIN = '2' THEN '0' WHEN B.STS_KAWIN = '3' THEN '2' ELSE B.STS_KAWIN END) STS_KAWIN, NVL(B.NPWP, '000000000000000') NPWP, NVL(B.NO_HP, '') NO_HP FROM MASTER.LOAN_MASTER_SIKP C JOIN MASTER.NASABAH_SIKP A ON C.NO_REGISTER = A.NO_REGISTER JOIN MASTER.NASABAH B ON A.NO_NSB = B.NO_NSB";

    //     if (!empty($post["no_rek"])) {
    //         // $rek = json_decode(base64_decode($post["no_rek"]));
    //         // $sql .= "  WHERE C.NO_REK IN $rek";
    //         $rek = $post["no_rek"];
    //         $sql .= "  WHERE C.NO_REK = '$rek'";
    //     }

    //     $get = $dbsultra->query($sql);
        

    //     if ($get->code == "00") {
    //         $data = $dbsultra->db2->fetch_assoc($get->data);
    //         if (count($data) > 0) {
    //             $response = respon("00", "Success", $data[0]);
    //         } else {
    //             $response = respon("10", "Data Tidak Ditemukan", $sql);
    //         }
    //     } else {
    //         $response = respon($get->code, "Error SELECT MASTER.NASABAH_SIKP <br> " . $get->message);
    //         $this->status = FALSE;
    //     }

    //     $dbsultra->db2->is_commit($this->status, $dbsultra->db2->connection);
    //     return $this->respond($response);
    // }

    // public function get_htx_loan() {
    //     $dbsultra = new Custom_model(NULL, NULL, "DBSULTRA");
    //     $dbwh2 = new Custom_model(NULL, NULL, "DBWH2");
    //     $post = $this->request->getPost();
    //     $sqlDBWH = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR";
    //     $sql = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR";

    //     if (!empty($post["no_rek"])) {
    //         $rek = $post["no_rek"];
    //         $sqlDBWH .= "  AND KD_CAB||NO_REK = '$rek'";
    //     }

    //     if (!empty($post["tgl_akhir"])) {
    //         $tgl = $post["tgl_akhir"];
    //         $sqlDBWH .= "  AND TGL_TX >= '$tgl'";
    //     }

    //     if (!empty($post["no_rek"]) && empty($post["tgl_akhir"])) {
    //         $rek = $post["no_rek"];
    //         $sql = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = '$rek' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR UNION SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK+TUNGG_POKOK) POKOK, SALDO_AKHIR FROM MASTER.TX_HARIAN WHERE POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = '$rek' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR";
    //     } elseif (empty($post["no_rek"]) && !empty($post["tgl_akhir"])) {
    //         $tgl = $post["tgl_akhir"];
    //         $sql = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR AND TGL_TX >= '$tgl' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR UNION SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK+TUNGG_POKOK) POKOK, SALDO_AKHIR FROM MASTER.TX_HARIAN WHERE POKOK <> SALDO_AKHIR AND TGL_TX >= '$tgl' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR";
    //     } elseif (!empty($post["no_rek"]) && !empty($post["tgl_akhir"])) {
    //         $rek = $post["no_rek"];
    //         $tgl = $post["tgl_akhir"];
    //         $sql = "SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK) POKOK, SALDO_AKHIR FROM MASTER.HTX_LOAN_NONPRK WHERE POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = '$rek' AND TGL_TX >= '$tgl' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR UNION SELECT KD_CAB||NO_REK AS NO_REK, TGL_TX, SUM(POKOK+TUNGG_POKOK) POKOK, SALDO_AKHIR FROM MASTER.TX_HARIAN WHERE POKOK <> SALDO_AKHIR AND KD_CAB||NO_REK = '$rek' AND TGL_TX >= '$tgl' GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR";
    //     }

    //     $sqlDBWH .= " GROUP BY KD_CAB||NO_REK, TGL_TX, SALDO_AKHIR ORDER BY TGL_TX, SALDO_AKHIR";

    //     $getdbwh2 = $dbwh2->query($sqlDBWH);
        

    //     if ($getdbwh2->code == "00") {
    //         $data = $dbwh2->db2->fetch_assoc($getdbwh2->data);

    //         $get = $dbsultra->query($sql);
    //         if ($get->code == "00") {
    //             $data2 = $dbsultra->db2->fetch_assoc($get->data);
    //             if (count($data2) > 0) {
    //                 $response = respon("00", "Success", array_merge($data, $data2));
    //             } else {
    //                 $response = respon("00", "Success", $data);
    //             }
    //         } else {
    //             $response = respon("00", "Success", $data);
    //             $this->status = FALSE;
    //         }
            
            
    //     } else {
    //         $response = respon($getdbwh2->code, "Error SELECT MASTER.HTX_LOAN_NONPRK <br> " . $getdbwh2->message);
    //         $this->status = FALSE;
    //     }

    //     $dbsultra->db2->is_commit($this->status, $dbsultra->db2->connection);
    //     $dbwh2->db2->is_commit($this->status, $dbwh2->db2->connection);
    //     return $this->respond($response);
    // }

}

 /* End Of File Tools.php */