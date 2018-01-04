<?php

// general functions go here

function menu($array, $fieldName = 'select', $default = '', $allowBlank = FALSE) {
  $menu = "<select name=\"{$fieldName}\" id=\"{$fieldname}\">";
  if ($allowBlank) {
    $menu .= "\n<option value=''>Please select one</option>";
  }
  foreach ($array as $key=>$value) {
    // if (is_array($option)) { // maybe for opt groups?
    //   list($key,$value) = $option;
    // } else {
    //   $key = $option;
    //   $value = $option;
    // }
    $selected = ($key == $default) ? ' selected ' : '';
    $menu .= "\n<option value=\"$key\"{$selected}>$value</option>";
  }
  $menu .= "\b</select>";
  return $menu;
}

function radio($array, $fieldName = 'select', $default = '') {
  $radio = '';
  foreach ($array as $option) {
    if (is_array($option)) {
      list($key,$value) = $option;
    } else {
      $key = $option;
      $value = $option;
    }
    $selected = ($key == $default) ? ' checked="checked" ' : '';
    $radio .= "<span class=\"radio\"><input type=\"radio\" value=\"$key\"{$selected}/>$value</span>\n";
  }
  return trim($radio);
} 

function checkbox($array, $fieldName = 'select', $default = []) {
  $checkbox = '';
  foreach ($array as $option) {
    if (is_array($option)) {
      list($key,$value) = $option;
    } else {
      $key = $option;
      $value = $option;
    }
    $selected = (in_array($key, $default)) ? ' checked="checked" ' : '';
    $checkbox .= "<div class=\"checkbox\"><input type=\"checkbox\" value=\"$key\"{$selected}/>$value</div>\n";
  }
  return trim($checkbox);
} 