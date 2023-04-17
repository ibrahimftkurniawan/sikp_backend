<?php

namespace App\Controllers;

use App\Models\Custom_model;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\Encryption;

class Home extends BaseController {

    use ResponseTrait;

    public function index() {
        // return view('welcome_message');
        return view('config/setup_database');
    }

    public function config() {
        $val = $this->validate([
            'filename' => 'required',
            'hostname' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'database' => 'required',
        ]);

        if (!$val) {

            $validasi = array_values($this->validator->getErrors());

            echo '<script>alert("' . $validasi[0] . '")</script>';
        } else {
            $converter = new Encryption;

            $filename = $this->request->getPost('filename');
            $loc = $this->request->getPost('hostname');
            $port = $this->request->getPost('port');
            $user = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $database = $this->request->getPost('database');
            $key = $this->request->getPost('key');

            $arr["hostname"] = $converter->encode($loc);
            $arr["port"] = $converter->encode($port);
            $arr["username"] = $converter->encode($user);
            $arr["password"] = $converter->encode($password);
            $arr["database"] = $converter->encode($database);
            $arr["key"] = $converter->encode($key);

            $converter->write_php_ini($arr, WRITEPATH . "config/" . $filename . ".ini");

            echo '<script>
                alert("Proses inisialisasi selesai");
                window.location.href = "' . site_url() . '";
            </script>';
            
        }
    }

    public function see_config($param) {
        $encryption = new Encryption();

        $file = WRITEPATH . "config/" . $param . ".ini";

        $file_config = parse_ini_file($file);

        $hostname = $encryption->decode($file_config["hostname"]);
        $username = $encryption->decode($file_config["username"]);
        $password = $encryption->decode($file_config["password"]);
        $database = $encryption->decode($file_config["database"]);
        $port = $encryption->decode($file_config["port"]);
        $key = $encryption->decode($file_config["key"]);

        $response = array(
            "hostname" => $hostname,
            "username" => $username,
            "password" => $password,
            "database" => $database,
            "port" => $port,
            "key" => $key
        );

        echo '<pre>';
        print_r($response);
    }

    public function test() {
        $this->prod = new Custom_model();

        $sql = "SELECT * FROM MASTER.SYSTEM_HOST WHERE THE_KEY = ?";
        $param_to_bind = [
            ['name' => "THE_KEY", 'value' => '999', 'type' => "DB2_PARAM_IN"],
        ];
        $get = $this->prod->query_bind($sql, $param_to_bind);
        if ($get->code == "00") {
            $data = $this->prod->db2->fetch_assoc($get->data);
            $response = respon("00", "Success", $data);
        } else {
            $response = respon($get->code, $get->message);
        }

        $this->prod->db2->is_commit($this->status, $this->prod->db2->connection);

        return $this->respond($response);
    }
}
