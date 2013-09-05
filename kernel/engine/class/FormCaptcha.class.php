<?php

/**
 * The FormCaptcha class.  
 *
 * NOTE:  This class requires the CAPTCHA class found at http://www.phpcaptcha.org/
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
 
class FormCaptcha extends FormField
{
  /**
   * The location of the captcha script.
   *
   * @var string
   */
  private $_src = "";
  
  /**
   * Show the reload button.
   *
   * @var bool
   */
  private $_showReload = true;
  
  
  /**
   * The name and ID for the captcha image.
   *
   * @var string
   */
  private $_imageName = "";
  
  /**
   * The name and ID for the captcha input field.
   *
   * @var string
   */
  private $_inputName = "";
  
  /**
   * The name and ID for the reload button.
   *
   * @var string
   */
  private $_reloadName = "";
  
  /**
   * The image to use for the reload button.
   *
   * @var string;
   */
  private $_reloadImage = "";
  
  /**
   * The text to use for reload.
   *
   * @var string;
   */
  private $_reloadText = "Try a different image.";
  
  /**
   * The the location of the reload link/button.
   *
   * @var string;
   */
  private $_reloadLocation = "BeforeImage";
  
  /**
   * The class name for the captcha field.
   *
   * @var string
   */
  private $_className = "";
  
  /**
   * The maximum length for the captcha input.
   *
   * @var int
   */
  private $_maxLength = 6;
  
  /**
   * Adds style padding to the field.
   *
   * @var bool
   */
  private $_stylePad = false;
  
  /**
   * This class builds an HTML <input>.
   *
   * @param string $src The location of the captcha script.
   * @param string $showReload Show the captcha reload button?
   * @param string $label The text for the CAPTCHA label.
   * @param string $imageName The name and ID for the image.
   * @param string $inputName The name and ID for the input.
   * @param string $reloadName The name and ID for the reload button/link.
   * @param string $reloadImage The image to use for the reload button.
   * @param string $reloadText The text to display for the reload link and alt for image.
   * @param string $reloadLocation The location to place the reload link/button.
   * @param string $className The CSS class for the captcha.
   * @param int $maxLength The max length for the captcha.
   * @param bool $stylePad Add <div>'s so the <input> can be styled with fancy
   *                  rounded edges and such.
   */
  public function __construct($src = "", $showReload = true, $label = "", $imageName = "captchaimage",
                              $inputName = "captchacode", $reloadName = "captchareload", 
                              $reloadImage = "", $reloadText = "Try a different image.",
                              $reloadLocation = "BeforeImage", $className = "", 
                              $maxLength = 6, $stylePad = false)
  {
    $this->setFieldType("captcha");
    $this->setSrc($src);
    $this->setShowReload($showReload);
    $this->setLabel($label);
    $this->setImageName($imageName);
    $this->setInputName($inputName);
    $this->setReloadName($reloadName);
    $this->setReloadImage($reloadImage);
    $this->setReloadText($reloadText);
    $this->setReloadLocation($reloadLocation);
    $this->setClassName($className);
    $this->setMaxLength($maxLength);
    $this->setStylePad($stylePad);
  }
  
  /**
   * Getter for  $_src.
   *
   * @return string The location of the CAPTCHA script.
   */
  public function src()
  {
    return $this->_src;
  } 
  
  /** 
   * Getter for $_showReload.
   *
   * @return bool Whether or not to show the reload button.
   */  
  public function showReload()
  {
    return $this->_showReload;
  }
  
  /** 
   * Getter for $_imageName.
   *
   * @return string The name of the CAPTCHA image.
   */  
  public function imageName()
  {
    return $this->_imageName;
  }
  
  /** 
   * Getter for $_inputName.
   *
   * @return string The name for the CAPTCHA input field.
   */  
  public function inputName()
  {
    return $this->_inputName;
  }
  
  /** 
   * Getter for $_reloadName.
   *
   * @return string The name for the reload button.
   */  
  public function reloadName()
  {
    return $this->_reloadName;
  }
  
  /**
   * Getter for $_reloadImage.
   *
   * @return string The image to use for the reload button.
   */
  public function reloadImage()
  {
    return $this->_reloadImage;
  }
  
  /**
   * Getter for $_reloadText.
   *
   * @return string The text to use for the reload link.
   */
  public function reloadText()
  {
    return $this->_reloadText;
  }
  
  /**
   * Getter for $_reloadLocation.
   *
   * @return string The placement for the reload button.
   */
  public function reloadLocation()
  {
    return $this->_reloadLocation;
  }
  
  /** 
   * Getter for $_maxLength.
   *
   * @return int The maximum length of the CAPTCHA code.
   */  
  public function maxLength()
  {
    return $this->_maxLength;
  }
  
  /**
   * Getter for $_className.
   *
   * @return string The CSS class name for the CAPTCHA.
   */
  public function className()
  {
    return $this->_className;
  }
  
  /**
   * Getter for $_stylePad.
   *
   * @return bool Whether or not to add style padding.
   */
  public function stylePad()
  {
    return $this->_stylePad;
  }
  
  /**
   * Setter for $_src;
   * 
   * @param string $value The contents for $_src.
   */
  public function setSrc($value)
  {
    $this->_src = $value;
  }
  
  /**
   * Setter for $_showReload.
   *
   * @param string $value Set the reload button to be displayed or not.
   */
  public function setShowReload($value)
  {
    if (!is_bool($value)) {
      throw new Exception("The value must be a boolean.");
    }
    $this->_showReload = $value;
  }
  
  /**
   * Setter for $_imageName.
   *
   * @param string $value The name and ID for the image.
   */
  public function setImageName($value)
  {
    $this->_imageName = $value;
  }
  
  /**
   * Setter for $_inputName.
   *
   * @param string $value The name and ID for the input.
   */
  public function setInputName($value)
  {
    $this->_inputName = $value;
  }
  
  /**
   * Setter for $_reloadName.
   *
   * @param string $value The name and ID for the reload button.
   */
  public function setReloadName($value)
  {
    $this->_reloadName = $value;
  }
  
  /**
   * Setter for $_reloadImage.
   *
   * @param string $value The image location for the reload button.
   */
  public function setReloadImage($value)
  {
    $this->_reloadImage = $value;
  }
  
  /**
   * Setter for $_reloadText.
   *
   * @param string $value The text for the reload button.
   */
  public function setReloadText($value)
  {
    $this->_reloadText = $value;
  }
  
  /**
   * Setter for $_reloadLocation.
   *
   * @param string $value The location for the reload button.
   */
  public function setReloadLocation($value)
  {
    $locations = array("BeforeImage", "AfterImage", "AfterInput");
    if (!in_array($value, $locations)) {
      throw new Exception("You must set a valid location.");
    }
    $this->_reloadLocation = $value;
  }
  
  /**
   * Setter for $_maxLength.
   *
   * @param int $value The length allowed for the input field.
   */
  public function setMaxLength($value)
  {
    if (!is_int($value)) {
      throw new Exception("Value must be an integer.");
    }
    
    $this->_maxLength = $value;
  }
  
  /**
   * Setter for $_className.
   *
   * @param string $value The name for the CSS class.
   */
  public function setClassName($value)
  {
    $this->_className = $value;
  }
  
  /**
   * Setter for $_stylePad.
   *
   * @param bool $value The value for style pad.
   */
  public function setStylePad($value)
  {
    if (!is_bool($value)) {
      throw new Exception("Value must be a boolean.");
    }
    $this->_stylePad = $value;
  }
}

?>
