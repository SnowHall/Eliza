<?php
/**
 * Eliza - Simple php acceptance testing framework
 *
 *
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 *
 * @version		0.2.0
 * @date		April 18, 2013
 *
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

class Form
{
  // Form name
  private $name;

  // Form action url
  private $action = '';

  // Form request method
  private $method = 'post';

  // Form HTML options
  private $options = array();

  public function __construct($name, $action = '', $options = array()) {
    $this->name = $name;
    $this->action = $action ? Eliza::app()->controller->url($action) : '';
    $this->options = $options;
  }

  /**
   * Returns form element
   *
   * @param string $name Form element name
   * @param string $type Type of form element
   * @param mixed $value Value of form element
   * @param array $options Additional HTML options
   * @return string Form HTML element
   */
  public function getElement($name, $type = 'text', $value = null, $options = array()) {
    switch ($type)
    {
      case 'text': case 'hidden': case 'submit': case 'checkbox':case 'radio':case 'checkboxlist':
        return $this->getInput($name, $type, $value, $options);
        break;
    }
  }

  /**
   * Return form input
   *
   * @param string $name Input name
   * @param string $type Input type
   * @param string $value Input value
   * @param array $options Input HTML options
   * @return string Generated input
   */
  public function getInput($name, $type = 'text', $value=null, $options = array()) {
    $input = '';
    if (!in_array($type, array('submit', 'button', 'hidden'))) {
      $labelName = isset($options['label']) ? $options['label'] : ucfirst($name);
      if (!is_null($labelName)) $input .= $this->getLabel($labelName, $name, $options);
    }
    if (!in_array($type, array('radio', 'checkboxlist'))) {
      $postValue = $this->getValue($name, $options);
      if ($postValue) {
        $value = $postValue;
        if ($type == 'checkbox') $options['checked'] = 'checked';
      }
    }
    if ($type == 'checkboxlist') $type = 'checkbox';
    $input .= '<input type="'.$type.'" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" '.$this->renderOptions($options).' />';

    return $input;
  }

  /**
   * Return form text field
   *
   * @param type $name Text field name
   * @param type $value Text field value
   * @param type $options Text field HTML options
   * @return string Text field HTML code
   */
  public function getTextField($name, $value = null, $options = array()) {
    return $this->getElement($name, 'text', $value, $options);
  }

  /**
   * Return form hidden text field
   *
   * @param type $name Hidden text field name
   * @param type $value Hidden text field value
   * @param type $options Hidden text field HTML options
   * @return string Hidden text field HTML code
   */
  public function getHiddenField($name, $value = null, $options = array()) {
    return $this->getElement($name, 'hidden', $value, $options);
  }

  /**
   * Return HTML form label
   *
   * @param string $name Label name
   * @param string $for Input name
   * @param array $options Label HTML options
   * @return string Generated label
   */
  public function getLabel($name, $for, $options = array()) {
    if (empty($name)) return '';

    $name = $this->getLabelName($name, $options);
    $required = !empty($options['required']) ? '<span class="required">*</span>' : '';
    return '<label class="control-label" for="'.$for.'">'.htmlspecialchars($name).$required.'</label>';
  }

  /**
   * Generate label name form input name
   *
   * @param string $name Label name
   * @param array() $options Label options
   * @return string Label name
   */
  public function getLabelName($name, $options = array()) {
    if (strpos($name,'[')) {
      preg_match('/[^\]]*\[(.*)\][^\]]*/is', $name, $matches);
      if (isset($matches[1])) $name = $matches[1];
    }
    $name = str_replace('_', ' ', $name);
    return ucfirst($name);
  }

  /**
   * Return HTML button
   *
   * @param string $label
   * @param array $options
   * @return string HTML button
   */
  public function getButton($label, $options = array()) {
    return '<button '.$this->renderOptions($options).'>'.htmlspecialchars($label).'</button>';
  }

  /**
   * Return form checkbox
   *
   * @param string $name Checkbox name
   * @param string $value Checkbox value
   * @param array $options Checkbox HTML options
   * @return string Form checkbox element
   */
  public function getCheckbox($name, $value = null, $options = array()) {
    return $this->getElement($name, 'checkbox', $value, $options);
  }

  /**
   * Return radiobutton list
   *
   * @param type $name Radiobutton list bname
   * @param type $selected Selected value
   * @param type $values Radiobutton list values
   * @param type $options Radiobutton list HTML options
   * @return string Radiobutton list
   */
  public function getRadioButtonList($name, $checked = '', $values = array(), $options = array()) {
    if (!$values) return;

    $options['label'] = false;

    $list = '<ul>';
    $postValue = $this->getValue($name);

    foreach ($values as $value=>$label) {
      // check current value
      if ($postValue == $value) $options['checked'] = 'checked';
      else if (!$postValue && $value == $checked) $options['checked'] = 'checked';

      $element = $this->getElement($name, 'radio', $value, $options);
      $elementLabel = '<span>'.htmlspecialchars($label).'<span>';

      // set nedeed list position
      if (isset($options['position']) && ($options['position'] == 'horizontal')) {
        $list .= '<li>'.$elementLabel.'<br />'.$element.'</li>';
      }
      else {
        $list .= '<li>'.$element.$elementLabel.'</li>';
      }
      unset($options['checked']);
    }
    $list .= '</ul>';

    return $list;
  }

  /**
   * Return Checkbox list
   *
   * @param string $name Checkboxlist name
   * @param array $checked Array of checked values
   * @param array $values Checkboxlist values
   * @param array $options Checkbox HTML options
   * @return string Checkbox list
   */
  public function getCheckboxList($name, $checked = array(), $values = array(), $options = array()) {
    if (!$values) return;

    $options['label'] = false;

    $list = '<ul>';
    $postValue = $this->getValue($name);

    foreach ($values as $value=>$label) {
      // check current value
      if ($postValue && in_array($value, $postValue)) $options['checked'] = 'checked';
      else if (!$postValue && $checked && in_array($value, $checked)) $options['checked'] = 'checked';

      $element = $this->getElement($name.'[]', 'checkboxlist', $value, $options);
      $elementLabel = '<span>'.htmlspecialchars($label).'<span>';

      // set nedeed list position
      if (isset($options['position']) && ($options['position'] == 'horizontal')) {
        $list .= '<li>'.$elementLabel.'<br />'.$element.'</li>';
      }
      else {
        $list .= '<li>'.$element.' '.$elementLabel.'</li>';
      }
      unset($options['checked']);
    }
    $list .= '</ul>';

    return $list;
  }

  /**
   * Return Dropdown list
   *
   * @param string $name
   * @param string $selected
   * @param array $values
   * @param array $options
   * @return string Dropdown list
   */
  public function getDropdownList($name, $selected = '', $values = array(), $options = array()) {
    if (!$values) return;
    $select = '<select name="'.  htmlspecialchars($name).'" >';
    foreach ($values as $value=>$label) {
      $postValue = $this->getValue($name);
      if ($postValue == $value) $options['selected'] = 'selected';
      else if ($value == $selected) $options['selected'] = 'selected';
      $select .= $this->getOption($label, $value, $options);
      unset($options['selected']);
    }
    $select .= '</select>';

    return $select;
  }

  /**
   * Get form element value from POST array
   *
   * @param type Form element
   * @param type $options Form element HTML options
   * @return string Form element value
   */
  private function getValue($name, $options = array()) {
    // Check if field name like Form[field]
    if (preg_match('/(.*)\[(.*?)\]/i', $name, $matches)) {
      $container = $matches[1];
      $element = $matches[2];
      $value = isset($_POST[$container][$element]) ? $_POST[$container][$element] : '';
    }
    else $value = isset($_POST[$name]) ? $_POST[$name] : '';

    if (!$value && isset($options['default'])) $value = $options['default'];

    return $value;
  }

  /**
   * Get option for Dropdown list
   *
   * @param string $label Option label
   * @param string $value Option value
   * @param array $options Option HTML options
   * @return string Dropdown list option
   */
  private function getOption($label, $value, $options) {
    return '<option value="'.htmlspecialchars($value).'" '.$this->renderOptions($options).' >'.htmlspecialchars($label).'</option>';
  }

  /**
   * Render HTML options for form element
   *
   * @param type $options array of HTML options
   * @return string HTML options string
   */
  private function renderOptions($options = array()) {
    if (!$options) return '';

    if (isset($options['label'])) unset($options['label'], $options['required'], $options['default']);

    $optionValue = '';
    foreach ($options as $key=>$value) {
      if (!is_null($value)) $optionValue .= $key.'="'.$value.'" ';
    }
    return $optionValue;
  }

  /**
   * Return open form tag
   * @return Open form  tag
   */
  public function begin() {
    return '<form name="'.$this->name.'" method="'.$this->method.'" action="'.$this->action.'" '.$this->renderOptions($this->options).'>';
  }

  /**
   * Return close form tag
   * @return Close form tag
   */
  public function end() {
    return '</form>';
  }

  /**
   * Email validator
   *
   * @param string $email Checking email
   * @return boolean True if email is valid
   */
  public static function emailValidate($email) {
    if (!$email) return false;

    return preg_match('/^[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',$email);
  }
}
