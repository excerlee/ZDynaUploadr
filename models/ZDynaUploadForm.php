<?php
/**
 * 
 * A dynamic Yii upload form model with configurable text fields & upload fields
 * To use with ZDynaUploadController & zdynaupload view
 * 
 * @author joeli
 *
 */
class ZDynaUploadForm extends CFormModel {
    
	//default form title
	public $title = "Upload Form";
	//default form variable values
	private $_values = array("upload_file"=>"", "editor"=>"");
	//default list of form text fields
	private $_text_types = array("editor");
	//default form upload fields
	private $_file_types = array("upload_file");

	//attribute labels used for form display
    private $_labels = array(
    		"upload_file"=>"File Name To Upload",
    		"editor"=>"Your Email ID",);
    
    //validation rules for attributes
    private $_rules = array(
            array('upload_file', 'file', 'types' => 'txt, sku, csv', 'maxSize'=>6097152),
        	array('editor', 'length', 'min'=>3),
        );
    
    public function __construct($title, $labels, $rules = array(), $text_types = NULL, $overwrite =  FALSE)
    {
    	$this->title = $title;
    	if ($overwrite === FALSE)
    	{
    		$this->set('_labels', array_merge($this->_labels, $labels));
    		$this->set('_rules', array_merge($this->_rules, $rules));
    		$this->set('_values', array_merge($this->_values, $labels));
    		$this->set('_text_types', array_merge($this->_text_types , $text_types) );    		
    	}else
    	{
    		$this->_labels = $labels;
    		$this->_rules = $rules;
    		$this->_text_types = $text_types;
    	}	
    }
    
    public function getTextVars()
    {
    	return $this->_text_types;
    }
    
    public function __get($name)
    {
    	if (array_key_exists($name, $this->_values)) {
    		return $this->_values[$name];
    	}
    	return NULL;
    }
    
    public function __set($name, $value)
    {
    	$this->_values[$name] = $value;
    }
    
    public function attributeLabels()
    {
    	return $this->_labels;
    }
    
    public function rules() {
    	return $this->_rules;
    }
}