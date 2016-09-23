<?php
/**
 * Created by PhpStorm.
 * User: AxelDreamer
 * Date: 21.04.14
 * Time: 10:03
 */

class Application_Model_Comment extends Zend_Db_Table_Row_Abstract{
    const TYPE_NEWS = 1;
    const TYPE_ARTICLES = 2;
    const TYPE_BOOKS = 3;
    const TYPE_DREAMS = 4;
    const TYPE_MAPS = 5;
    public static $typeControllers = array(
        self::TYPE_NEWS => 'index',
        self::TYPE_ARTICLES => 'articles',
        self::TYPE_BOOKS => 'books',
        self::TYPE_DREAMS => 'my-dreams',
        self::TYPE_MAPS => 'dream-map',
    );
    public function __construct(array $config = array()) {
        $tableClassName = get_class($this).'_Table';
        if(class_exists($tableClassName)) {
            $this->_tableClass = $tableClassName;
        }

        parent::__construct($config);
    }
    public function getList($itemId, $type){
        $select = $this->getTable()->getAdapter()->select()
            ->from(array('c'=>'Comments'))
            ->join(array('u'=>'Users'), 'u.Id = UserId', array('UserName'=>'u.Name'))
            ->where('c.ItemId = ?', $itemId)
            ->where('c.ItemType = ?', $type);

        return new Application_Model_Tree($this->getTable()->getAdapter()->fetchAll($select));
    }
}