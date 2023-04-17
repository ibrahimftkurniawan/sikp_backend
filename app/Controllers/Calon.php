<?php
/*
## File: Calon.php
## File Created: Monday, 13th February 2023 2:59:46 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
use App\Models\Custom_model;

class Calon extends BaseController {
    
    use ResponseTrait;

    public function __construct() {
        $this->prod = new Custom_model();
    }
    
    public function get_nasabah() {
        $rules = [
            'NO_NSB' => [
                'rules' => "required|min_length[8]",
                'errors' => [
                    'required' => "Nomor Nasabah harus diisi",
                    'min_length' => "Nomor Nasabah minimal 8 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sql = "SELECT NO_NSB, KD_JNS_NSB, NO_IDENTITAS, NAMA_NSB, TGL_LAHIR, ALAMAT, JNS_KELAMIN, (CASE WHEN STS_KAWIN = '2' THEN '0' WHEN STS_KAWIN = '3' THEN '2' ELSE STS_KAWIN END) STS_KAWIN , NVL(NPWP, '000000000000000') NPWP, NVL(NO_HP, '') NO_HP FROM MASTER.NASABAH WHERE NO_NSB = ?";
            $param_to_bind = [
                ['name' => "NO_NSB", 'value' => $post["NO_NSB"], 'type' => "DB2_PARAM_IN"]
            ];
            $getnsb = $this->prod->query_bind($sql, $param_to_bind);

            if ($getnsb->code == "00") {
                $data = $this->prod->db2->fetch_assoc($getnsb->data);
                if (count($data) > 0) {
                    $response = respon("00", "Success", $data[0]);
                } else {
                    $response = respon("10", "Data Nasabah Tidak Ditemukan");
                }
            } else {
                $response = respon($getnsb->code, "Error SELECT MASTER.NASABAH. <br>" . str_replace('"', "'", $getnsb->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function get_calon() {
        $rules = [
            'NO_NSB' => [
                'rules' => "required|min_length[8]",
                'errors' => [
                    'required' => "Nomor Nasabah harus diisi",
                    'min_length' => "Nomor Nasabah minimal 8 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            
            $sql = "SELECT NO_REGISTER, NO_NSB, TO_CHAR(MULAI_USAHA, 'DD-MM-YYYY') MULAI_USAHA, ALAMAT_USAHA, IJIN_USAHA, MODAL_USAHA, JML_PEKERJA, IS_LINKAGE, LINKAGE, URAIAN_AGUNAN, IS_SUBSIDIZED, SUBSIDI_SEBELUMNYA, JML_KREDIT, PENDIDIKAN, PEKERJAAN, KODE_WILAYAH, KODE_POS FROM MASTER.NASABAH_SIKP WHERE NO_NSB = ? ORDER BY NO_REGISTER DESC";
            $param_to_bind = [
                ['name' => "NO_NSB", 'value' => $post["NO_NSB"], 'type' => "DB2_PARAM_IN"]
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

    public function update_data_calon() {
        $rules = [
            'NO_REGISTER' => [
                'rules' => "required|max_length[10]",
                'errors' => [
                    'required' => "Nomor Register harus diisi",
                    'max_length' => "Nomor Register maksimal 10 karakter"
                ]
            ],
            'MULAI_USAHA' => [
                'rules' => "required", 'errors' => ['required' => "Tanggal Mulai Usaha harus diisi"]
            ],
            'ALAMAT_USAHA' => [
                'rules' => "required|max_length[200]",
                'errors' => [
                    'required' => "Alamat Usaha harus diisi",
                    'max_length' => "Alamat Usaha maksimal 200 karakter"
                ]
            ],
            'KODE_WILAYAH' => [
                'rules' => "required|min_length[4]|max_length[5]",
                'errors' => [
                    'required' => "Kode Kab/Kota harus diisi",
                    'min_length' => "Kode Kab/Kota minimal 4 karakter",
                    'max_length' => "Kode Kab/Kota maksimal 5 karakter"
                ]
            ],
            'KODE_POS' => [
                'rules' => "required|max_length[5]",
                'errors' => [
                    'required' => "Kodepos harus diisi",
                    'max_length' => "Kodepos maksimal 5 karakter"
                ]
            ],
            'IJIN_USAHA' => [
                'rules' => "required|max_length[45]",
                'errors' => [
                    'required' => "Izin Usaha harus diisi",
                    'max_length' => "Izin Usaha maksimal 45 karakter"
                ]
            ],
            'MODAL_USAHA' => [
                'rules' => "required|greater_than[0]",
                'errors' => [
                    'required' => "Modal Usaha harus diisi",
                    'max_length' => "Modal Usaha harus lebih dari 0"
                ]
            ],
            'JML_PEKERJA' => [
                'rules' => "required|greater_than[0]",
                'errors' => [
                    'required' => "Jumlah Pekerja harus diisi",
                    'max_length' => "Jumlah Pekerja harus lebih dari 0"
                ]
            ],
            'IS_LINKAGE' => [
                'rules' => "required|max_length[1]",
                'errors' => [
                    'required' => "IS Linkage harus diisi",
                    'max_length' => "IS Linkage maksimal 1 karakter"
                ]
            ],
            'URAIAN_AGUNAN' => [
                'rules' => "required|max_length[50]",
                'errors' => [
                    'required' => "Uraian Agunan harus diisi",
                    'max_length' => "Uraian Agunan maksimal 50 karakter"
                ]
            ],
            'IS_SUBSIDIZED' => [
                'rules' => "required|max_length[1]",
                'errors' => [
                    'required' => "IS Subsidized harus diisi",
                    'max_length' => "IS Subsidized maksimal 1 karakter"
                ]
            ],
            'JML_KREDIT' => [
                'rules' => "required|greater_than[0]",
                'errors' => [
                    'required' => "Jumlah Kredit harus diisi",
                    'max_length' => "Jumlah Kredit harus lebih dari 0"
                ]
            ],
            'PEKERJAAN' => [
                'rules' => "required|max_length[2]",
                'errors' => [
                    'required' => "Pekerjaan harus diisi",
                    'max_length' => "Pekerjaan maksimal 2 karakter"
                ]
            ],
            'PENDIDIKAN' => [
                'rules' => "required|max_length[2]",
                'errors' => [
                    'required' => "Pendidikan harus diisi",
                    'max_length' => "Pendidikan maksimal 2 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sqlupdate = "UPDATE MASTER.NASABAH_SIKP SET PENDIDIKAN = ?, PEKERJAAN = ?, KODE_WILAYAH = ?, KODE_POS = ?, MULAI_USAHA = ?, ALAMAT_USAHA = ?, IJIN_USAHA = ?, MODAL_USAHA = ?, JML_PEKERJA = ?, JML_KREDIT = ?, IS_LINKAGE = ?, LINKAGE = ?, URAIAN_AGUNAN = ?, IS_SUBSIDIZED = ?, SUBSIDI_SEBELUMNYA = ? WHERE NO_REGISTER = ?";
            $param_to_bind = [
                ['name' => "PENDIDIKAN", 'value' => $post["PENDIDIKAN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "PEKERJAAN", 'value' => $post["PEKERJAAN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "KODE_WILAYAH", 'value' => $post["KODE_WILAYAH"], 'type' => "DB2_PARAM_IN"],
                ['name' => "KODE_POS", 'value' => $post["KODE_POS"], 'type' => "DB2_PARAM_IN"],
                ['name' => "MULAI_USAHA", 'value' => $post["MULAI_USAHA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "ALAMAT_USAHA", 'value' => $post["ALAMAT_USAHA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "IJIN_USAHA", 'value' => $post["IJIN_USAHA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "MODAL_USAHA", 'value' => $post["MODAL_USAHA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "JML_PEKERJA", 'value' => $post["JML_PEKERJA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "JML_KREDIT", 'value' => $post["JML_KREDIT"], 'type' => "DB2_PARAM_IN"],
                ['name' => "IS_LINKAGE", 'value' => $post["IS_LINKAGE"], 'type' => "DB2_PARAM_IN"],
                ['name' => "LINKAGE", 'value' => $post["LINKAGE"], 'type' => "DB2_PARAM_IN"],
                ['name' => "URAIAN_AGUNAN", 'value' => $post["URAIAN_AGUNAN"], 'type' => "DB2_PARAM_IN"],
                ['name' => "IS_SUBSIDIZED", 'value' => $post["IS_SUBSIDIZED"], 'type' => "DB2_PARAM_IN"],
                ['name' => "SUBSIDI_SEBELUMNYA", 'value' => $post["SUBSIDI_SEBELUMNYA"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REGISTER", 'value' => $post["NO_REGISTER"], 'type' => "DB2_PARAM_IN"]
            ];

            $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Data Calon Debitur SIKP", $post);
            } else {
                $response = respon($update->code, "Error Update MASTER.NASABAH_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function update_rc() {
        $rules = [
            'RC_CALON' => [
                'rules' => "required", 'errors' => ['required' => "RC Calon harus diisi"]
            ], 
            'MSG_CALON' => [
                'rules' => "required", 'errors' => ['required' => "Message Calon harus diisi"]
            ], 
            'NO_REGISTER' => [
                'rules' => "required|max_length[10]",
                'errors' => [
                    'required' => "Nomor Register harus diisi", 
                    'max_length' => "Nomor Register maksimal 10 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();
        
        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else {
            $sqlupdate = "UPDATE MASTER.NASABAH_SIKP SET STS_CALON = '1', RC_CALON = ?, MSG_CALON = ? WHERE NO_REGISTER = ?";
            $param_to_bind = [
                ['name' => "RC_CALON", 'value' => $post["RC_CALON"], 'type' => "DB2_PARAM_IN"],
                ['name' => "MSG_CALON", 'value' => $post["MSG_CALON"], 'type' => "DB2_PARAM_IN"],
                ['name' => "NO_REGISTER", 'value' => $post["NO_REGISTER"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sqlupdate, $param_to_bind);
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Data Calon Debitur SIKP", $post);
            } else {
                $response = respon($update->code, "Error Update MASTER.NASABAH_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

    public function hapus() {
        $rules = [
            'NO_NSB' => [
                'rules' => "required|min_length[8]",
                'errors' => [
                    'required' => "Nomor Nasabah harus diisi",
                    'min_length' => "Nomor Nasabah minimal 8 karakter"
                ]
            ]
        ];
        $post = $this->request->getPost();

        if (!$this->validate($rules)) {
            $error = rtrim(implode(", ", array_values($this->validation->getErrors())), ', ');
            $response = respon("99", $error, $post);
        } else { 
            $sql = "UPDATE MASTER.NASABAH_SIKP SET STS_CALON = '2' WHERE NO_NSB = ?";
            $param_to_bind = [
                ['name' => "NO_NSB", 'value' => $post["NO_NSB"], 'type' => "DB2_PARAM_IN"]
            ];
            $update = $this->prod->query_bind($sql, $param_to_bind);
    
            if ($update->code == "00") {
                $response = respon("00", "Berhasil Mengubah Status Data Calon");
            } else {
                $response = respon($update->code, "Error Update MASTER.NASABAH_SIKP. <br>" . str_replace('"', "'", $update->message));
                $this->status = FALSE;
            }

        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);
        return $this->respond($response);
    }

}

 /* End Of File Calon.php */