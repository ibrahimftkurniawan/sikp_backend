<?php
/*
## File: Akad.php
## File Created: Thursday, 16th February 2023 11:30:53 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;

class Akad extends BaseController {

    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
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

    public function get_akad() {
        $rules = [
            'NO_REK' => [
                'rules' => "required|min_length[14]",
                'errors' => [
                    'required' => "Nomor Rekening harus diisi",
                    'min_length' => "Nomor Rekening minimal 14 karakter"
                ]
            ],
            // 'NO_REGISTER' => [
            //     'rules' => "required|max_length[10]",
            //     'errors' => [
            //         'required' => "Nomor Register harus diisi",
            //         'max_length' => "Nomor Register maksimal 10 karakter"
            //     ]
            // ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            
            $sql = "SELECT A.NO_IDENTITAS , B.NO_REK_LAMA, B.NO_REK, B.STATUS_AKAD, B.STATUS_REKENING, C.NOMOR_PK, C.TANGGAL_PK , C.TGL_JT, C.PLAFOND, B.KODE_PENJAMIN,B.NOMOR_PENJAMINAN, B.NILAI_DIJAMIN, B.SKEMA, C.SEKTOR_EKONOMI, B.PERSEN_BUNGA_RILL AS PRS_BUNGA, NVL(B.NEGARA_TUJUAN,'') AS NEGARA_TUJUAN, B.RC_AKAD FROM MASTER.NASABAH A , MASTER.LOAN_MASTER_SIKP B, MASTER.LOAN_MASTER C WHERE A.NO_NSB = C.NO_NSB AND B.NO_REK = C.KD_CAB||C.NO_REK AND B.NO_REK = ?"; 
            // AND B.NO_REGISTER = ?";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"],
                // ['name' => "NO_REGISTER", 'value' => $post["NO_REGISTER"], 'type' => "DB2_PARAM_IN"]
            ];
            $getcalon = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($getcalon->code == "00") {
                $data = $this->prod->db2->fetch_assoc($getcalon->data);
                if (count($data) > 0) {
                    $response = respon("00", "Success", $data[0]);
                } else {
                    $response = respon("10", "Data Tidak Ditemukan");
                }

            } else {
                $response = respon($getcalon->code, "Error SELECT MASTER.NASABAH. <br>" . str_replace('"', "'", $getcalon->message));
                $this->status = FALSE;
            }

        }


        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function get_akad_lama() {
        $rules = [
            'NO_REK' => [
                'rules' => "required|min_length[14]",
                'errors' => [
                    'required' => "Nomor Rekening harus diisi",
                    'min_length' => "Nomor Rekening minimal 14 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            
            $sql = "SELECT A.NO_IDENTITAS , B.NO_REK_LAMA, B.NO_REK, B.STATUS_AKAD, B.STATUS_REKENING, C.NOMOR_PK, C.TANGGAL_PK , C.TGL_JT, C.PLAFOND, B.KODE_PENJAMIN,B.NOMOR_PENJAMINAN, B.NILAI_DIJAMIN, B.SKEMA, C.SEKTOR_EKONOMI, B.PERSEN_BUNGA_RILL AS PRS_BUNGA, NVL(B.NEGARA_TUJUAN,'') AS NEGARA_TUJUAN, B.RC_AKAD FROM MASTER.NASABAH A , MASTER.LOAN_MASTER_SIKP B, MASTER.LOAN_MASTER C WHERE A.NO_NSB = C.NO_NSB AND B.NO_REK = C.KD_CAB||C.NO_REK AND B.NO_REK = ?";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];
            $getcalon = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($getcalon->code == "00") {
                $data = $this->prod->db2->fetch_assoc($getcalon->data);
                if (count($data) > 0) {
                    $response = respon("00", "Success", $data[0]);
                } else {
                    $response = respon("10", "Data Tidak Ditemukan");
                }

            } else {
                $response = respon($getcalon->code, "Error SELECT MASTER.NASABAH. <br>" . str_replace('"', "'", $getcalon->message));
                $this->status = FALSE;
            }

        }


        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function update_data_akad() {
        $rules = [
            'NO_REK_LAMA' => [
                'rules' => "required|max_length[15]",
                'errors' => [
                    'required' => "Nomor Rekening Lama harus diisi",
                    'max_length' => "Nomor Rekening Lama maksimal 15 karakter"
                ]
            ],
            'STATUS_AKAD' => [
                'rules' => "required|max_length[1]",
                'errors' => [
                    'required' => "Status Akad harus diisi",
                    'max_length' => "Status Akad maksimal 1 karakter"
                ]
            ],
            'STATUS_REKENING' => [
                'rules' => "required|max_length[1]",
                'errors' => [
                    'required' => "Status Rekening harus diisi",
                    'max_length' => "Status Rekening maksimal 1 karakter"
                ]
            ],
            'KODE_PENJAMIN' => [
                'rules' => "required|max_length[1]",
                'errors' => [
                    'required' => "Kode Penjamin harus diisi",
                    'max_length' => "Kode Penjamin maksimal 1 karakter"
                ]
            ],
            'NOMOR_PENJAMINAN' => [
                'rules' => "required|max_length[45]",
                'errors' => [
                    'required' => "Nomor Penjaminan harus diisi",
                    'max_length' => "Nomor Penjaminan maksimal 45 karakter"
                ]
            ],
            'NILAI_DIJAMIN' => [
                'rules' => "required",
                'errors' => ['required' => "Nilai Dijamin harus diisi"]
            ],
            'SKEMA' => [
                'rules' => "required|max_length[2]",
                'errors' => [
                    'required' => "Skema harus diisi",
                    'max_length' => "Skema maksimal 2 karakter"
                ]
            ],
            'PERSEN_BUNGA_RILL' => [
                'rules' => "required",
                'errors' => ['required' => "Persen Bunga harus diisi"]
            ],
            'NO_REGISTER' => [
                'rules' => "required|max_length[10]",
                'errors' => [
                    'required' => "Nomor Register harus diisi",
                    'max_length' => "Nomor Register maksimal 10 karakter"
                ]
            ],
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
            $sqlupdate = "UPDATE MASTER.LOAN_MASTER_SIKP SET NO_REK_LAMA = ?, STATUS_AKAD = ?, STATUS_REKENING = ?, KODE_PENJAMIN = ?, NOMOR_PENJAMINAN = ?, NILAI_DIJAMIN = ?, SKEMA = ?, NEGARA_TUJUAN = ?, PERSEN_BUNGA_RILL = ? WHERE NO_REGISTER = ? AND NO_REK = ?";

            $param_to_bind = [
                ['name' => "NO_REK_LAMA", 'value' => $post["NO_REK_LAMA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "STATUS_AKAD", 'value' => $post["STATUS_AKAD"], 'type' => "DB2_PARAM_IN"],
                ['name' => "STATUS_REKENING", 'value' => $post["STATUS_REKENING"], 'type' => "DB2_PARAM_IN"],
                ['name' => "KODE_PENJAMIN", 'value' => $post["KODE_PENJAMIN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NOMOR_PENJAMINAN", 'value' => $post["NOMOR_PENJAMINAN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NILAI_DIJAMIN", 'value' => $post["NILAI_DIJAMIN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "SKEMA", 'value' => $post["SKEMA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NEGARA_TUJUAN", 'value' => $post["NEGARA_TUJUAN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "PERSEN_BUNGA_RILL", 'value' => $post["PERSEN_BUNGA_RILL"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REGISTER", 'value' => $post["NO_REGISTER"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];

            $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Data Akad Debitur SIKP", $post);
            } else {
                $response = respon($update->code, "Error Update MASTER.LOAN_MASTER_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);

    }

    public function update_rc() {
        $rules = [
            'RC_AKAD' => [
                'rules' => "required", 'errors' => ['required' => "RC Akad harus diisi"]
            ], 
            'MSG_AKAD' => [
                'rules' => "required", 'errors' => ['required' => "Message Akad harus diisi"]
            ], 
            'NO_REGISTER' => [
                'rules' => "required|max_length[10]",
                'errors' => [
                    'required' => "Nomor Register harus diisi", 
                    'max_length' => "Nomor Register maksimal 10 karakter"
                ]
            ],
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
            $sqlupdate = "UPDATE MASTER.LOAN_MASTER_SIKP SET STS_AKAD = '1', RC_AKAD = ?, MSG_AKAD = ? WHERE NO_REGISTER = ? AND NO_REK = ?";
            $param_to_bind = [
                ['name' => "RC_AKAD", 'value' => $post["RC_AKAD"], 'type' => "DB2_PARAM_IN"],
                ['name' => "MSG_AKAD", 'value' => $post["MSG_AKAD"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REGISTER", 'value' => $post["NO_REGISTER"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Data Akad Debitur SIKP", $post);
            } else {
                $response = respon($update->code, "Error Update MASTER.LOAN_MASTER_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
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
            $sql = "UPDATE MASTER.LOAN_MASTER_SIKP SET STS_AKAD = '2' WHERE NO_REK = ?";
            $param_to_bind = [
                ['name' => "NO_REK", 'value' => $post["NO_REK"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Status Data Akad");
            } else {
                $response = respon($update->code, "Error Update MASTER.LOAN_MASTER_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }

        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Akad.php */