<?php
require_once('Request.php');
require_once('PersonRequest.php');
require_once('./src/model/Check.php');

class CheckRequest extends Request {

    protected $table = 'hotel.check';
    protected $model = 'Check';

    protected $sql = [
        'document',
        'check_in',
        'check_out',
        'garage',
        'paid'
    ];
    
    protected $pk = [
        'id'
    ];

    protected $required = [
        'document',
        'check_in'
    ];
    
    public function newclass() {
        return new Check();
    }

    public function read($data) {
        if (array_key_exists('search', $data)) {
            $search = $data['search'];
            $result = Db::getInstance()->execute(" SELECT $this->table.* FROM $this->table LEFT JOIN hotel.person USING (document) WHERE person.document ILIKE '%$search%' OR name ILIKE '%$search%' OR phone ILIKE '%$search%' ", false);
        } else {
            $result = parent::read($data);
        }

        if ($result) {
            $person = new PersonRequest();
            $result['person'] = $person->read(['document' => $result['document']]);
            $result['person']['total'] = Db::getInstance()->execute(" SELECT SUM(paid) AS total FROM $this->table WHERE document = '{$result['document']}' ", false);
        }

        return $result;
    }

}