<?php

// general functions go here

function menu($array, $fieldName = 'select', $default = '', $allowBlank = FALSE, $required = TRUE, $use_keys = TRUE) {
  $menu = "<select name=\"{$fieldName}\" id=\"{$fieldName}\">";
  if ($allowBlank) {
    $menu .= "\n<option value=''>Please select one</option>";
  }
  foreach ($array as $key=>$value) {
      if (is_array($value)) {
          $menu .= "\n<optgroup label=\"$key\">";
          foreach ($value as $subkey=>$subval) {
              $selected = ($subkey == $default) ? ' selected ' : '';
              if (!$use_keys) {
                  $subkey = $subval;
              }
              $menu .= "\n<option value=\"$subkey\"{$selected}>$subval</option>";
          }
          $menu .= "</optgroup>";
      } else {
        if (!$use_keys) {
          $key = $value;
        }
        $selected = ($key == $default) ? ' selected ' : '';
        $menu .= "\n<option value=\"$key\"{$selected}>$value</option>";

      }
  }
  $menu .= "\n</select>";
  return $menu;
}

function radio($array, $fieldName = 'select', $default = '') {
  $radio = '';
  foreach ($array as $key=>$value) {
    $selected = ($key == $default) ? ' checked="checked" ' : '';
    $radio .= "<span class=\"radio\"><input type=\"radio\" name=\"{$fieldName}\" value=\"$key\"{$selected}/>$value</span>\n";
  }
  return trim($radio);
} 

function checkbox($array, $fieldName = 'select', $default = []) {
  $checkbox = '';
  foreach ($array as $key=>$value) {
    if ($value == '') {
      continue;
    }
    $selected = (in_array($key, $default)) ? ' checked="checked" ' : '';
    $checkbox .= "<span class=\"checkbox\"><input type=\"checkbox\" name=\"{$fieldName}\" value=\"$key\"{$selected}/>$value</span>\n";
  }
  return trim($checkbox);
} 

function print_pre($what) {
  echo "<pre>";
  print_r($what);
  echo "</pre>";
}