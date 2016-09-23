<?php
/**
 * Created by PhpStorm.
 * User: AxelDreamer
 * Date: 21.04.14
 * Time: 10:03
 */

class Application_Model_Statistic extends Zend_Db_Table_Row_Abstract{
    public function __construct(array $config = array()) {
        $tableClassName = get_class($this).'_Table';
        if(class_exists($tableClassName)) {
            $this->_tableClass = $tableClassName;
        }

        parent::__construct($config);
    }
    public function getViews(){
        $select = $this->getTable()->select()
            ->from('Statistic',array('cnt' => 'COUNT(*)'))
            ->where('DATE(`Date`) = ?', date('Y-m-d'));
        return $this->getTable()->getAdapter()->fetchOne($select);
    }
    public function getUsers(){
        $select = $this->getTable()->select()
            ->from('Statistic',array('cnt' => 'COUNT(DISTINCT `Ip`)'))
            ->where('DATE(`Date`) = ?', date('Y-m-d'));

        return $this->getTable()->getAdapter()->fetchOne($select);
    }
    public function getRegistered(){
        $select = $this->getTable()->select()
            ->from('Statistic',array('cnt' => 'COUNT(DISTINCT `UserId`)'))
            ->where('DATE(`Date`) = ?', date('Y-m-d'))
            ->where('UserId IS NOT NULL');

        return $this->getTable()->getAdapter()->fetchOne($select);
    }
}