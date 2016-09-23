<?php
/**
 * Created by PhpStorm.
 * User: AxelDreamer
 * Date: 21.04.14
 * Time: 10:03
 */

class Application_Model_User extends Zend_Db_Table_Row_Abstract{
    protected $_table = 'Users';
    protected $_tableClass = 'Application_Model_User_Table';
    const ALLOW_NEWS = 1;
    const ALLOW_ARTICLES = 2;
    const ALLOW_BOOKS = 4;
    const ALLOW_STATISTIC = 8;
}