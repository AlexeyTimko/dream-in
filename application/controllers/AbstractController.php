<?php

class AbstractController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->breadcrumbs = array(
            array(
                'title' => "Главная",
                'href' => "/",
            ),
        );
        $this->view->auth = $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()
            && ($auth->getIdentity()->User->Rights & Application_Model_User::ALLOW_STATISTIC)){
            $this->view->stats = new Application_Model_Statistic();
        }

//        $mp3List = glob(MP3_PATH."/*.mp3");
//        if(count($mp3List)){
//            $i = rand(0, count($mp3List)-1);
//            $this->view->mp3 = '/mp3/'.basename($mp3List[$i]);
//        }
        Zend_View_Helper_PaginationControl::setDefaultViewPartial ( 'paginator.phtml' );
        $this->view->title = 'Мир снов. ';
        $this->view->meta = 'Осознанные сновидения ОС фаза phase lucid dream дневник сновидений карта сновидений Картография сновидений ';
        $this->view->seoText = 'Осознанные сновидения (ОС). Этот сайт посвящен осознанным сновидениям и всему что с этим связано. Все чаще люди обращают свое внимание на данный феномен. Мы постарались собрать как можно больше материалов по этой теме. Статьи по осознанным сновидениям, книги по осознанным сновидениям. Все это можно найти на сайте. Если есть желание поделиться знаниями, то можно добавить на сайт статью или книгу. Так же, на сайте Вы можете вести свой дневник сновидений.';
    }
    public function paginatorPrepare($select, $itemsPerPage = 10){
        $paginator = Zend_Paginator::factory($select);

        $page = $this->_request->getParam('page', 1);

        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
    }
    public function rus2translit($string) {

        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        return strtr($string, $converter);

    }

    public function str2url($str) {
        // переводим в транслит
        $str = $this->rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        $str = preg_replace('/[\-]{2,}/','-',$str);
        return $str;
    }

    /**
     * @param string $title
     * @param Zend_Db_Table_Abstract $table
     * @return string
     */
    public function getAlias($title, Zend_Db_Table_Abstract $table){
        $alias = $this->str2url($title);
        $select = $table->getAdapter()->select()->from($table->info('name'), array('cnt'=>'COUNT(Alias)'))->where('Alias LIKE ?', $alias."%");
        $matches = $table->getAdapter()->fetchOne($select);
        if($matches > 0){
            $alias .= $matches;
        }
        return $alias;
    }
}

