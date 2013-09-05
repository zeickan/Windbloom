<?php

/**
 * The FormTextArea class.  
 *
 * PHP Version 5
 *
 * LICENSE: This source file is licensed under a Creative Commons Attribution-Share Alike 3.0 United States License. http://creativecommons.org/licenses/by-sa/3.0/us/
 *
 * @category    Form Input
 * @author      Sean Monahan
 * @copyright   2008 Sean Monahan
 * @version     1.0
 */

require_once "FormField.class.php";
 
class FormTextArea extends FormField
{
  /**
   * The number of columns for the textarea
   * Default: -1 (undefined).
   *
   * @var int
   */
  private $_columns = -1;
  
  /**
   * The number of rows for the textarea
   * Default: -1 (undefined).
   *
   * @var int
   */
  private $_rows = -1;
  
  /**
   * The value assigned to the textarea.
   *
   * @var string
   */
  private $_value = "";
  
  /**
   * This class builds an HTML <textarea>.
   *
   * @param array $options The list of options for the <textarea>.
   * @param string $name The name for the <textarea>.  Also sets the id.
   * @param string $label The label for the <textarea>.
   * @param string $className The CSS class for the <textarea>.
   * @param int $columns The number of columns for the <textarea>.
   * @param bool $isDisabled Sets the <textarea> to disabled.  Default: false.
   */
  public function __construct($value = "", $name = "", $label = "",  
                              $className = "", $columns = -1, 
                              $rows = -1, $isDisabled = false)
  {
    $this->setFieldType("textarea");
    $this->setValue($value);
    $this->setName($name);
    $this->setLabel($label);
    $this->setClassName($className);
    $this->setIsDisabled($isDisabled);
    $this->setColumns($columns);
    $this->setRows($rows);
  }
    
  /**
   * Getter for $_columns.
   *
   * @return int The number of set columns.
   */
  public function columns()
  {
    return $this->_columns;
  }
  
  /**
   * Getter for $_rows.
   *
   * @return int The number of set rows.
   */
  public function rows()
  {
    return $this->_rows;
  }
  
  /**
   * Getter for  $_value.
   *
   * @return string The value for the text area.
   */
  public function value()
  {
    return $this->_value;
  } 
  
  /**
   * Setter for $_columns.
   *
   * @param string $value The options for the select.
   */
  public function setColumns($value)
  {
    if (!is_int($value)) {
      throw new Exception("Value for columns must be an integer.");
    }
    $this->_columns = $value;
  }
  
  /**
   * Setter for $_rows.
   *
   * @param string $value The options for the select.
   */
  public function setRows($value)
  {
    if (!is_int($value)) {
      throw new Exception("Value for rows must be an integer.");
    }
    $this->_rows = $value;
  }
  
  /**
   * Setter for $_value;
   * 
   * @param string $value The contents for $_value.
   */
  public function setValue($value)
  {
    $this->_value = $value;
  }
}

?>
