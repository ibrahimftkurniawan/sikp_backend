<?php
/*
## File: Base_model.php
## File Created: Friday, 10th February 2023 10:43:00 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Models;

use App\Libraries\DB2;
use CodeIgniter\API\ResponseTrait;

class Base_model extends \CodeIgniter\Controller {

    use ResponseTrait;

    public $db2;
    public $status_sql = TRUE;

    public function __construct($database = NULL, $options = NULL) {

        if ($database == NULL) {
            $database = $_SERVER['DATABASE'];
        }

        $this->db2 = new DB2($database, $options);
        if ($this->db2->response['code'] !== '00') {
            header('Content-Type: application/json');
            echo json_encode($this->db2->response);
            die();
        }
    }

}