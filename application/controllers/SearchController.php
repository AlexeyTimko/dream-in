<?php

class SearchController extends AbstractController
{
    public function init(){
        parent::init();
        $this->view->breadcrumbs[] = array(
            'title' => "Результаты поиска",
            'href' => "/search",
        );
    }
    public function indexAction()
    {
        $this->view->search = $search = $this->getRequest()->getParam('Search');
        if(is_null($search)){
            $this->_helper->redirector('index', 'index');
        }
        $words = explode(' ', $search);
        $search = array();
        foreach($words as $word){
            if(mb_strlen($word, 'UTF-8') > 4){
                $word = mb_substr($word,0,-2,'UTF-8');
            }
            $search[] = $word.'*';
        }
        $search = join(' ', $search);
        $table = new Application_Model_News_Table();
        $select = $table->getAdapter()->select()
            ->from('News', array('*', "MATCH (Title,Text) AGAINST ('$search' IN BOOLEAN MODE) as relev"))
            ->where("MATCH (Title,Text) AGAINST ('$search' IN BOOLEAN MODE)")
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('relev DESC')
            ->order('Date DESC')
            ->limit(5);
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_NEWS)){
            $select->where('Visible = ?', 1);
        }
        $this->view->news = $table->getAdapter()->fetchAll($select);

        $select = $table->getAdapter()->select()
            ->from('Articles', array('*', "MATCH (Title,Text) AGAINST ('$search' IN BOOLEAN MODE) as relev"))
            ->where("MATCH (Title,Text) AGAINST ('$search' IN BOOLEAN MODE)")
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('relev DESC')
            ->order('Date DESC')
            ->limit(5);
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_ARTICLES)){
            $select->where('Visible = ?', 1);
        }
        $this->view->articles = $table->getAdapter()->fetchAll($select);

        $select = $table->getAdapter()->select()
            ->from('Books', array('*', "MATCH (Title,Description) AGAINST ('$search' IN BOOLEAN MODE) as relev"))
            ->where("MATCH (Title,Description) AGAINST ('$search' IN BOOLEAN MODE)")
            ->join(array('u'=>'Users'),'u.Id = UserId',array('UserName'=>'u.Name'))
            ->order('relev DESC')
            ->order('Date DESC')
            ->limit(5);
        if(!Zend_Auth::getInstance()->hasIdentity()
            || !(Zend_Auth::getInstance()->getIdentity()->User->Rights & Application_Model_User::ALLOW_BOOKS)){
            $select->where('Visible = ?', 1);
        }
        $this->view->books = $table->getAdapter()->fetchAll($select);
    }
}

