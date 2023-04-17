<?php
/*
## File: MyModels.php
## File Created: Friday, 10th February 2023 3:50:29 pm
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Models;

use CodeIgniter\Model;

class MyModels extends Model {
    
    public function __construct() {
        $this->db = \Config\Database::connect('default', false);
        
    }
    
    public function insert_data($tabel, $data) {
        try {
            $this->db->table($tabel)->insert($data);
            $response = ['code' => "00", 'message' => "Berhasil Menambahkan Data..!!", 'data' => $data];

        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Insert $tabel. <br>" . $th->getMessage(), 'data' => $data];
        }

        return $response;
    }

    public function update_data($tabel, $data, $where) {
        try {
            $this->db->table($tabel)->update($data, $where);
            if ($this->db->affectedRows() > 0) {
                $response = ['code' => "00", 'message' => "Berhasil Mengubah Data..!!", 'data' => array_merge($data, $where)];
            } else {
                $response = ['code' => "10", 'message' => "Tidak Ada Data Yang Diubah..!!", 'data' => $this->db->getLastQuery()];
            }

        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Update $tabel. <br>" . $th->getMessage(), 'data' => array_merge($data, $where)];
        }

        return $response;
    }

    public function delete_data($tabel, $where) {
        try {
            $this->db->table($tabel)->delete($where);
            if ($this->db->affectedRows() > 0) {
                $response = ['code' => "00", 'message' => "Berhasil Menghapus Data..!!", 'data' => $where];
            } else {
                $response = ['code' => "10", 'message' => "Tidak Ada Data Yang Dihapus..!!", 'data' => null];
            }
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Delete $tabel. <br>" . $th->getMessage(), 'data' => $where];
        }

        return $response;
    }

    public function get_all($tabel) {
        try {
            $get = $this->db->table($tabel)->get();
            $data = $get->getResultArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];    
            }
            
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select $tabel. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function get_all_where($tabel, $where) {
        try {
            $get = $this->db->table($tabel)->getWhere($where);
            $data = $get->getResultArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];
            }

        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select $tabel. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function get_row($tabel, $where) {
        try {
            $get = $this->db->table($tabel)->getWhere($where);
            $data = $get->getRowArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];
            }

        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select $tabel. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function custom_select_all($sql) {
        try {
            $get = $this->db->query($sql);
            $data = $get->getResultArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];    
            }
            
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function custom_select_where($sql, $where) {
        try {
            $get = $this->db->query($sql, $where);
            $data = $get->getResultArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];    
            }
            
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function custom_select_row($sql, $where) {
        try {
            $get = $this->db->query($sql, $where);
            $data = $get->getRowArray();
            if (!empty($data) && sizeof($data) > 0) {
                $response = ['code' => "00", 'message' => "Success", 'data' => $data];
            } else {
                $response = ['code' => "10", 'message' => "Data Tidak Ditemukan..!!", 'data' => null];    
            }
            
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Error Select. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

    public function custom_query($sql) {
        try {
            $this->db->query($sql);
            $response = ['code' => "00", 'message' => "Sukses Eksekusi Query..!!", 'data' => null];
        } catch (\Throwable $th) {
            $response = ['code' => "99", 'message' => "Query Error. <br>" . $th->getMessage(), 'data' => null];
        }

        return $response;
    }

}