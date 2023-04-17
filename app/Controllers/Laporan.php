<?php
/*
## File: Laporan.php
## File Created: Friday, 3rd March 2023 2:24:37 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;


class Laporan extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model(NULL, NULL, "DBREDE");
        $this->tools = new Tools();
    }

    public function get_adk() {
        $sql = "SELECT D.NO_IDENTITAS AS NIK , A.KD_CAB ||  A.NO_REK AS NO_REK, C.NAMA_SINGKAT, C.PLAFOND, A.SALDO_AKHIR AS OUTSTANDING, DAYS(NVL((SELECT MIN(A1.TGL_TX ) FROM MASTER.V_SIKP_REKON A1 WHERE A1.NO_REK = A.NO_REK  AND A.KD_CAB = A1.KD_CAB  AND A1.TGL_TX > A.TGL_TX  ), (SELECT NEXT_DATE FROM MASTER.SYSTEM_HOST)))  -   DAYS(A.TGL_TX) AS LAMA_HARI, A.TGL_TX, CASE WHEN LEFT(B.SKEMA,1)='1' THEN 0.105 ELSE 0.055 END AS PROSEN_SUBSIDI FROM MASTER.V_SIKP_REKON A, MASTER.LOAN_MASTER_SIKP B,  MASTER.LOAN_MASTER C , MASTER.NASABAH D, MASTER.SIKP_MAP_SEKTOR E WHERE B.NO_REK = A.KD_CAB ||  A.NO_REK  AND A.KD_CAB ||  A.NO_REK = C.KD_CAB ||  C.NO_REK  AND C.NO_NSB = D.NO_NSB  AND E.SEKTOR = C.SEKTOR_EKONOMI";
        $get = $this->prod->query($sql);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.V_SIKP_REKON. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function export_txt_adk() {
        $sql = "SELECT * FROM MASTER.V_SIKP_REKON2";
        $get = $this->prod->query($sql);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.V_SIKP_REKON. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function export_excel_adk() {
        $sql = "SELECT '135' AS KD_BANK, (SELECT YEAR(OPEN_DATE) FROM MASTER.SYSTEM_HOST) AS TAHUN, 
                (SELECT VARCHAR_FORMAT(OPEN_DATE, 'MM') FROM MASTER.SYSTEM_HOST) AS BULAN,  LEFT(B.SKEMA,1) AS SKEMA, E.KODE_SIKP, D.NO_IDENTITAS AS NIK , A.KD_CAB ||  A.NO_REK AS NOREK, TRIM(C.NAMA_SINGKAT) NAMA_SINGKAT, C.PLAFOND,   A.SALDO_AKHIR AS OUTSTANDING, DAYS(NVL((SELECT MIN(A1.TGL_TX )  
            FROM MASTER.V_SIKP_REKON A1 WHERE A1.NO_REK = A.NO_REK  AND A.KD_CAB = A1.KD_CAB  AND A1.TGL_TX > A.TGL_TX  ),
            (SELECT NEXT_DATE FROM MASTER.SYSTEM_HOST)))  -   DAYS(A.TGL_TX) AS LAMA_HARI, A.TGL_TX, CASE WHEN LEFT(B.SKEMA,1)='5' THEN 0.12 
			WHEN LEFT(B.SKEMA,1)='1' THEN 0.10 ELSE 0.055  END AS PROSEN_SUBSIDI FROM MASTER.V_SIKP_REKON A, MASTER.LOAN_MASTER_SIKP B,  
			MASTER.LOAN_MASTER C , MASTER.NASABAH D, MASTER.SIKP_MAP_SEKTOR E WHERE B.NO_REK = A.KD_CAB ||  A.NO_REK  AND 
			A.KD_CAB ||  A.NO_REK = C.KD_CAB ||  C.NO_REK  AND C.NO_NSB = D.NO_NSB  AND E.SEKTOR = C.SEKTOR_EKONOMI 
			AND C.TGL_JT >= (SELECT OPEN_DATE - 1 MONTH FROM MASTER.SYSTEM_HOST WHERE THE_KEY = '999')";
        $get = $this->prod->query($sql);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.V_SIKP_REKON. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function get_debitur() {
        $sql = "SELECT LEFT(A.NO_REK,3) AS KD_CAB, RIGHT(A.NO_REK,11) AS NO_REK, B.NO_NSB, B.NAMA_SINGKAT, B.TANGGAL_CAIR,
        B.TGL_JT, B.SALDO_AKHIR, B.JNK_WKT_BL, B.PLAFOND FROM MASTER.LOAN_MASTER_SIKP A LEFT JOIN MASTER.LOAN_MASTER B
        ON B.KD_CAB||B.NO_REK = A.NO_REK WHERE B.KD_STATUS = '1' ORDER BY KD_CAB";
        $get = $this->prod->query($sql);

        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan");
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.V_SIKP_REKON. " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Laporan.php */