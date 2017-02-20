<?php
require_once('../../../private/initialize.php');

$territories_result = find_territory_by_id($_GET['id']);
if (db_num_rows($territories_result) === 0) { // Invalid territory id
  redirect_to('../states/index.php');
}
// No loop, only one result
$territory = db_fetch_assoc($territories_result);

// Set default values for all variables the page needs.
$errors = array();

if(is_post_request()) {

  // Confirm that values are present before accessing them.
  if(isset($_POST['name'])) { $territory['name'] = $_POST['name']; }
  if(isset($_POST['position'])) { $territory['position'] = $_POST['position']; }
  $territory = obj_o($territory);
  // Do not change the old state_id value

  $result = update_territory($territory);
  if($result === true) {
    redirect_to('show.php?id=' . u($territory['id']));
  } else {
    $errors = $result;
  }

}

?>
<?php $page_title = 'Staff: Edit Territory ' . $territory['name']; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="main-content">
  <a href="../states/show.php?id=<?php echo $territory['state_id']; ?>">Back to State Details</a><br />

  <h1>Edit Territory: <?php echo $territory['name']; ?></h1>

  <form action="edit.php?id=<?php echo u($_GET['id']); ?>" method="post">
    Name:<br />
    <input type="text" name="name" value="<?php echo $territory['name']; ?>" /><br />
    Position:<br />
    <input type="text" name="position" value="<?php echo $territory['position']; ?>" /><br />
    <br />
    <input type="submit" name="submit" value="Update"  />
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
