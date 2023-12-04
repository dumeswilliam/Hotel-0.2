<?php

class Base {

    protected static function props() {
        $reflect = new ReflectionClass(get_called_class());
        return array_map(function ($prop) { return $prop->name; }, $reflect->getProperties());
    }

    public function toArray() {
        $data = [];
        foreach (self::props() as $prop) {
            $get = "get$prop";
            $data[$prop] = $this->$get();
        }
        return $data;
    }

    public function fromArray($data) {
        foreach (self::props() as $prop) {
            $get = "set$prop";
            if (array_key_exists($prop, $data))
                $this->$get($data[$prop]);
        }
    }

    public function __call($name, $arguments) {
        $name = strtolower($name);
        if (property_exists($this, substr($name, 3))) {
            if (substr($name, 0, 3) == 'set')
                $this->{substr($name, 3)} = reset($arguments);
            else if (substr($name, 0, 3) == 'get')
                return $this->{substr($name, 3)};
            else 
                die("Método não encontrado: $name");
        }
        return $this;
    }

}