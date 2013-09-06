<?php

/**
 * The FormField class.  
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

class FormField
{
  /**
   * The name of the field.
   *
   * @var string
   */
  private $_name;
  
  /**
   * The label for the field.
   *
   * @var string
   */
  private $_label = "";
  
  /**
   * The the CSS class for the field.
   *
   * @var string
   */
  private $_className = "";
  
  /**
   * The value assigned to the field.
   *
   * @var string
   */
  private $_value = "";
  
  /**
   * When set, it disables the field.
   * 
   * @var bool
   */
  private $_isDisabled = false;
  
  
  /**
   * When set, it requiered the field.
   * 
   * @var bool
   */
  
  private $_isRequired = false;
  
  /**
   * Any event listeners (for Javascript) that the field needs.
   *
   * @var array
   */
  private $_eventListeners = array();
  
  /**
   * The size of the input element.  Cannot be used with type
   * "hidden". Default: -1 (no size defined).
   *
   * @var int
   */
  private $_size = -1;
  
  /**
   * Corresponds to the HTML field type.
   *
   * @var string
   */ 
  private $_fieldType = "";
  
  /**
   * This is the base class for all HTML form fields.
   */
  public function __construct()
  {
 
  }
  
  /**
   * Getter for $_name.
   *
   * @return string The name of the form field.
   */
  public function name()
  {
    return $this->_name;
  }
  
  /**
   * Getter for $_method.
   *
   * @return string The label for the form field.
   */
  public function label()
  {
    return $this->_label;
  }
  
  /**
   * Getter for $_action.
   *
   * @return string The CSS class name for the form field.
   */
  public function className()
  {
    return $this->_className;
  }
  
  /**
   * Getter for $_type.
   *
   * @return string The type of form field.
   */
  public function type()
  {
    return $this->_type;
  }
  
  /**
   * Getter for $_isDisabled.
   *
   * @return bool Whether the form field is disabled or not.
   */
  public function isDisabled()
  {
    return $this->_isDisabled;
  }
  
  /**
   * Getter for $_isRequiered.
   *
   * @return bool Whether the form field is disabled or not.
   */
  public function isRequired()
  {
    return $this->_isRequired;
  }
  
  /**
   * Getter for $_size.
   *
   * @return int The size of the form field.
   */
  public function size()
  {
    return $this->_size;
  }
  
  /**
   * Getter for $_eventListeners.
   *
   * @return array The event listeners attached to the form field.
   */
  public function eventListeners()
  {
    return $this->_eventListeners;
  }
  
  /**
   * Getter for $_fieldType;
   *
   * @return string the field type.
   */
  public function fieldType()
  {
    return $this->_fieldType;
  }
  
  /**
   * Setter for $_name
   *
   * @param string $name The name to set.
   */
  public function setName($name)
  {
    $this->_name = $name;
  }
  
  /**
   * Setter for  $_label.
   *
   * @param string $label The label to use.
   */
  public function setLabel($label)
  {
    $this->_label = $label;
  }
  
  /**
   * Setter for $_className.
   *
   * @param string $className The class name for the field.
   */
  public function setClassName($className)
  {
    $this->_className = $className;
  }
  
  /**
   * Setter for $_isDisabled.
   *
   * @param bool $value True to allow, false to disallow.
   */
  public function setIsDisabled($value)
  {
    if (!is_bool($value)) {
      throw new Exception("isDisabled must be a boolean.");
    }
    $this->_isDisabled = $value;
  }
  
  /**
   * Setter for $_isRequiered.
   *
   * @param bool $value True to allow, false to disallow.
   */
  public function setIsRequired($value)
  {
    if (!is_bool($value)) {
      throw new Exception("isRequiered must be a boolean.");
    }
    $this->_isRequired = $value;
  }
  
  /**
   * Setter for $_size.
   *
   * @param int $value The size.
   */
  public function setSize($value)
  {
    if (!is_int($value)) {
      throw new Exception("Size must be an integer.");
    }
    $this->_size = $value;
  } 
  
  /**
   * Setter for $_fieldType.
   *
   * @param string $value The field type.
   */
  public function setFieldType($value)
  {
    $types = array("input", "select", "textarea", "separator", "captcha");
    if (!in_array($value, $types)) {
      throw new Exception("Invalid field type.");
    }
    $this->_fieldType = $value;
  }
  
  /**
   * Adds a class name to the existing list of classes.
   *
   * @param string $className The class name to add to the list.
   */
  public function addClassName($className)
  {
    if ($this->_className != "") {
      $this->_className .= " ";
    }
    $this->_className .= $className;
  }
  
  /**
   * Adds an event listeners to the field.
   *
   * @param string $event The event to listen for.
   * @param string $listener The function to execute.
   */
  public function addEventListener($event, $listener)
  {
    $eventListener = array("event" => $event,
                           "listener" => $listener);
    
    $this->_eventListeners[] = $eventListener;
  }
}

?>
