<?php
require_once('../../../private/initialize.php');

if (!is_post_request() && !isset($_GET['id'])) { // Must have a state id for context
  redirect_to('../states/index.php');
}

// Set default values for all variables the page needs.
$errors = array();
$territory = array(
  'name' => '',
  'position' => '',
  'state_id' => ''
);

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}

if(is_post_request()) {

  // Confirm that values are present before accessing them.
  if(isset($_POST['name'])) { $territory['name'] = $_POST['name']; }
  if(isset($_POST['position'])) { $territory['position'] = $_POST['position']; }
  if(isset($_GET['id'])) {
    $territory['state_id'] = $_GET['id'];
    $id = $territory['state_id'];
  } else {
    // redirect_to('../states/index.php'); // The post request has no state_id context
  }

  $result = insert_territory($territory);
  if($result === true) {
    $new_id = db_insert_id($db); // Get the id of the last entry
    redirect_to('show.php?id=' . $new_id);
  } else {
    $errors = $result;
  }

}
?>
<?php $page_title = 'Staff: New Territory'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="main-content">
  <a href="../states/show.php?id=<?php echo $id; ?>">Back to State Details</a><br />

  <h1>New Territory</h1>

  <?php echo display_errors($errors); ?>

  <form action="new.php?id=<?php echo $id; ?>" method="post">
    Name:<br />
    <input type="text" name="name" value="<?php echo $territory['name']; ?>" /><br />
    Position:<br />
    <input type="text" name="position" value="<?php echo $territory['position']; ?>" /><br />
    <br />
    <input type="submit" name="submit" value="Create"  />
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
