<?php
/**
 * Created by PhpStorm.
 * User: Timko
 * Date: 22.09.2014
 * Time: 13:15
 */

class Application_Model_Tree {
    private $_nodeTemplate = '<li id="comment_%%Id%%"><span class="date">%%Date%%</span><br/><span class="date">Добавил: <strong>%%UserName%%</strong></span><div class="p">%%Text%%<br/><a href="/comment/new/type/%%ItemType%%/id/%%ItemId%%/parent/%%Id%%" title="Комментировать" class="comment_img hovered popup_link"></a><a href="/comment/new/type/%%ItemType%%/id/%%ItemId%%/parent/%%Id%%" title="Цитировать" class="quote_img hovered popup_link"></a></div>%%Children%%</li>';
    private $_template = '<ul>%%List%%</ul>';
    private $_data = array();
    private $_isBuilt = false;
    private $_html = '';
    public function __construct($rows){
        foreach($rows as $row){
            $this->_data[$row->Id] = $row;
        }
    }

    /**
     * @param string $nodeTemplate
     */
    public function setNodeTemplate($nodeTemplate)
    {
        $this->_nodeTemplate = $nodeTemplate;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }
    public function __toString(){
        if(!empty($this->_html)){
            return $this->_html;
        }
        if(!empty($this->_data)){
            if(!$this->_isBuilt){
                $this->_buildTree();
            }
        }

        return $this->_html = $this->_getHtml();
    }
    public function hasData(){
        return !empty($this->_data);
    }
    protected function _getHtml($data = array()){
        if(empty($data)){
            $data = $this->_data;
        }
        $html = '';
        if(!empty($data)){
            $list = '';
            foreach($data as $node){
                $template = $this->_nodeTemplate;
                foreach($node as $key => $val){
                    if($key == 'Children'){
                        continue;
                    }
                    $val = str_ireplace("\r\n", '<br/>', $val);
                    $val = preg_replace("/\[q\](.*)\[\/q\]/ui", '<div class="quoted">$1</div>', $val);

                    $template = str_ireplace('%%'.$key.'%%', $val,$template);
                }
                $template = str_ireplace('%%Children%%', !empty($node->Children)?$this->_getHtml($node->Children):'',$template);
                $list .= $template;
            }
            $html = str_ireplace('%%List%%', $list, $this->_template);
        }
        return $html;
    }
    protected function _buildTree(){
        foreach($this->_data as $node){
            if(!empty($node->ParentId)){
                $this->_data[$node->ParentId]->Children[$node->Id] = $node;
            }
        }
        foreach($this->_data as $node){
            if(!empty($node->ParentId)){
                unset($this->_data[$node->Id]);
            }
        }
        $this->_isBuilt = true;
    }
} 