<?php
/*
## File: Penjaminan.php
## File Created: Sunday, 26th February 2023 5:08:32 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;

class Penjaminan extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }
    
    public function index() {
        
    }

    public function hapus() {
        $rules = [
            'NO_REK' => [
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
            $sql = "DELETE FROM MASTER.PENJAMINAN_SIKP WHERE NO_REK = ?";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Menghapus Data Penjaminan");
            } else {
                $response = respon($update->code, "Error Delete MASTER.PENJAMINAN_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }

        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Penjaminan.php */