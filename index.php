<?php

require_once 'models/countWords.class.php';

if(!empty($_POST['sFilePath']))
{
    $sFilePath = $_POST['sFilePath'];
    $oWordCount = new CountWords($sFilePath);
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Count words from File</title>
</head>
<body>
<?php if(!empty($sFilePath)) { ?>
<h1>
    The File <?= $sFilePath ?> contains <?php echo $oWordCount->countWords(); ?> words.
</h1>
<?php } ?>
<form action="index.php" name="countWords" method="post">
    <input type="text" value="" name="sFilePath" />
    <button type="submit">Count words</button>
</form>
</body>
</html>