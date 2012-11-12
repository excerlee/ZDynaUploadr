<?php

/**
 * A colleciton of utility static DB/Model functions for Yii framework
 * @author joeli
 *
 */
class ZDBUtil
{

	/**
	 * Update a CActiveRecord $model using each row data from in $rows, in which each column should be seperated by $delimiter;
	 * if update_only is true, will only update a record that already exists, other wise, will create new record;
	 * @param string $model
	 * @param mix $rows, data rows from the file
	 * @param mix $col_map, map of the table column names to the text/csv file header
	 * @param mix $col_find, list of columns whose value will be used as find value
	 * @param string $update_only, TRUE to update existing rows in table only, FALSE to create new rows;
	 * @param mix $unset, list of columns to ignore during update
	 * @param string $delimiter
	 * @return number of rows updated
	 */
	public function updateModelWithRows($model, $rows, $col_map, $col_find, $update_only=FALSE, $unset=NULL, $delimiter="\t")
	{
		Yii::log("STARTED TO UPDATE INVENTORY ".date('G:i:s') );
	
		try{
			//1. Search for column position & check for necessary column exists;
			$columns = explode($delimiter, $rows[0]);
				
			if ( ($col_position =  ZDBUtil::searchColumnPositions($col_map, $columns)) === NULL)
			{
				Yii::log(__FUNCTION__."FAIL to match columns headers;");
				return NULL;
			}
				
			//2. For each row update the column;
			//skip header for import
			$update_count = 0;
			unset($rows[0]);
			foreach($rows as $row)
			{
				
				$row=trim($row);
				if (empty($row)) { continue; }
				$attrs = explode($delimiter, $row);
	
				$new_values = array();
				foreach ($col_map as $key => $val)
				{
					if (isset($attrs[$col_position[$key]]))
					{
						$new_values[$key] = $attrs[$col_position[$key]];
					}else {
						$new_values[$key] = 0;
						Yii::trace("INFO empty value found for column".$key."with row[%s]". var_export($attrs, true) );
					}
					if (empty($new_values[$key])) $new_values[$key] =0;
				}
	
				$find = array();
				foreach($col_find as $key)
				{
					$find[$key] = $new_values[$key];
				}
					
				if ( ZDBUtil::updateModel($model, $find, $new_values, $update_only, $unset) === TRUE)
					++$update_count;
					
			}
		}catch(Exception $e)
		{
			Yii::log("Exception Caught: ".$e->getTraceAsString());
		}
		return $update_count;
	
	}
	
	
	/**
	 *
	 * @param string $model_name, usually name of the table;
	 * @param array $find, find array used by CActiveRecord->findByAttributes();
	 * @param array $values, values to update the model;
	 * @param boolean $update_only, TRUE->will only update existing records, 
	 * 					FALSE->will create new record if record ,matching $find criteria doesn't already exists; 
	 * @param array $unset, some of the model attributes to unset before saving the model, this is a work-around for 
	 * 		  	attributes where the Yii Model has validation rules, and yet the old drecords in DB don't meet these criteria; 
	 */
	public static function updateModel($model_name, $find, $values, $update_only = false, $unsets=NULL )
	{
		$model = array();
		$action = "";
		if ( ($model = $model_name::model()->findByAttributes($find)) === NULL)
		{	
			/*
			 * model doesn't exist yet, create a new one or return if $update_only is true
			 */
			if ($update_only) {
				Yii::trace("Model NOT FOUND matching".var_export($find, true));
				return;
			}
			$model = new $model_name;
			$action="create";
			//If table has created_at (time stamp) column;
			if ($model->hasAttribute('created_at'))
			{
				$created_col_name = 'created_at';
				$values[$created_col_name] = date("Y-m-d H:i:s");
			}
		}
		else
		{	//model is found, nothing needs to be done
			$action="update";
		}
	
		/*
		 * populate new values;
		 */
		foreach ($values as $k=>$v){ $model->$k = $v; }
	
		/*
		 * unset some of the attributes since old values might not meet the restrictions added later on at Yii Model.
		 */
		if (isset($unsets) )
		{
			foreach ($unsets as $v) { unset( $model[$v]);}
		}
	
		if (!$model->save())
		{
			Yii::log("Fail to ".$action." data for model matching .".var_export($values, true)." error is ".var_export($model->errors, true));
		}else
		{
			Yii::trace("DONE ".$action." data for model matching .".var_export($values, true)." error is ".var_export($model->errors, true));
			return TRUE;
		}	
	}
	
	/**
	 * A more user friendly version of execute()
	 * @param string $sql
	 * @return boolean|Ambigous <number, FALSE>
	 */
	public static function executeDBQuery($sql)
	{
		$rows_affected=Yii::app()->db->createCommand($sql)->execute();
		if ($rows_affected === NULL OR $rows_affected === FALSE OR $rows_affected <0)
		{
			return FALSE;
		}else
		{
			return $rows_affected;
		}
	}

	
	/**
	 *
	 * @param columns to search
	 * @param an array with list of all column names
	 * @return result array with all column names array("sku" => 1);
	 */
	public static function searchColumnPositions($targets, $columns, $checkall = TRUE)
	{
		$result = array();
		$columns = array_map('trim', $columns);
		foreach ($targets as $k=>$t)
		{
			if ( ($p = array_search($t, $columns)) !== false )
			{
				$result[$k]=$p;
			}elseif ($checkall == TRUE)
			{
				Yii::log("Can't find positoin for column ".$k." matched with ". $t);
				return NULL;
			}
		}
		return $result;
	}
	
}