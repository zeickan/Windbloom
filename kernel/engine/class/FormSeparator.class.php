<?php

/**
 * The FormSeparator class.  
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
 
class FormSeparator extends FormField
{
  /**
   * The title for the separator.
   *
   * @var string
   */
  private $_title = "";
  
  /**
   * This class builds a separator to be used inside an HTML form.
   *
   * @param array $title The text to display with the separator.
   * @param string $className The CSS class for the <select>.
   */
  public function __construct($title = "", $className = "formSeparator")
  {
    $this->setFieldType("separator");
    $this->setTitle($title);
    $this->setClassname($className);
  }
    
  /**
   * Getter for $_title.
   *
   * @return string The title for the separator.
   */
  public function title()
  {
    return $this->_title;
  }
  
  /**
   * Setter for $_title.
   *
   * @param string $value The options for the select.
   */
  public function setTitle($value)
  {
    $this->_title = $value;
  }
}

?>
