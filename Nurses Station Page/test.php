<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>HTML5 Starter Template</title>
  <meta name="description" content="Starter Template">
  <meta name="author" content="Gregry Pike">
  <!-- For the toast messages -->
  <link href="css/toast.css" rel="stylesheet">
</head>
<body>
<script src="js/scripts.js"></script>

<?php
// Calculate the date 19 years ago in the format Year-Month-Day
$nineteenYearsAgo = date('Y-m-d', strtotime('-19 years'));
?>

<!-- HTML input field -->
<input type="date" name="birthdate" max="<?php echo $nineteenYearsAgo; ?>">


</body>
</html>