<?php
/*
## File: DB2.php
## File Created: Friday, 10th February 2023 10:38:04 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Libraries;

use App\Libraries\Encryption;

class DB2 {
    /*
     * Private
     */

    private $hostname;
    private $database;
    private $username;
    private $password;
    private $port;
    private $options = array(
        'autocommit' => DB2_AUTOCOMMIT_OFF,
        'DB2_ATTR_CASE' => DB2_CASE_UPPER,
        'i5_commit' => DB2_I5_TXN_SERIALIZABLE
    );

    /*
     * Public 
     */
    public $connection;
    public $response;

    public function __construct($param = NULL, $options = NULL) {
        helper('backend');

        if ($param != NULL) {

            $encryption = new Encryption();

            $file = WRITEPATH . "config/" . $param . ".ini";

            if (file_exists($file)) {

                $file_config = parse_ini_file($file);

                $this->hostname = $encryption->decode($file_config["hostname"]);
                $this->username = $encryption->decode($file_config["username"]);
                $this->password = $encryption->decode($file_config["password"]);
                $this->database = $encryption->decode($file_config["database"]);
                $this->port = $encryption->decode($file_config["port"]);

                if ($options != NULL) {
                    $this->options = $options;
                }

                $conn_string = "DATABASE=" . $this->database . ";HOSTNAME=" . $this->hostname . ";PORT=" . $this->port . ";PROTOCOL=TCPIP;UID=" . $this->username . ";PWD=" . $this->password . ";";
                // echo $conn_string;
                $conn = db2_pconnect($conn_string, '', '', $this->options);

                if ($conn) {
                    $this->connection = $conn;
                    $response = respon('00', 'Connection Active');
                } else {
                    $response = respon('99', 'Failed Connection ' . db2_conn_errormsg());
                }
            } else {
                $response = respon('99', 'File inisialisasi database tidak ditemukan');
            }
        } else {
            $response = respon('99', 'Database belum didefinisikan');
        }

        $this->response = $response;
    }

    public function query_exec($sql) {
        $query = @db2_exec($this->connection, $sql);

        if (db2_stmt_error() != 0) {
            $response = respon('97', db2_stmt_error() . " - " . db2_stmt_errormsg());
        } else {
            $response = respon('00', 'Query true', $query);
        }

        return (object) $response;
    }

    public function query($sql) {
        $stmt = db2_prepare($this->connection, $sql);
        $query = @db2_execute($stmt);

        if ($query === TRUE) {
            $response = respon('00', 'Query true', $stmt);
        } else {
            $response = respon('97', db2_stmt_error() . " - " . db2_stmt_errormsg());
        }

        return (object) $response;
    }

    public function query_bind($sql, $condition) {

        $stmt = db2_prepare($this->connection, $sql);
        $number = 1;
        for ($i = 0; $i < count($condition); $i++) {
            ${$condition[$i]['name']} = $condition[$i]['value'];
            if (is_string($condition[$i]['value'])) {
                /* Edited By IFTK 24 Juni 2021 */
                ${$condition[$i]['name']} = db2_escape_string($condition[$i]['value']);
                ${$condition[$i]['name']} = str_replace("''", "'", ${$condition[$i]['name']});
                /* End Edited By IFTK 24 Juni 2021 */
            }
            $exec = @db2_bind_param($stmt, $number, $condition[$i]['name'], constant($condition[$i]['type']));
            $number++;
            if (!$exec) {
                goto error;
            }
        }

        $exec = @db2_execute($stmt);
        if ($exec) {
            $response = respon('00', 'Query true', $stmt);
        } else {
            error:
            $response = respon('97', db2_stmt_error() . " - " . db2_stmt_errormsg() . ' - ');
        }

        return (object) $response;
    }

    public function fetch_assoc($data, $blob = NULL) {
        $rows = array();

        while ($row = db2_fetch_assoc($data)) {
            if (is_array($row)) {

                if ($blob != null) {
                    if (is_array($blob)) {
                        foreach ($blob as $key => $value) {
                            $row[$value] = base64_encode($row[$value]);
                        }
                    }
                }

                $rows[] = $row;
            } else {
                break;
            }
        }

        return $rows;
    }

    public function fetch_array($data, $blob = NULL) {
        $rows = array();

        while ($row = db2_fetch_array($data)) {
            if (is_array($row)) {

                if ($blob != null) {
                    if (is_array($blob)) {
                        foreach ($blob as $key => $value) {
                            $row[$value] = base64_encode($row[$value]);
                        }
                    }
                }
                $rows[] = $row;
            } else {
                break;
            }
        }

        if (count($rows) == 1) {
            $rows = $rows[0];
        }

        return $rows;
    }

    public function is_commit($status_sql, $connection)
    {
        if ($status_sql) {
            return db2_commit($connection);
        } else {
            return db2_rollback($connection);
        }
    }

    public function close() {
        db2_close($this->connection);
    }

    /* Added By IFTK 30 April 2021 */

    public function num_rows($resource) {
        return db2_num_rows($resource);
    }

    public function field_name($resource) {
        $field_name = [];
        for ($i = 0; $i < db2_num_fields($resource); $i++) {
            $field_name[] = db2_field_name($resource, $i);
        }

        return $field_name;
    }

    public function escape_string($field) {
        return db2_escape_string($field);
    }

    /* End Added By IFTK 30 April 2021 */
}