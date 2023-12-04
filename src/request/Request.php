<?php
require_once('InterfaceRequest.php');

abstract class Request implements InterfaceRequest {

    public static function __callStatic($name, $arguments) {
        $class = get_called_class(); $func = str_replace('to', '', $name);
        return call_user_func([new $class, $func], ...$arguments);
    }

    public function required($data) {
        $this->validate($this->required, $data);
    }
    
    public function pk($data) {
        $this->validate($this->pk, $data);
    }

    public function validate($validate, $data) {
        foreach ($validate as $key)
            if (!array_key_exists($key, $data))
                die("Dados incorretos, favor informar '$key'");
    }

    public function list() {
        $sql = " SELECT * FROM $this->table ";
        $result = Db::getInstance()->execute($sql);
        return $result;
    }

    public function delete($data) {
        $this->pk($data);
        $val = [];
        foreach ($this->pk as $key) {
            $value = $data[$key];
            $val[] = " $key = '$value' ";
        }

        $val = implode(' AND ', $val);
        $sql = " DELETE FROM $this->table WHERE $val";
        Db::getInstance()->execute($sql, false);
        return 'Registro excluido.';
    }

    public function read($data, $model = false) {
        $this->pk($data);
        $sql = " SELECT * FROM $this->table ";

        $first = true;
        foreach ($this->pk as $value) {
            $val = $data[$value];
            $sql .= $first ? ' WHERE ' : ' AND ';
            $sql .= " $value = '$val' ";
            $first = false;
        }

        $return = Db::getInstance()->execute($sql, false);
        if ($model && $return) {
            $new = $this->newclass();
            $new->fromArray($return);
            $return = $new;
        }
        return $return;
    }

    public function create($data) {
        $this->required($data);
        $model = $this->newclass();
        $model->fromArray($data);

        $val = [];
        foreach ($this->sql as $column) {
            $call = "get$column";
            $value = $model->$call();
            $val[] = $value ? "'$value'" : 'DEFAULT';
        }
        $val = implode(', ', $val);
        $columns = implode(', ', $this->sql);
        $sql = " INSERT INTO $this->table ($columns) VALUES ($val)";
        
        Db::getInstance()->execute($sql, false);
        return 'Registro inserido.';
    }
    
    public function update($data) {
        $this->pk($data);
        $this->required($data);
        $model = $this->newclass();
        $model->fromArray($data);

        $val = [];
        $pk = [];
        foreach ($this->sql as $column) {
            $call = "get$column";
            $value = $model->$call();
            if (in_array($column, $this->pk))
                $pk[] = " $column = '$value' ";
            else {
                $value = $value ?: 'DEFAULT';
                $val[] = " $column = '$value' ";
            }
        }
        $pk = implode(' AND ', $pk);
        $val = implode(', ', $val);
        $sql = " UPDATE $this->table SET $val WHERE $pk";
        
        Db::getInstance()->execute($sql, false);
        return 'Registro alterado.';
    }

}