<?php

/**
 * This class creates HTML form using only PHP code.
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


class Form
{
  /**
   * The name of the form.
   *
   * @var string
   */
  private $_name;
  
  /**
   * The method use to submit the form.  Default: post.
   *
   * @var string
   */
  private $_method = "post";
  
  /**
   * The script to which the form is submitted.  Default: self.
   *
   * @var string
   */
  private $_action = "";
  
  /**
   * The fields for the form.
   *
   * @var array
   */
  private $_fields = array();
  
  /**
   * Any event listeners that need to be added to the form.
   *
   * @var array
   */
  private $_eventListeners = array();
  
  /**
   * This class builds an HTML form.
   *
   * @param string $name The name of the form.
   * @param string $method The method used to submit the form.
   * @param string $action The script used to process the form.
   */
  public function __construct($name = "", $method = "post", $action = null)
  {
    $this->setName($name);
    $this->setMethod($method);
    if (is_null($action)) {
      $action = $_SERVER['REQUEST_URI'];
    }
    $this->setAction($action);
    $this->newline = "\n\n";
  }
  
  /**
   * Getter for $_name.
   *
   * @return string The name of the form.
   */
  public function name()
  {
    return $this->_name;
  }
  
  /**
   * Getter for $_method.
   *
   * @return string The method used to submit the form.
   */
  public function method()
  {
    return $this->_method;
  }
  
  /**
   * Getter for $_action.
   *
   * @return string The script to submit the form to.
   */
  public function action()
  {
    return $this->_action;
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
   * Setter for  $_method.
   *
   * @param string $method The method to use.
   */
  public function setMethod($method)
  {
    $method = strtolower($method);
    if ($method != "get" && $method != "post") {
      throw new Exception("Method can only be 'get' or 'post'.");
    }
    $this->_method = $method;
  }
  
  /**
   * Setter for $_action.
   *
   * @param string $action The action to use.
   */
  public function setAction($action)
  {
    $this->_action = $action;
  }
  
  /**
   * Adds a FromField to the list of fields.
   *
   * @param FormField $field A FormField object to be added to the list of fields.
   */
  public function addField($field)
  {
    $this->_fields[] = $field;
  }
  
  /**
   * Adds an event listeners to the form.
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
  
  /**
   * Prints the form.
   */
  public function printForm()
  {
    $newline = "\n\n";
    //Add the opening tag.
    $form = $this->_processFormOpeningTag();
    $form .= $newline;
    //Add the form fields.
    for ($i = 0; $i < count($this->_fields); $i++) {
      $form .= $this->_processField($i);
      $form .= $newline;
    }
    $form .= $this->_processFormClosingTag();
    $form .= $newline;
    
    echo $form;
  }
  
  /**
   * Builds the form.
   *
   * @param string $form The HTML form.
   */
  public function buildForm()
  {
    $newline = $this->newline;
    //Add the opening tag.
    
    $form = '';    
    $form .= $this->_processFormOpeningTag();
    
    if( !empty($this->legend) ):
    
      $form.='<fieldset><legend>'.$this->legend.'</legend>';
    
    endif;
    
    $form .= $newline;
    //Add the form fields.
    for ($i = 0; $i < count($this->_fields); $i++) {
      
      if( !empty($this->coverField) ){
        
        $form .= $this->coverField[0].$this->_processField($i).$this->coverField[1];
        
      } else {
        
        $form .= $this->_processField($i);
        $form .= $newline;
        
      }
      
    }
    
    if( !empty($this->legend) ):
    
      $form.='</fieldset>';
    
    endif;
    
    $form .= $this->_processFormClosingTag();
    $form .= $newline;
    
   
    
    return $form;
  }
  
  /**
   * Processes the opening tag of the form.
   *
   * @return The processed opening tag of the form.
   */
  private function _processFormOpeningTag()
  {
    $form = '<form';
    if ($this->_name != "") {
      $form .= ' name="' . $this->_name . '"';
      $form .= ' id="' . $this->_name . '"';
    }
    $form .= ' method="' . $this->_method . '"';
    $form .= ' action="' . $this->_action . '"';
    if (count($this->_eventListeners) > 0) {
      $form .= $this->_processFormEventListeners();
    }
    $form .= '>';
    return $form;
  }
  
  /**
   * Processes the closing tag of the form.
   *
   * @return The processed closing tag of the form.
   */
  private function _processFormClosingTag()
  {
    $form = '</form>';
    return $form;
  }
  
  /**
   * Processes the fields added to the Form.
   *
   * @param int $i The index of the field to be processed.
   * @return string The field, processed into an HTML string.
   */
  private function _processField($i)
  {
    $field = "";
    switch ($this->_fields[$i]->fieldType()) {
      case "input":
        $field = $this->_processInput($i);
        break;
      case "select":
        $field = $this->_processSelect($i);
        break;
      case "textarea":
        $field = $this->_processTextArea($i);
        break;
      case "separator":
        $field = $this->_processSeparator($i);
        break;
      case "captcha":
        $field = $this->_processCaptcha($i);
        break;
    }
    return $field;
  }
  
  /**
   * Processes a field of type "input", i.e., <input> tags.
   *
   * @param int $i The index of the current field.
   * @return string The field processed into an HTML string.
   */
  private function _processInput($i)
  {
    //Add the label;
    $field = "";
    if ($this->_fields[$i]->name() != "" && $this->_fields[$i]->label() != "") {
      $field .= '<label for="' . $this->_fields[$i]->name() . '">';
      $field .= $this->_fields[$i]->label() . '</label>' . "\n";
    }
    //Add any style padding
    if ($this->_fields[$i]->stylePad()) {
      $field .= '<span class="formStylePadLeft';
      if ($this->_fields[$i]->className() != "") {
        $field .= ' ' . $this->_fields[$i]->className();
      }
      $field .= '"></span>';
    }
    //Add the input
    $field .= '<input type="' . $this->_fields[$i]->type() . '" ';
    //Set the name and id attributes
    if ($this->_fields[$i]->name() != "") {
      $field .= 'id="' . $this->_fields[$i]->name() . '" ';
      $field .= 'name="' . $this->_fields[$i]->name() . '" ';
    }
    //Set the class attribute
    if ($this->_fields[$i]->className() != "") {
      $field .= 'class="' . $this->_fields[$i]->className() . '" ';
    }
    //Set the disabled attribute
    if ($this->_fields[$i]->isDisabled()) {
      $field .= 'disabled="disabled" ';
    }
    
    //Set the require
    if ($this->_fields[$i]->isRequired()) {
      $field .= 'required ';
    }
    
    //Set the accept attribute
    if ($this->_fields[$i]->accept() != "") {
      $field .= 'accept="' . $this->_fields[$i]->accept() . '" ';
    }
    //Set the checked attribute
    if ($this->_fields[$i]->checked()) {
      $field .= 'checked="checked" ';
    }
    //Set the maxlength attribute
    if ($this->_fields[$i]->maxLength() != -1) {
      $field .= 'maxlength="' . $this->_fields[$i]->maxLength() . '" ';
    }
    //Set the readonly attribute
    if ($this->_fields[$i]->readOnly()) {
      $field .= 'readonly="readonly" ';
    }
    //Set the image attribute
    if ($this->_fields[$i]->image() != "") {
      $field .= 'src="' . $this->_fields[$i]->image() . '" ';
    }
    //Set the size attribute
    if ($this->_fields[$i]->size() != -1) {
      $field .= 'size="' . $this->_fields[$i]->size() . '" ';
    }
    //Set the value
    if ($this->_fields[$i]->value() != "") {
      $field .= 'value="' . $this->_fields[$i]->value() . '" ';
    }
    
    
    
    if (count($this->_fields[$i]->eventListeners()) > 0) {
      $field .= $this->_processFieldEventListeners($i);
    }
    //Close the tag
    $field .= '/>';
    
    //Add any style padding
    if ($this->_fields[$i]->stylePad()) {
      $field .= '<span class="formStylePadRight';
      if ($this->_fields[$i]->className() != "") {
        $field .= ' ' . $this->_fields[$i]->className();
      }
      $field .= '"></span>' . "\n";
    }
    
    return $field;
  }
  
  /**
   * Processes a field of type "select", i.e., <select> tags.
   *
   * @param int $i The index of the current field.
   * @return string The field processed into an HTML string.
   */
  private function _processSelect($i)
  {
    //Add the label;
    $field = "";
    if ($this->_fields[$i]->name() != "" && $this->_fields[$i]->label() != "") {
      $field .= '<label for="' . $this->_fields[$i]->name() . '">';
      $field .= $this->_fields[$i]->label() . '</label>' . "\n";
    }
    //Add the select
    $field .= '<select ';
    //Set the name and id attributes
    if ($this->_fields[$i]->name() != "") {
      $field .= 'id="' . $this->_fields[$i]->name() . '" ';
      $field .= 'name="' . $this->_fields[$i]->name() . '" ';
    }
    //Set the class attribute
    if ($this->_fields[$i]->className() != "") {
      $field .= 'class="' . $this->_fields[$i]->className() . '" ';
    }
    //Set the disabled attribute
    if ($this->_fields[$i]->isDisabled()) {
      $field .= 'disabled="disabled" ';
    }
    //Set the multiple attribute
    if ($this->_fields[$i]->allowMultipleSelections()) {
      $field .= 'multiple="multiple" ';
    }
    //Set the size
    if ($this->_fields[$i]->size() != -1) {
      $field .= 'size="' . $this->_fields[$i]->size() . '" ';
    }
    //Add event listeners  
    if (count($this->_fields[$i]->eventListeners()) > 0) {
      $field .= $this->_processFieldEventListeners($i);
    }
    $field .= ">\n";;
    $optionsHtml = "";
    if (count($this->_fields[$i]->options()) > 0) {
      $options = $this->_fields[$i]->options();
      for ($i = 0; $i < count($options); $i++) {
        $option = '<option ';
        $option .= 'value="' . $options[$i]['value'] . '"';
        if (isset($options[$i]['selected']) && $options[$i]['selected']) {
          $option .= ' selected="selected"';
        }
        $option .= '>';
        $option .= $options[$i]['name'];
        $option .= '</option>';
        
        $optionsHtml .= $option . "\n";
      }
    }
    
    $field .= $optionsHtml;
    //Close the tag
    $field .= '</select>';
    
    return $field;
  }
  
  /**
   * Processes a field of type "textarea", i.e., <textarea> tags.
   *
   * @param int $i The index of the current field.
   * @return string The field processed into an HTML string.
   */
  private function _processTextArea($i)
  {
    //Add the label;
    $field = "";
    if ($this->_fields[$i]->name() != "" && $this->_fields[$i]->label() != "") {
      $field .= '<label for="' . $this->_fields[$i]->name() . '">';
      $field .= $this->_fields[$i]->label() . '</label>' . "\n";
    }
    //Add the textarea
    $field .= '<textarea ';
    //Set the name and id attributes
    if ($this->_fields[$i]->name() != "") {
      $field .= 'id="' . $this->_fields[$i]->name() . '" ';
      $field .= 'name="' . $this->_fields[$i]->name() . '" ';
    }
    //Set the class attribute
    if ($this->_fields[$i]->className() != "") {
      $field .= 'class="' . $this->_fields[$i]->className() . '" ';
    }
    //Set the disabled attribute
    if ($this->_fields[$i]->isDisabled()) {
      $field .= 'disabled="disabled" ';
    }
    //Set the rows attribute
    if ($this->_fields[$i]->rows() != -1) {
      $field .= 'rows="' . $this->_fields[$i]->rows() . '" ';
    }
    //Set the cols attribute
    if ($this->_fields[$i]->columns() != -1) {
      $field .= 'cols="' . $this->_fields[$i]->columns() . '" ';
    }
    //Add event listeners  
    if (count($this->_fields[$i]->eventListeners()) > 0) {
      $field .= $this->_processFieldEventListeners($i);
    }
    $field .= ">\n";;
    //Add the value
    if ($this->_fields[$i]->value() != "") {
      $field .= $this->_fields[$i]->value();
    }
     
    //Close the tag
    $field .= '</textarea>';
    
    return $field;
  }
  
  /**
   * Processes a field of type "separator", i.e., a title for a section
   * of the form.
   *
   * @param int $i The index of the current field.
   * @return string The field processed into an HTML string.
   */
  private function _processSeparator($i)
  {
    $field = "";
    //Set the title
    $separator = $this->_fields[$i];
    if ($separator->title() != "") {
      $field .= '<div';
      if ($separator->className() != "") {
        $field .= ' class="' . $separator->className() . '"';
      }
      $field .= '>';
      $field .= $separator->title();
      $field .= '</div>';
    }

    return $field;
  }
  
  /**
   * Processes captcha fields.
   *
   * @param int $i The index.
   * @return string the Catpcha, processed into an HTML string.
   */
  private function _processCaptcha($i)
  {
    $field = "";
    $captcha = $this->_fields[$i];
    //If the reload button goes before the image
    if ($captcha->reloadLocation() == "BeforeImage") {
      $field .= $this->_addCaptchaReloadButton($captcha);
    }
    //Add the captcha image.
    $field .= '<img id="' . $captcha->imageName() . '" ';
    $field .= 'name="' . $captcha->imageName() . '" ';
    $field .= 'src="' . $captcha->src() . '" alt="CAPTCHA image"/>';
    //If the reload button goes after the image
    if ($captcha->reloadLocation() == "AfterImage") {
      $field .= $this->_addCaptchaReloadButton($captcha);
    }
    //Add the label.
    $field .= '<label for="' . $captcha->inputName() . '">'. $captcha->label() . '</label>';
    //Add the input.
    $field .= '<input type="text" name="' . $captcha->inputName() . '" ';
    $field .= 'id="' . $captcha->inputName() . '" ';
    $field .= 'maxlength="' . $captcha->maxLength() . '"/>';
    //If the reload button goes after the input
    if ($captcha->reloadLocation() == "AfterInput") {
      $field .= $this->_addCaptchaReloadButton($captcha);
    }
    

    return $field;
  }
  
  /**
   * Adds the CAPTCHA reload button/link.
   *
   * @param FormCaptcha $captcha The FormCaptcha object.
   * @return string The HTML string for the reload button/link.
   */
  private function _addCaptchaReloadButton($captcha)
  {
    //Add the reload button
    $field = '<div id="captchaReloadDiv">';
    if ($captcha->showReload()) {
      //Build the link
      $reload = '<a id="' . $captcha->reloadName() . '" ';
      $reload .= 'href="#" onclick="document.getElementById(\\\'';
      $reload .= $captcha->imageName() . '\\\').src = \\\'' . $captcha->src();
      $reload .= '?\\\' + Math.random(); return false">';
      if ($captcha->reloadImage() != "") {
        $reload .= '<img src="' . $captcha->reloadImage() . '" alt="' . $captcha->reloadText() . '"/>';
      } else {
        $reload .= $captcha->reloadText();
      }
      $reload .= '</a>';
       
      //The reload uses JS.  Wrap the code in a <script> tag so it won't be displayed for non-JS users.
      $script = '<script type="text/javascript">';
      $script .= 'document.write(\'' . $reload . '\');';
      $script .= '</script>';
      //Add the reload
      $field .= $script;
    }
    $field .= '</div>';
    
    return $field;
  }
  
  /**
   * Processes any event listeners attached to the field at the current index.
   *
   * @param int $i The index of the field being processed.
   * @return string The event listeners parsed into an HTML string that can be
   *                attached to the input field.
   */
  private function _processFieldEventListeners($i)
  {
    $listeners = $this->_fields[$i]->eventListeners();
    $html = "";

    for ($j = 0; $j < count($listeners); $j++) {
      $html .= " ";
      $html .= $listeners[$j]['event'] . '="' . $listeners[$j]['listener'] . '"';
    }
    
    return $html;
  }
  
  /**
   * Processes any event listeners attached to the form.
   *
   * @return string The event listeners parsed into an HTML string that can be
   *                attached to the form.
   */
  private function _processFormEventListeners()
  {
    $listeners = $this->_eventListeners;
    $html = "";
    
    for ($j = 0; $j < count($listeners); $j++) {
      $html .= " ";
      $html .= $listeners[$j]['event'] . '="' . $listeners[$j]['listener'] . '"';
    }
    
    return $html;
  }
}

?>
