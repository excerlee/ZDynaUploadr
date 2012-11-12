<?php

/**
 * A dynamic Yii upload form controller with configurable text fields & upload fields
 * To use with ZDynaUploadForm model & zdynaupload view
 * 
 * @author joeli
 *
 */
class ZDynaUploadController extends Controller
{
	private $_actions = array("FBAInventoryHealth", "FBAReplenishReport");
	
	public function actionIndex()
	{
		$this->render('index', array('actions'=>$this->_actions, 'count'=>0));
	}

	/**
	 * 
	 * Handles generic upload form rendering 
	 * @param mix $zdyna_form
	 * 
	 */
	public function upload($zdyna_form)
	{
		$form = $zdyna_form;
				
		if (isset($_POST['ZDynaUploadForm']))
		{
			$form->attributes = $_POST['ZDynaUploadForm'];
			if ($form->validate()) {
				$form->upload_file = CUploadedFile::getInstance($form, 'upload_file');
				$file= dirname(Yii::app()->request->scriptFile) . DIRECTORY_SEPARATOR . 'var'.DIRECTORY_SEPARATOR . $form->upload_file->name;
				$form->upload_file->saveAs($file);

				return $file;
			}
		}
		
		$this->render('zdynaupload', array('form'=>$form));
	}
	
	/**
	 * example action handler for a simple User Table;
	 */
	public function actionUserExample()
	{
		//set up the column name to text file header match
		$column_map = array(
				"user" => 'User Name',
				"first" => 'First Name',
				"last" => 'Last Name',
		);
		//the column name to match existing record
		$column_find = array("user");
		
		//Set up your own batch update upload form
		$form = new ZDynaUploadForm(
					"User Information Upload Form",	//title
					array("uploader"=>"Uploaded By"), //additional fields in the upload form;
					array("uploader"=>array("length", "max"=>25)), //validation rules for the fields
					array( ) );
	
		if ( ($file = $this->upload($form)) !== NULL)
		{
			$content = file_get_contents($file);
			$rows = explode("\n", $content);
			//batch update without creating new records where user doesn't exist already in table
			ZDBUtil::updateModelWithRows("User", $rows, $column_map, $column_find);
			/**
			 * uncomment the following line to to the batch update with creating new records too
			 * ZDBUtil::updateModelWithRows("User", $rows, $column_map, $column_find, FALSE);
			*/
			$this->redirect(array('index', 'update_count'=>$count, 'actions'=>$this->_actions));
		}
	}
	
	
	/**
	 * action handler for Amazon FBA Replenish report upload
	 */
	public function actionFBAReplenishReport()
	{
		$column_map = array(
				"asin" => 'ASIN',
				"sku" => 'SKU',
				"title" => 'Title',
				"sales_7_days" => 'Sales for the last 7 Days',
				"current_inventory" => 'Current Inventory',
				"stock_run_out_in_days" => 'Stock Will Run Out In*',
				"fulfillment_type" => 'Fulfillment Type',
				"inbound_qty" => 'Inbound Quantity',
		);
		$column_find = array("asin", );
		
		$form = new ZDynaUploadForm("FBA Health Report Upload", array(), //labels
				array( ), array( ) );
		
		if ( ($file = $this->upload($form)) !== NULL)
		{
			$content = file_get_contents($file);
			//removed unreadable characters
			$content = preg_replace('/[^\x00-\x7F]+/', '', $content);
			$rows = explode("\n", $content);			
			$count=ZDBUtil::updateModelWithRows("AmzFBAReplenish", $rows, $column_map, $column_find, 
					FALSE, array("status"));
			$this->redirect(array('index', 'update_count'=>$count, 'actions'=>$this->_actions));
		}
	}
}