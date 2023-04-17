<?php
/*
## File: Custom_model.php
## File Created: Friday, 10th February 2023 10:42:01 am
## Author: ibrahimftkurniawan (ibrahimftk@banksultra.co.id)
## Copyright @ 2023 Ibrahim FT Kurniawan
*/

namespace App\Models;

class Custom_model extends Base_model {

    public $schema;
    public $table;
    public $sys_column;
    public $sql;

    public function __construct($schema = NULL, $table = NULL, $database = NULL, $options = NULL) {
        parent::__construct($database, $options);
        $this->schema = $schema;
        $this->table = $table;
        $this->sys_column();
    }

    private function sys_column() {
        $this->sql = "SELECT NULLS, COLTYPE, LENGTH, NAME, KEYSEQ, GENERATED FROM SYSIBM.SYSCOLUMNS WHERE TBNAME = '" . $this->table . "'  AND TBCREATOR = '" . $this->schema . "'  ORDER BY COLNO";
        $query = $this->db2->query($this->sql);

        $data = array();

        if ($query->code == '00') {
            while ($row = db2_fetch_assoc($query->data)) {
                $data[] = $row;
            }
            $this->db2->is_commit(TRUE, $this->db2->connection);
        } else {
            $this->db2->is_commit(FALSE, $this->db2->connection);
        }


        $this->sys_column = $data;
    }

    public function select(array $column, array $condition = NULL, array $orders = NULL) {

        if (in_array('*', $column)) {
            $this->sql = "SELECT " . implode(",", array_values($column)) . " FROM " . $this->schema . "." . $this->table;
        } else {
            $this->sql = "SELECT \"" . implode("\",\"", array_values($column)) . "\" FROM " . $this->schema . "." . $this->table;
        }

        if (is_array($condition)) {

            foreach ($condition as $key_condition => $value_condition) {

                if ($key_condition == 'BETWEEN') {
                    $value_condition = $condition[$key_condition]['CONDITION'];
                    $condition[$key_condition] = "( " . $condition[$key_condition]['COLUMN'] . " BETWEEN " . $value_condition . ")";
                } else {
                    $condition[$key_condition] = "$key_condition = '" . db2_escape_string($value_condition) . "' ";
                }
            }

            $this->sql .= " WHERE " . implode(" AND ", array_values($condition));
        }

        if (is_array($orders)) {
            foreach ($orders as $key_order => $value_order) {
                $orders[$key_order] = " $key_order $value_order ";
            }

            $this->sql .= " ORDER BY " . implode(" , ", array_values($orders));
        }

        //$query = $this->query($this->sql);
		$query = $this->db2->query($this->sql);
        return $query;
    }

    public function select2(array $column, array $condition = NULL, array $orders = NULL) {

        if (in_array('*', $column)) {
            $this->sql = "SELECT " . implode(",", array_values($column)) . " FROM " . $this->schema . "." . $this->table;
        } else {
            $this->sql = "SELECT \"" . implode("\",\"", array_values($column)) . "\" FROM " . $this->schema . "." . $this->table;
        }

        if (is_array($condition)) {
            $this->sql .= " WHERE ";

            $counter = 0;
            foreach ($condition as $key_condition => $value_condition) {

                if ($key_condition == 'BETWEEN') {
                    $value_condition = $condition[$key_condition]['CONDITION'];
                    $and_condition[$key_condition] = "( " . $condition[$key_condition]['COLUMN'] . " BETWEEN " . $value_condition . ")";
                } elseif ($key_condition == 'CONDITION') {
                    foreach ($condition[$key_condition] as $key_conditions => $value_conditions) {
                        if (is_array($or_value_condition)) {
                            
                        } else {
                            $or_condition[] = "$or_key_condition = '" . db2_escape_string($or_value_condition) . "' ";
                            $counter++;
                        }
                    }
                } else {
                    $and_condition[$key_condition] = "$key_condition = '" . db2_escape_string($value_condition) . "' ";
                }
            }

            if (isset($and_condition)) {
                $this->sql .= "(" . implode(" AND ", array_values($and_condition)) . ")";
            }

            if (isset($or_condition)) {
                $this->sql .= "(" . implode(" OR ", array_values($or_condition)) . ")";
            }
        }

        if (is_array($orders)) {
            foreach ($orders as $key_order => $value_order) {
                $orders[$key_order] = " $key_order $value_order ";
            }

            $this->sql .= " ORDER BY " . implode(" , ", array_values($orders));
        }

        $tes['counter'] = $counter;
        return $tes;
    }

    public function select_like(array $column, array $condition = NULL) {

        $this->sql = "SELECT " . implode(" , ", array_values($column)) . " FROM " . $this->schema . "." . $this->table;

        if (is_array($condition)) {

            foreach ($condition as $key_condition => $value_condition) {

                $condition[$key_condition] = "$key_condition LIKE '%" . db2_escape_string($value_condition) . "%' ";
            }

            $this->sql .= " WHERE " . implode(" AND ", array_values($condition));
        }

        $query = $this->db2->query($this->sql);
        return $query;
    }

    public function insert(array $column) {

        foreach ($column as $key => $value) {
            if ($value === "OPEN_DATE") { /* nanti buatkan function */
                $value = date('Y-m-d');
            } elseif ($value === "CURRENT_TIMESTAMP") {
                $value = date('Y-m-d H:i:s');
            }
            $column[$key] = db2_escape_string($value);
        }

        $this->sql = "INSERT INTO " . $this->schema . "." . $this->table . " (" . implode(" , ", array_keys($column)) . ") VALUES ('" . implode("' , '", array_values($column)) . "')";

        $query = $this->db2->query($this->sql);
        return $query;
    }

    public function update(array $column, array $condition) {

        foreach ($column as $key => $value) {
            $column[$key] = "$key = '" . db2_escape_string($value) . "' ";
        }

        foreach ($condition as $key_condition => $value_condition) {
            $condition[$key_condition] = "$key_condition = '" . db2_escape_string($value_condition) . "' ";
        }

        $this->sql = "UPDATE " . $this->schema . "." . $this->table . " SET  " . implode(" , ", array_values($column)) . " WHERE " . implode(" AND ", array_values($condition));

        $query = $this->db2->query($this->sql);
        return $query;
    }

    public function delete(array $condition) {

        foreach ($condition as $key => $value) {
            $condition[$key] = "$key = '" . db2_escape_string($value) . "' ";
        }

        $this->sql = "DELETE FROM " . $this->schema . "." . $this->table . " WHERE " . implode(" AND ", array_values($condition));

        $query = $this->db2->query($this->sql);
        return $query;
    }

    public function query(string $sql) {
        $this->sql = $sql;
        //$query = $this->query($this->sql);
		$query = $this->db2->query($this->sql);

        return $query;
    }

    public function query_bind(string $sql, array $condition) {
        $this->sql = $sql;
        $query = $this->db2->query_bind($this->sql, $condition);

        return $query;
    }

    public function bind_select_by(array $column, array $condition) {

        $this->sql = "SELECT " . implode(" , ", array_values($column)) . " FROM " . $this->schema . "." . $this->table;

        foreach ($condition as $value_condition) {
            $condition_bind[] = $value_condition['name'] . " = ? ";
        }

        $this->sql .= " WHERE " . implode(" AND ", array_values($condition_bind));

        $query = $this->db2->query_bind($this->sql, $condition);
        return $query;
    }

    public function bind_insert(array $column) {

        foreach ($column as $value) {
            if ($value === "OPEN_DATE") { /* nanti buatkan function */
                $value = date('Y-m-d');
            }
            $column_value[] = db2_escape_string($value['name']);
            $column_bind[] = "?";
        }

        $this->sql = "INSERT INTO " . $this->schema . "." . $this->table . " (" . implode(" , ", array_values($column_value)) . ") VALUES (" . implode(" , ", array_values($column_bind)) . ")";

        $query = $this->db2->query_bind($this->sql, $column);
        return $query;
    }

    public function bind_update(array $column, array $condition) {

        foreach ($column as $value) {
            $column_bind[] = $value['name'] . " = ? ";
        }

        foreach ($condition as $value_condition) {
            $condition_bind[] = $value_condition['name'] . " = ? ";
        }

        $this->sql = "UPDATE " . $this->schema . "." . $this->table . " SET  " . implode(" , ", array_values($column_bind)) . " WHERE " . implode(" AND ", array_values($condition_bind));

        $merger = array_merge($column, $condition);

        $query = $this->db2->query_bind($this->sql, $merger);
        return $query;
    }

    public function select_into(string $sql, array $into = NULL) {

        $this->sql = $sql;

        $query = $this->db2->query($this->sql);

        if ($query->code == '00') {

            $variabel = array();

            $data = $this->db2->fetch_array($query->data);

            /* Jika data tidak ditemukan */
            if (count($data) == 0) {
                $query->code = '01';
                $query->message = "Data tidak ditemukan saat proses SELECT INTO | SQL $sql ";
                goto akhir;
            }

            /* Jika data ditemukan dan lebih dari 1 row */
            if (count($data) > 1) {

                /* ambil array paling awal */
                if (is_array($data[0])) {
                    $data = $data[0];
                }
            }

            if ((count($data)) == (count($into))) {
                for ($i = 0; $i < count($into); $i++) {
                    $variabel[$i] = "$" . $into[$i] . "='" . $data[$i] . "';";
                }
            } else {
                $query->code = '01';
                $query->message = 'Jumlah variabel tidak sesuai dengan jumlah data yang diselect ';
            }

            $query->data = implode('', $variabel);
        }

        akhir:
        return $query;
    }

}