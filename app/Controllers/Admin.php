<?php
/*
## File: Admin.php
## File Created: Monday, 13th March 2023 4:39:52 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use App\Models\Custom_model;
use CodeIgniter\API\ResponseTrait;

class Admin extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }

    public function index() {
        
    }

    public function get_outstanding_core() {
        $post = $this->request->getPost();

        $sql = "SELECT A.NO_REK, B.KD_STATUS, B.SALDO_AKHIR FROM MASTER.LOAN_MASTER_SIKP A JOIN MASTER.LOAN_MASTER B ON A.NO_REK = B.KD_CAB||B.NO_REK";
        if (!empty($post["no_rek"])) {
            $rek = json_decode(base64_decode($post["no_rek"]));
            $sql .= "  WHERE A.NO_REK IN $rek";
        }

        $get = $this->prod->query($sql);

            if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            if (count($data) > 0) {
                $response = respon("00", "Success", $data);
            } else {
                $response = respon("10", "Data Tidak Ditemukan", $sql);
            }
        } else {
            $response = respon($get->code, "Error SELECT MASTER.LOAN_MASTER <br> " . $get->message);
            $this->status = FALSE;
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $this->respond($response);
    }

}

 /* End Of File Admin.php */