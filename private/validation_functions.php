<?php

  // is_blank('abcd')
  function is_blank($value='') {
    return !isset($value) || trim($value) == '';
  }

  // has_length('abcd', ['min' => 3, 'max' => 5])
  function has_length($value, $options=array()) {
    $length = strlen($value);
    if(isset($options['max']) && ($length > $options['max'])) {
      return false;
    } elseif(isset($options['min']) && ($length < $options['min'])) {
      return false;
    } elseif(isset($options['exact']) && ($length != $options['exact'])) {
      return false;
    } else {
      return true;
    }
  }

  // has_valid_email_format('test@test.com')
  function valid_email_format($email) {
    // My custom validation: A much more complicated regexp to match an email format
    // [A string of whitelisted characters]@[zero or more subdomains and a site name].[domain]
    $regexp = '/\A[A-Za-z0-9\_\.]+\@[A-Za-z0-9\_\.]+\.[A-Za-z]+\Z/'; // Reg exp for a proper email format
    return preg_match($regexp, $email);
  }

  function valid_username_format($username) {
    // A string of 1 or more whitelisted characters
    $regexp = '/\A[A-Za-z0-9\_]+\Z/';
    return preg_match($regexp, $username)
  }

  function valid_phone_format($phone) {
    // One or more whitelisted characters
    $regexp = '/\A[0-9\s\( \) \_]+\Z/';
    return preg_match($regexp, $phone);
  }

  function valid_name_format($name) {
    // My custom validation: Some restrictions on characters allowed in names
    // Important to note that apostrophes are allowed
    // Also, excluding characters from other languages could potentially create an issue
    $regexp = '/\A[A-Za-z\'\s]+\Z/';
    return preg_match($regexp, $name);
  }

  function valid_number_format($number) {
    // My custom validation: Ensure that a string intended to be used as a number is valid
    $regexp = '/\A[0-9]+\Z/';
    return preg_match($regexp, $number);
  }

?>
