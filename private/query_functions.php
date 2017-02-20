<?php

  //
  // COUNTRY QUERIES
  //

  // Find all countries, ordered by name
  function find_all_countries() {
    global $db;
    $sql = "SELECT * FROM countries ORDER BY name ASC;";
    $country_result = db_query($db, $sql);
    return $country_result;
  }

  //
  // STATE QUERIES
  //

  // My custom validation
  function state_id_exists($id) {
    $result = find_state_by_id($id);
    if (db_num_rows($result) === 0) { // If no entries found
      $ret = false;
    } else $ret = true;
    db_free_result($result);
    return $ret;
  }

  // Find all states, ordered by name
  function find_all_states() {
    global $db;
    $sql = "SELECT * FROM states ";
    $sql .= "ORDER BY name ASC;";
    $state_result = db_query($db, $sql);
    return $state_result;
  }

  // Find all states, ordered by name
  function find_states_for_country_id($country_id=0) {
    global $db;
    $sql = "SELECT * FROM states ";
    $sql .= "WHERE country_id='" . $country_id . "' ";
    $sql .= "ORDER BY name ASC;";
    $state_result = db_query($db, $sql);
    return $state_result;
  }

  // Find state by ID
  function find_state_by_id($id=0) {
    global $db;
    $sql = "SELECT * FROM states ";
    $sql .= "WHERE id='" . $id . "';";
    $state_result = db_query($db, $sql);
    return $state_result;
  }

  function validate_state($state, $errors=array()) {
    if (is_blank($state['name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif (!has_length($state['name'], array('min' => 2, 'max' => 25))) {
      // My custom validation: Max is 25 characters
      $errors[] = "Name must be between 2 and 25 characters.";
    } else if (!valid_name_format($state['name'])) {
      $errors[] = "Name must only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($state['code'])) {
      $errors[] = "Code cannot be blank.";
    } elseif (!has_length($state['code'], array('min' => 2, 'max' => 10))) {
      // My custom validation: Max is 10 characters
      $errors[] = "Code must be between 2 and 10 characters.";
    } else if (!valid_name_format($state['code'])) {
      $errors[] = "Code must only contain letters, spaces, and apostrophes.";
    }

    return $errors;
  }

  // Add a new state to the table
  // Either returns true or an array of errors
  function insert_state($state) {
    global $db;

    $errors = validate_state($state);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO states (name, code)";
    $sql .= "VALUES ('". i($state['name']) ."', '". i($state['code']) ."')";    // For INSERT statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL INSERT statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Edit a state record
  // Either returns true or an array of errors
  function update_state($state) {
    global $db;

    $errors = validate_state($state);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE states SET ";
    $sql .= "name='" . db_escape(i($state['name'])) . "', ";
    $sql .= "code='" . db_escape(i($state['code'])) . "' ";
    $sql .= "WHERE id='" . db_escape(i($state['id'])) . "' ";
    $sql .= "LIMIT 1;";    // For update_state statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  //
  // TERRITORY QUERIES
  //

  // Find all territories, ordered by state_id
  function find_all_territories() {
    global $db;
    $sql = "SELECT * FROM territories ";
    $sql .= "ORDER BY state_id ASC, position ASC;";
    $territory_result = db_query($db, $sql);
    return $territory_result;
  }

  // Find all territories whose state_id (foreign key) matches this id
  function find_territories_for_state_id($state_id=0) {
    global $db;
    $sql = "SELECT * FROM territories ";
    $sql .= "WHERE state_id='" . $state_id . "' ";
    $sql .= "ORDER BY position ASC;";
    $territory_result = db_query($db, $sql);
    return $territory_result;
  }

  // Find territory by ID
  function find_territory_by_id($id=0) {
    global $db;
    $sql = "SELECT * FROM territories ";
    $sql .= "WHERE id='" . $id . "';";
    $territory_result = db_query($db, $sql);
    return $territory_result;
  }

  function validate_territory($territory, $errors=array()) {
    if (is_blank($territory['name'])) {
      $errors[] = "Name cannot be blank.";
    } elseif (!has_length($territory['name'], array('min' => 2, 'max' => 25))) {
      // My custom validation: I feel like 25 should be more than enough characters for any territory name
      $errors[] = "Name must be between 2 and 25 characters.";
    } else if (!valid_name_format($territory['name'])) {
      $errors[] = "Name should only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($territory['position'])) {
      $errors[] = "Position cannot be blank.";
    } elseif (!has_length($territory['position'], array('min' => 2, 'max' => 255))) {
      $errors[] = "Position must be between 2 and 255 characters.";
    } else if (!valid_number_format($territory['position'])) {
      $errors[] = "Position should only contain numeric characters.";
    }

    // This block shouldn't be relevant, because the state id should never be able
    // to be entered by a user. Left here for good measures
    if (is_blank($territory['state_id'])) {
      $errors[] = "State id cannot be blank.";
    } elseif (!has_length($territory['state_id'], array('min' => 1, 'max' => 255))) {
      $errors[] = "State id must be between 1 and 255 characters.";
    } else if (!valid_number_format($territory['state_id'])) {
      $errors[] = "State id must only contain numeric characters.";
    }

    return $errors;
  }

  // Add a new territory to the table
  // Either returns true or an array of errors
  function insert_territory($territory) {
    global $db;

    $errors = validate_territory($territory);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO territories (name, position, state_id)";
    $sql .= "VALUES ('". i($territory['name']) ."', '";
    $sql .= i($territory['position']) ."', '". i($territory['state_id']) ."')";    // For INSERT statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL INSERT territoryment failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Edit a territory record
  // Either returns true or an array of errors
  function update_territory($territory) {
    global $db;

    $errors = validate_territory($territory);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE territories SET ";
    $sql .= "name='" . i($territory['name']) . "', ";
    $sql .= "position='" . i($territory['position']) . "' ";
    $sql .= "WHERE id='" . i($territory['id']) . "' ";
    $sql .= "LIMIT 1;";
    // For update_territory statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE territoryment failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  //
  // SALESPERSON QUERIES
  //

  // Find all salespeople, ordered last_name, first_name
  function find_all_salespeople() {
    global $db;
    $sql = "SELECT * FROM salespeople ";
    $sql .= "ORDER BY last_name ASC, first_name ASC;";
    $salespeople_result = db_query($db, $sql);
    return $salespeople_result;
  }

  // To find salespeople, we need to use the join table.
  // We LEFT JOIN salespeople_territories and then find results
  // in the join table which have the same territory ID.
  function find_salespeople_for_territory_id($territory_id=0) {
    global $db;
    $sql = "SELECT * FROM salespeople ";
    $sql .= "LEFT JOIN salespeople_territories
              ON (salespeople_territories.salesperson_id = salespeople.id) ";
    $sql .= "WHERE salespeople_territories.territory_id='" . i($territory_id) . "' ";
    $sql .= "ORDER BY last_name ASC, first_name ASC;";
    $salespeople_result = db_query($db, $sql);
    return $salespeople_result;
  }

  // Find salesperson using id
  function find_salesperson_by_id($id=0) {
    global $db;
    $sql = "SELECT * FROM salespeople ";
    $sql .= "WHERE id='" . i($id) . "';";
    $salespeople_result = db_query($db, $sql);
    return $salespeople_result;
  }

  function validate_salesperson($salesperson, $errors=array()) {
    // TODO add validations
    if (is_blank($salesperson['first_name'])) {
      $errors[] = "First name cannot be blank.";
    } elseif (!has_length($salesperson['first_name'], array('min' => 2, 'max' => 255))) {
      $errors[] = "First name must be between 2 and 255 characters.";
    } else if (!valid_name_format($salesperson['first_name'])) {
      $errors[] = "First name must only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($salesperson['last_name'])) {
      $errors[] = "Last name cannot be blank.";
    } elseif (!has_length($salesperson['last_name'], array('min' => 2, 'max' => 255))) {
      $errors[] = "Last name must be between 2 and 255 characters.";
    } else if (!valid_name_format($salesperson['last_name'])) {
      $errors[] = "Last name must only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($salesperson['email'])) {
      $errors[] = "Email cannot be blank.";
    } else if (!has_length($salesperson['email'], array('min' => 5, 'max' => 35))) {
      // My custom validation: Bounds for the smallest possible email, and the reasonable max
      $errors[] = "Email must be between 5 and 35 characters.";
    } else if (!valid_email_format($salesperson['email'])) {
      $errors[] = "Email must be a valid format. Only letters, numbers, @, _, and . symbols allowed. Ex. user@site.domain";
    }

    if (is_blank($salesperson['phone'])) {
      $errors[] = "Phone cannot be blank.";
    } elseif (!has_length($salesperson['phone'], array('min' => 10, 'max' => 15))) {
      $errors[] = "Phone must be between 10 and 20 characters. Please include area code.";
    } else if (!valid_phone_format($salesperson['phone'])) {
      $errors[] = "Invalid phone format. Only numbers, (), and - symbols allowed.";
    }

    return $errors;
  }

  // Add a new salesperson to the table
  // Either returns true or an array of errors
  function insert_salesperson($salesperson) {
    global $db;

    $errors = validate_salesperson($salesperson);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "INSERT INTO salespeople (first_name, last_name, phone, email)";
    $sql .= "VALUES ('". db_escape($db, i($salesperson['first_name'])) ."', '";
    $sql .= db_escape($db, i($salesperson['last_name'])) ."', '". db_escape($db,i($salesperson['phone']));
    $sql .= "', '". db_escape($db, i($salesperson['email'])) ."')";
    // For INSERT statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL INSERT statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Edit a salesperson record
  // Either returns true or an array of errors
  function update_salesperson($salesperson) {
    global $db;

    $errors = validate_salesperson($salesperson);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE salespeople SET ";
    $sql .= "first_name='" . db_escape($db, i($salesperson['first_name'])) . "', ";
    $sql .= "last_name='" . db_escape($db, i($salesperson['last_name'])) . "', ";
    $sql .= "email='" . db_escape($db, i($salesperson['email'])) . "', ";
    $sql .= "phone='" . db_escape($db, i($salesperson['phone'])) . "' ";
    $sql .= "WHERE id='" . db_escape($db, i($salesperson['id'])) . "' ";
    $sql .= "LIMIT 1;";

    // For update_salesperson statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // To find territories, we need to use the join table.
  // We LEFT JOIN salespeople_territories and then find results
  // in the join table which have the same salesperson ID.
  function find_territories_by_salesperson_id($id=0) {
    global $db;
    $sql = "SELECT * FROM territories ";
    $sql .= "LEFT JOIN salespeople_territories
              ON (territories.id = salespeople_territories.territory_id) ";
    $sql .= "WHERE salespeople_territories.salesperson_id='" . db_escape($db, i($id)) . "' ";
    $sql .= "ORDER BY territories.name ASC;";

    $territories_result = db_query($db, $sql);
    return $territories_result;
  }

  //
  // USER QUERIES
  //

  // Find all users, ordered last_name, first_name
  function find_all_users() {
    global $db;
    $sql = "SELECT * FROM users ";
    $sql .= "ORDER BY last_name ASC, first_name ASC;";
    $users_result = db_query($db, $sql);
    return $users_result;
  }

  // Find user using id
  function find_user_by_id($id=0) {
    global $db;
    $sql = "SELECT * FROM users WHERE id='" . db_escape($db, i($id)) . "' LIMIT 1;";

    $users_result = db_query($db, $sql);
    return $users_result;
  }

  function validate_user($user, $errors=array()) {
    if (is_blank($user['first_name'])) {
      $errors[] = "First name cannot be blank.";
    } elseif (!has_length($user['first_name'], array('min' => 2, 'max' => 25))) {
      // My custom validation: Reasonable bounds for username length
      $errors[] = "First name must be between 2 and 25 characters.";
    } else if (!valid_name_format($user['first_name'])) {
      $errors[] = "First name must only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($user['last_name'])) {
      $errors[] = "Last name cannot be blank.";
    } elseif (!has_length($user['last_name'], array('min' => 2, 'max' => 25))) {
      $errors[] = "Last name must be between 2 and 25 characters.";
    } else if (!valid_name_format($user['last_name'])) {
      $errors[] = "Last name must only contain letters, spaces, and apostrophes.";
    }

    if (is_blank($user['email'])) {
      $errors[] = "Email cannot be blank.";
    } else if (!has_length($user['email'], array('min' => 5, 'max' => 35))) {
      // My custom validation: Bounds for the smallest possible email, and the reasonable max
      $errors[] = "Email must be between 5 and 35 characters.";
    } else if (!has_valid_email_format($user['email'])) {
      $errors[] = "Email must be a valid format. Only letters, numbers, @, _, and . symbols allowed. Ex. user@site.domain";
    }

    if (is_blank($user['username'])) {
      $errors[] = "Username cannot be blank.";
    } else if (!has_length($user['username'], array('min' => 2, 'max' => 25))) {
      // My custom validation: Reasonable bounds for username
      $errors[] = "Username must be between 2 and 25 characters.";
    } else if (!valid_username_format($user['username'])) {
      $errors[] = "Invalid username format. Only letters, numbers, and _ symbols allowed.";
    }

    return $errors;
  }

  // Add a new user to the table
  // Either returns true or an array of errors
  function insert_user($user) {
    global $db;

    $errors = validate_user($user);
    if (!empty($errors)) {
      return $errors;
    }

    $created_at = date("Y-m-d H:i:s");
    $sql = "INSERT INTO users ";
    $sql .= "(first_name, last_name, email, username, created_at) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, i($user['first_name'])) . "',";
    $sql .= "'" . db_escape($db, i($user['last_name'])) . "',";
    $sql .= "'" . db_escape($db, i($user['email'])) . "',";
    $sql .= "'" . db_escape($db, i($user['username'])) . "',";
    $sql .= "'" . $created_at . "',";
    $sql .= ");";

    // For INSERT statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL INSERT statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Edit a user record
  // Either returns true or an array of errors
  function update_user($user) {
    global $db;

    $errors = validate_user($user);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE users SET ";
    $sql .= "first_name='" . db_escape($db, i($user['first_name'])) . "', ";
    $sql .= "last_name='" . db_escape($db, i($user['last_name'])) . "', ";
    $sql .= "email='" . db_escape($db, i($user['email'])) . "', ";
    $sql .= "username='" . db_escape($db, i($user['username'])) . "' ";
    $sql .= "WHERE id='" . db_escape($db, i($user['id'])) . "' ";
    $sql .= "LIMIT 1;";

    // For update_user statments, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

?>
