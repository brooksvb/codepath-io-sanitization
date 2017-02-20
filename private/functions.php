<?php

  function h($string="") {
    return htmlspecialchars($string);
  }

  function u($string="") {
    return urlencode($string);
  }

  function raw_u($string="") {
    return rawurlencode($string);
  }

  function redirect_to($location) {
    header("Location: " . $location);
    exit;
  }

  function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
  }

  function display_errors($errors=array()) {
    $output = '';
    if (!empty($errors)) {
      $output .= "<div class=\"errors\">";
      $output .= "Please fix the following errors:";
      $output .= "<ul>";
      foreach ($errors as $error) {
        $output .= "<li>{$error}</li>";
      }
      $output .= "</ul>";
      $output .= "</div>";
    }
    return $output;
  }

  /*
    @function: Sanitize any input
  */
  function i($string) {
    $string = strip_tags($string);
    // QUESTION: Is it bad practice to store html entities in the database?
    // After thinking of any possible issues, it seems like the only time it would
    // matter is if you're looking directly at db data and not through an html page
    $string = htmlentities($string);
    return $string;
  }

  /*
    @function: Sanitize any output on the page
  */
  function o($string) {
    $string = strip_tags($string);
    // QUESTION: Not sure if htmlspecialchars or htmlentities is better here.
    // I decided to use html entities because it is even more restrictive.
    $string = htmlentities($string);
    return $string;
  }

  /*
    @function: Sanitize every member of a given object for page output
  */
  function obj_o($object) {
    foreach ($object as $value) {
      $value = o($value);
    }
    // Ensure the variable values don't stick around
    unset($value);
    return $object;
  }

?>
