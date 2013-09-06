<?php

/**
 * The FormSelect class.  
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
 
class FormSelect extends FormField
{
  /**
   * When set, it allows multiple items to be selected at once.
   *
   * @var bool
   */
  private $_allowMultipleSelect = false;
  
  /**
   * The options for the select.
   *
   * Options is a multidimensional array of the form:
   * $options[$i] = array("value" => $value,
   *                      "name" => $name
   *                      "selected" => $selected);
   * Where $i is a positive integer,
   * $value is the "value" attribute,
   * $option is the text to be displayed for the option, and
   * $selected is a boolean where true marks the option as the
   * selected option.
   *
   * @var array
   */
  private $_options = array();
  
  /**
   * This class builds an HTML <select>.
   *
   * @param array $options The list of options for the <select>.
   * @param string $name The name for the <select>.  Also sets the id.
   * @param string $label The label for the <select>.
   * @param string $className The CSS class for the <select>.
   * @param bool $isDisabled Sets the <select> to disabled.  Default: false.
   * @param bool $allowMultipleSelections Allows multiple items in the <select> to 
   *                      be selected.  Default: false.
   * @param bool $size Sets the number of visible items in the <select>.
   *                      Default: -1 (unlimited).
   */
  public function __construct($options = null, $name = "", $label = "",  
                              $className = "", $isDisabled = false,
                              $allowMultipleSelections = false, $size = -1)
  {
    $this->setFieldType("select");
    if (!is_null($options)) {
      $this->setOptions($options);
    }
    $this->setName($name);
    $this->setLabel($label);
    $this->setClassName($className);
    $this->setIsDisabled($isDisabled);
    $this->setAllowMultipleSelections($allowMultipleSelections);
    $this->setSize($size);
  }
  
  /**
   * Getter for  $_options.
   *
   * @return array Returns the options set for the select.
   */
  public function options()
  {
    return $this->_options;
  } 
  
  /**
   * Getter for $_allowMultipleSelections.
   *
   * @return bool Whether multiple selections are allowed or not.
   */
  public function allowMultipleSelections()
  {
    return $this->_allowMultipleSelections;
  }
  
  /**
   * Setter for $_options
   *
   * @param string $value The options for the select.
   */
  public function setOptions($value)
  {
    $this->_options = $value;
  }
  
  /**
   * Setter for $_allowMultipleSelections.
   *
   * @param bool $value True to allow, false to disallow.
   */
  public function setAllowMultipleSelections($value)
  {
    if (!is_bool($value)) {
      throw new Exception("Value must be a boolean.");
    }
    $this->_allowMultipleSelections = $value;
  }
  
  /**
   * Adds a new option to the list of existing options.
   *
   * Option must be of the form:
   * $option = array("value" => $value,
   *                 "option" => $option);
   * Where "value" is the value attribute of the option and
   * "option" is the text that will be displayed in the drop down.
   *
   * @param array $option The option to add.
   * @return bool Returns true.
   */
  public function addOption($option)
  {
    array_push($this->_options, $option);
    return true;
  }
  
  /**
   * Setter for $_selectedValue.
   * Sets the value the drop down should have selected.
   * Removes any other selection settings.
   *
   * @param mixed $value The drop down value to be selected.
   * @return bool Returns true on success.  False if the option doesn't exist.
   */
  public function setSelectedOptionByValue($value)
  {
    if ($value == "") {
      return false;
    }
    
    $this->_removeSelectedOption();
    
    if (count($this->_options) <= 0) {
      return false;
    }
    
    for ($i = 0; $i < count($this->_options); $i++) {
      if ($this->_options[$i]['value'] == $value) {
        $this->_options[$i]['selected'] = true;
        return true;
      }
    }
    return false;
  }
  
  /**
   * Setter for $_selectedIndex.
   * Sets the index the drop down should have selected.
   *
   * @param int $index The index to be selected.
   * @return bool Returns true of the selected option is set.  False otherwise.
   */
  public function setSelectedOptionByIndex($index)
  {
    if ($index == "") {
      return false;
    }
    
    $this->_removeSelectedOption();
    
    if (isset($this->_option[$index])) {
      $this->_options[$index]['selected'] = true;
      return true;
    }
    
    return false;
  }
  
  /**
   * Removes any selected options.
   *
   * @return bool Returns true on success.  False if there are no options set.
   */
  private function _removeSelectedOption()
  {
    if (count($this->_options) <= 0) {
      return false;
    }
    
    for ($i = 0; $i < count($this->_options); $i++) {
      $this->_options[$i]['selected'] = false;
    }
    
    return true;
  }
}

?>
