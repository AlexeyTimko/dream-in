<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function run(){
        require_once('controllers/AbstractController.php');
        $statTable = new Application_Model_Statistic_Table();

        $stat = $statTable->createRow(array(
            'Date'=>date('Y-m-d H:i:s'),
            'Url'=>$_SERVER['REQUEST_URI'],
            'Ip'=>$_SERVER['REMOTE_ADDR'],
            'UserAgent'=>$_SERVER['HTTP_USER_AGENT'],
            'UserId'=>Zend_Auth::getInstance()->hasIdentity()?Zend_Auth::getInstance()->getIdentity()->User->Id:null,
        ));
        $stat->save();

        parent::run();
    }
    protected function _initMysql() {
        $this->bootstrap('db');
        $db = $this->getPluginResource('db')->getDbAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Registry::set('db', $db);
    }
}

