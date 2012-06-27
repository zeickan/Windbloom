<?php


class FORMS {
	

	public function INPUT( $name, $type = 'text', $attr = NULL , $maxlength = false ){
		
		$atr = array();

		$atr[] = ($type == 'text')?'type="text"':' type="'.$type.'"';

		if( $attr ):
			
			foreach ($attr as $key => $value) {
				
				$atr[] = $key.'="'.$value.'"';

			}

		endif;


		return '<input name="'.$name.'" id="'.$name.'" '.join(" ",$atr).' />';

	}

	public function TEXTAREA( $name, $text = '', $attr = NULL , $maxlength = false ){
		
		$atr = array();

		if( $attr ):
			
			foreach ($attr as $key => $value) {
				
				$atr[] = $key.'="'.$value.'"';

			}

		endif;


		return '<textarea name="'.$name.'" id="'.$name.'" '.join(" ",$atr).'>'.$text.'</textarea>';

	}

	public function SELECT( $name, $options = NULL, $attr = NULL ){
		
		$atr = array();

		if( $attr ):
			
			foreach ($attr as $key => $value) {
				
				$atr[] = $key.'="'.$value.'"';

			}

		endif;


		return '<select name="'.$name.'" id="'.$name.'" '.join(" ",$atr).'> '.$options.'</select>';		
		
	}

}

/*
*********************
* Build Form Class  *
*    Version 1.0    *
*   March 03, 2011  *
*     Updated on    *
*   March 06, 2011  *
*********************

This is the 4th class I've ever made. (Razvan)

This class was UPDATED:

- tabindex was added - (thanks to David Hyland)
- surrounding <div> for all fields no matter if they are in a fieldset or not - (thanks to David Hyland)
- extracting only the fields we need from database instead of using * (all) - (thanks to David Hyland)
- added IDs (= fieldname or fieldname_number for radio buttons) for all fields - (thanks to David Hyland)
- added a function to clean the variables which are used in a mysql query (to prevent sql injection)
- added htmlentities function for the fields we get from database (to prevent html errors in the form)


This class will help you build forms.
After that, you can use CSS to make it look better.

==================================================================================================================================
This is how it works:

	$form = new BuildForm();

1. Set the form attributes (name, action, method...) - required

	$form_attributes = array("name"		=> "two",
							"method"	=> "post",
							"id"		=> "two",
							"enctype"	=> 'multipart/form-data',
							"action"	=> "do.php");
	
	$form -> set_form($form_attributes);

2. Add fieldsets (<fieldset id = "OnlyTheLettersFromTheName"><legend>Name</legend> .. </fieldset>) - optional

	$element = array("Name", "Name Name");
	$form -> add_fieldset($element);
	
3. Add info under fields or * to the label  - optional

	A. info under the field <div {style} >some info</div>
	
		$fieldsinfo = array("fieldname" => "some info", ...)
		$style		= array("stye" => "color: red;");
		$form -> required($fieldsinfo, $style);
		
	B. add * to the label <label for="fieldname">Sometext <span {style} >*</span>
	
		$fieldsinfo = array("fieldname1", "fieldname2", ....)
		$style		= array("stye" => "color: red;");
		$form -> required($fieldsinfo, $style);

4. Add input fields / textarea / select / checkbox / radio etc. - required

		$fieldset		= the name of the fieldset where the field will be included -> optional	
		$field_type		= one of this values "submit", "text", "password", "hidden", "textarea", 
							"select", "select_from_db", "radio", "radio_from_db", "checkbox", "file"							
		$field_name		= the name of the field -> required							
		$field_style	= the style of the field (style, class, onclick, onmouseover, size ... etc) -> optional
		$field_label	= the label of the field -> optional
		$field_values	= default value for input fields -> optional, required for radio / checkbox / select
		$selectedvalue	= only for radio / checkbox / select  -> optional
		$form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
		
	For selectbox / radio buttons $field_values = array( "value" => "text", "value2" => " text 2" )
	and it will generate: 
		<option value="value">text</option>
		<div><input  type = "radio" name = "radioname" value = "value"  />text</div>
				
	If you want to generate a selectbox (or radio buttons) with values from database:
		$field_type = "select_from_db" or "radio_from_db";
	
	The $field_values must be like this:
		$field_values	= array("table" => "yourTableFromDatabase", "value_field" => "aFieldFromDatabase", "text_field" => "aFieldFromDatabase");

		*table and value_field are required

5. Generate the form - required
		
		$divstyle = the style / class / id of the div which includes the form (optional) 
		$divstyle = array("class" => "classname");
		echo $form -> output($divstyle) ; 
	
6. You can also use this class to generate single field (no form) like this
		
		$single			= new BuildForm();
		$fieldset		= "";							
		$field_type		= "text";							
		$field_name		= "username";							
		$field_style	= "";							
		$field_label	= "username";
		$field_values	= "";
		$selectedvalue	= "";
		$single -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);

		And then generate it with:
		echo $single -> get_field(); //this will get the last added field
		
		Or you can use
		echo $single -> get_field("0"); // the 1st field 
		echo $single -> get_field("1"); // the 2nd field ... 
		
==================================================================================================================================

*/	



class BuildForm{

private $errors			= array("0");
private $err_messages	= "";		//error messages will be put here
private $form			= "";		//the form with attributes
private $fields			= array();	//the generated html of fields with no fieldset
private $fieldset_list	= array();	//the names of fieldsets
private $fieldsets		= array();	//the generated html of fieldsets
private $required_fields= array();	//info under the fields
private $required_style;


	private function show_error($err, $show=TRUE, $echo=FALSE){
	/*
	== this function outputs error messages
	*/
		$errors = array("0" => "Form attributes must be set in an array ('name' => 'formname', 'action' => 'do.php').",
						"1" => "Wrong field type or field name wasn't set. One field was not displayed!",
						"2" => "Field values needs to be an array for this type of input. One field was not displayed!",
						"3" => "Error while trying to read from database!",
						"4" => "The form could not be generated. Check the settings!",
						"5" => "This form has no input fields!",
						"6" => "Wrong field values for the &laquo;Select from database&raquo; box! Must be an array ('table' => 'tablename', 'value_field' => 'pk' , 'text_field' => 'somefield', 'order' => 'field')",
						"7" => "Value field for the &laquo;Select from database&raquo; box wasn't set!",
						"8" => "Value field for the &laquo;Radio from database&raquo; section wasn't set!",
						"9" => "Wrong field values for the &laquo;Radio from database&raquo; section! Must be an array ('table' => 'tablename', 'value_field' => 'pk' , 'text_field' => 'somefield', 'order' => 'field')",
						"10"=> "To display fields requirements you must use an array ('fieldname' => 'some info') or to display that the field is required (no other info): array('fieldname', 'fieldname2', 'fieldname3')",
						"11"=> "The style of a field was not used. The style must be an array('size' => '20', 'class' => 'classname')",
						"12"=> "There is no field to display. First, you need to use add_field() !"
						); 

		if (array_key_exists($err,$errors)){
			$show = $errors[$err];
			}else{
			$show = "Unknown error."; //Unknown error.
		}
				
		$err = '<div style = "color: #000000;font-weight:bold; background-color: #EBEBEB;font-family:Arial; font-size:10px; border: 4px solid;margin: 10px 0px; padding:10px;"><u>BuildForm Class</u>: '.$show."</div>\n";

		if($echo){return $err;}
		if($show){$this -> err_messages .= $err;}
	}


	// ### function to prevent SQL injection
	private function clean($str) {
			$str = @trim($str);
			if(get_magic_quotes_gpc()) {
				$str = stripslashes($str);
			}
			return mysql_real_escape_string($str);
	}
	

	public function add_fieldset($element){
	/*
	== this function adds fieldsets
	*/
		if(is_array($element)){
			
			foreach ($element as $value){
				$this -> fieldset_list[] = $value;}
				
			}else{
			$this -> fieldset_list[] = $element;
		}
	
	
	}


	public function required($fields, $divstyle){
	/*
	== this function adds info under the fields
	*/
		if (is_array($fields)){
		
			$this -> required_fields = $fields;
		
			} else {
			
			$this -> show_error("10", TRUE) ;
			
		}
	
	
		if(is_array($divstyle)){
			$style = "";
			foreach ($divstyle as $attr => $value){
				$style .=" ".$attr." = ".'"'.$value.'"';}
				
			}else{
			$style = $divstyle;
		}
	
		$this -> required_style = $style;
	
	}

	
	
	public function set_form($form_attributes){
	/*
	== this function sets form attributes
	*/
		if(is_array($form_attributes)){
			$attributes="";
			foreach ($form_attributes as $attr => $value){
				$attributes .=" ".$attr." = ".'"'.$value.'"';}
							
			$this -> form = "\t<form $attributes >\n{content}\n\t</form>\n";
											
			} else {
			//form attributes are not in an array
			$this -> errors[] = 1;
			$this -> show_error("0", TRUE) ;
		}
		

	}


	private function seldata($table, $order, $value_field, $text_field, $selected){
	/*
	== this function generates a selectbox from database
	*/
		$sell		= "\t\t".'<option value = "" style = "color: #444; border-bottom: 1px dotted;">Select:</option>'."\n";
		
		// ### clean variables to prevent sql injection
		$value_field	= $this -> clean($value_field);
		$text_field		= $this -> clean($text_field);
		$table			= $this -> clean($table);
		$order			= $this -> clean($order);		
		
		// ### extract specfic fields only
		$query		= "select `$value_field` as value, `$text_field` as text from `$table` order by `$order`";
		$results	= @mysql_query($query); //or die(mysql_error());
						
			if($results){
			
				while ($row = mysql_fetch_array($results)){
				
					// ### added htmlentities function for the fields we get from database
					$v1		= (isset($row['value'])	? htmlentities($row['value'], ENT_QUOTES,  'UTF-8')	: "");
					$v2		= (isset($row['text'])	? htmlentities($row['text'] , ENT_QUOTES,  'UTF-8')	: $v1);
					if ($v1 != ""){
						if($v1 == $selected){
							$check = ' selected = "selected"';
							} else {
							$check = "";
						}
						$sell  .= "\t\t".'<option value = "'.$v1.'"'.$check.'>'.$v2."</option>\n";
					}
				}
				
				
				
				} else {
				$sell = "";
				$this -> show_error("3", TRUE) ;
				
			}
		
		return $sell;
	}
	
	
	
	
	// ### added field_name param
	private function radiodata($htmlstart, $htmlend, $table, $order, $value_field, $text_field, $field_name, $selected){
	/*
	== this function generates radio buttons from database
	*/

		$sell		= "";
		
		
		// ### clean variables to prevent sql injection
		$value_field	= $this -> clean($value_field);
		$text_field		= $this -> clean($text_field);
		$table			= $this -> clean($table);
		$order			= $this -> clean($order);
		
		// ### extract specfic fields only
		$query		= "select `$value_field` as value, `$text_field` as text from `$table` order by `$order`";
		$results	= @mysql_query($query); //or die(mysql_error());
						
			if($results){
				$sell = '<br style="clear: both;"/>'."\n\t\t";
				$i = 1;
				while ($row = mysql_fetch_array($results)){
					
					// ### added htmlentities function for the fields we get from database
					$v1		= (isset($row['value'])	? htmlentities($row['value'], ENT_QUOTES,  'UTF-8')	: "");
					$v2		= (isset($row['text'])	? htmlentities($row['text'] , ENT_QUOTES,  'UTF-8')	: $v1);
					if ($v1 != ""){
						if($v1 == $selected){
							$check = ' checked = "checked"';
							} else {
							$check = "";
						}
						
						// ### add incremental id
						$sell .= $htmlstart.'id="'.$field_name.'_'.$i.'" value = "'.$v1.'" '.$check." />$v2".$htmlend;
						$i++;
					}
				}
								
				
				} else {
				$this -> show_error("3", TRUE) ;
				
			}
		
		return $sell;
	}
	
	
	public function get_field($field=""){
	/*
	== this function outputs single fields (without a form)
	*/
			
		if (isset($this -> fields[$field])){

				return $this -> fields[$field];
			
			} elseif (!empty($this -> fields)) {
			
				return end($this -> fields);
			
			} else {
				
				if ($this ->err_messages != "") {$errors = $this ->err_messages;} else {$errors = "";}
				return $errors . $this -> show_error("12", FALSE, TRUE) ;
				
		}
	
	}
	
	
	public function add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue){
	/*
	== this function generates the html code for fields
	*/

	$fieldstypes = array(	"submit", "text", "password", "hidden", "textarea", 
							"select", "select_from_db", "radio", "radio_from_db", "checkbox", "file");
	
	
	if(in_array($field_type, $fieldstypes) && $field_name != ""){
		
		
		//load the style of the field
		if(is_array($field_style)){
			$style="";
			foreach ($field_style as $attr => $value){
				$style .=" ".$attr." = ".'"'.$value.'"';}
				
			}else{
			if ($field_style !=""){
			$this -> show_error("11" , TRUE) ;}
			$style="";
		}
		
		//label for the field
		$requirements = (in_array($field_name, $this -> required_fields) ? ' <span '.$this -> required_style .'>*</span>' : "");
		$label = ($field_label != "" ? '<label for = "'.$field_name.'">'.$field_label.$requirements.": </label>\n" : "\n");
		
		
		if(is_array($field_values)){
			$value = "";
			switch ($field_type) {
			
			
				case "select_from_db":
			
				if(is_array($field_values)){				
				
					$table			= (isset($field_values["table"])		? $field_values["table"]		: "" );
					$value_field	= (isset($field_values["value_field"])	? $field_values["value_field"]	: "" );
					$text_field		= (isset($field_values["text_field"])	? $field_values["text_field"]	: $value_field);
					$order			= (isset($field_values["order"])		? $field_values["order"]		: $value_field);
					
					
					if($value_field == ""){
					
						$this -> show_error("7" , TRUE) ;
					
						}else{
						$value = $this -> seldata($table, $order, $value_field, $text_field, $selectedvalue);
					
					}
					
					} else {
					
					$this -> show_error("6" , TRUE) ;
				}			
				break;
				
			
				case "select":
				
					foreach ($field_values as $val => $text){
						
						if($selectedvalue!="" && $val == $selectedvalue){
								$check = 'selected = "selected"';
							} else {
								$check = "";	
						}
												
						$value .="\t\t".'<option value = "'.$val.'" '.$check.">$text</option>\n";
					}								
				break;
				
				
				case "radio":
				$value .="<br style = \"clear:both;\" />\n\t\t";
					$i = 1;
					foreach ($field_values as $val => $text){
						
						if($selectedvalue!="" && $val == $selectedvalue){
								$check = 'checked = "checked"';
							} else {
								$check = "";	
						}
												
						// ###  added tabindex and ID attribute
						$value .='<div><input tabindex = #TABINDEX# '.$style.' type = "radio" name = "'.$field_name.'" id = "'.$field_name.'_'.$i.'" value = "'.$val.'" '.$check." />$text</div>\n\t\t";
						$i++;
					}
				break;
				
				
				case "radio_from_db":
				
				if(is_array($field_values)){				
				
						$table			= (isset($field_values["table"])		? $field_values["table"]		: "" );
						$value_field	= (isset($field_values["value_field"])	? $field_values["value_field"]	: "" );
						$text_field		= (isset($field_values["text_field"])	? $field_values["text_field"]	: $value_field);
						$order			= (isset($field_values["order"])		? $field_values["order"]		: $value_field);
						
						
						if($value_field == ""){
						
							$this -> show_error("8" , TRUE) ;
						
							} else {
							
							// ###  added tabindex
							$htmlstart	= '<div><input tabindex = #TABINDEX# '.$style.' type = "radio" name = "'.$field_name.'" ';
							$htmlend	= "</div>\n\t\t";
							// ###  added $field_name to params
							$value		= $this -> radiodata($htmlstart, $htmlend, $table, $order, $value_field, $text_field, $field_name, $selectedvalue);
						}
					
					} else {
					
					$this -> show_error("9" , TRUE) ;
				
				}
				break;
				
			//end field_type switch
			}
		
		
		
		
			//if field_values = array
			} else {
			
			if(in_array($field_type, array("text", "hidden", "textarea", "password", "submit", "checkbox", "file"))){
			
								
						
						$valuehtml = array( "text"		=> 'value = "'.$field_values.'"',
											"hidden"	=> 'value = "'.$field_values.'"',
											"checkbox"	=> 'value = "'.$field_values.'"',
											"submit"	=> 'value = "'.$field_values.'"',
											"textarea"	=> $field_values,
											"password"	=> "",
											"file"		=> "",
											);
					
						$value = $valuehtml[$field_type];
					
				
				} else {
				//Field values should be an array for this type of input
				$value = $this -> show_error("2", TRUE) ;
			}
			
			
			
		//if field_values != array	
		}
		
		
		
		// ###  added tabindex and ID attribute
		$fieldhtml = array ('text' 		=> '<input tabindex = #TABINDEX# '.$style.' type = "text" name = "'.$field_name.'"  id = "'.$field_name.'" '.$value.' />',
							'password'	=> '<input tabindex = #TABINDEX# '.$style.' type = "password" name = "'.$field_name.'" id = "'.$field_name.'" />',
							'file'		=> '<input tabindex = #TABINDEX# '.$style.' type = "file" name = "'.$field_name.'" id = "'.$field_name.'" />',
							'hidden' 	=> '<input tabindex = #TABINDEX# type = "hidden" name = "'.$field_name.'" id = "'.$field_name.'" '.$value.' />',
							'submit' 	=> '<input tabindex = #TABINDEX# type = "submit" name = "'.$field_name.'" id = "'.$field_name.'" '.$value.' />',
							'textarea'	=> '<textarea tabindex = #TABINDEX# '.$style.' name = "'.$field_name.'" id = "'.$field_name.'">'.$value.'</textarea>',		
							'select'	=> '<select tabindex = #TABINDEX# '.$style.' name = "'.$field_name.'" id = "'.$field_name.'">'."\n".$value."\t\t</select>",		
							'select_from_db'=> '<select tabindex = #TABINDEX# '.$style.' name = "'.$field_name.'" id = "'.$field_name.'">'."\n".$value."\t\t</select>",		
							'radio'		=> $value,		
							'radio_from_db'=> $value,		
							'checkbox'	=> '<input tabindex = #TABINDEX# type = "checkbox" name = "'.$field_name.'" id = "'.$field_name.'" '.$value.'/>'
							);
		
		
		
			if(!in_array("1", $this -> errors)){
			
				$requirements = "";
				if(!empty($this -> required_fields)){
				
					if(isset($this -> required_fields[$field_name])){$requirements = "\n\t\t<div ". $this -> required_style .'>*'.$this -> required_fields[$field_name] ."</div>";}
				}		
										
				if($fieldset !="" && in_array($fieldset, $this -> fieldset_list )){
							
					// ### added surrounding <div> element as it was missing from fieldsets
					$this -> fieldsets[$fieldset][] = "<div>" . $label."\t\t" . $fieldhtml[$field_type] . $requirements . "</div>";
					} else {
					$this -> fields[] = $label."\t\t" . $fieldhtml[$field_type] . $requirements;		
				}
				
				
			}

			

			} else {
			//field name or type incorrect
			$this -> show_error("1", TRUE) ;
		}

	
	}


	public function output($divstyle=""){
	/*
	== this function outputs a div which includes the whole form
	*/
	
		if(!in_array("1", $this -> errors)){
		
			if(is_array($divstyle)){
			$style = "";
			foreach ($divstyle as $attr => $value){
				$style .=" ".$attr." = ".'"'.$value.'"';}
				
			}else{
			$style = $divstyle;
		}
		
		$content = "";
	
	
		if(!empty($this -> fieldset_list)){
			
			foreach ($this -> fieldset_list as $key){
			
				if (isset($this -> fieldsets[$key] )){
					$content .= "\t<fieldset id=\"".preg_replace("#[^a-zA-Z]#", "", $key)."\"><legend>$key</legend>";
					
					foreach ($this -> fieldsets[$key] as $html){
					
						$content .= "\n \t\t$html\n";
					
					}
					$content .= "\t</fieldset>\n\n";
				}
			}
		}
	
	
		if(!empty($this -> fields)){
	
			foreach ($this -> fields as $field){
				$content .= "\n \t\t<div>$field\n\t\t</div>\n";
			}
		
		}
		
			if($content==""){
				//no input fields found
				return $this -> show_error("5", TRUE) ;
			
				} else {
				
				// ###  replace #TABINDEX# string with incremental index
				$content = preg_replace_callback("/#TABINDEX#/", "incTabindex", $content);
				
				return  "\n<!-- -=START FORM=- -->\n".'<div '.$style.">\n {$this -> err_messages} \n".preg_replace("#[{]" ."content". "[}]#", $content, $this -> form)."\n</div>\n<!-- -=END FORM=- -->\n";			 
			}
	
	
			} else {
			//the form could not be generated
			$this -> show_error("4", TRUE) ;
			
			return $this -> err_messages ;
		
		}
	
	}

	
}
		
// ###  call back function for incrementing tabindex
$tabindex = 1;
function incTabindex($matches){
	global $tabindex;
	$output = '';
	foreach($matches as $match)
	{
		$output .= $tabindex;
		$tabindex++;
	}
	return $output;
}