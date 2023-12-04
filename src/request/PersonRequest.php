<?php
require_once('Request.php');
require_once('./src/model/Person.php');

class PersonRequest extends Request {

    protected $table = 'hotel.person';
    protected $model = 'Person';

    protected $sql = [
        'document',
        'name',
        'phone'
    ];
    
    protected $pk = [
        'document'
    ];

    protected $required = [
        'document',
        'name',
        'phone'
    ];
    
    public function newclass() {
        return new Person();
    }

}