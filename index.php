<?php
require("Captcha.php");

$captcha = new Captcha();
if (empty($_POST['captcha_code'])) {
?>
<!DOCTYPE HTML>
<html>
  <head>
  </head>
  <body>
    
    <form action="#" method="POST">
      <input type="email" name="email" placeholder="email"></input>
<?php
    $captcha->getHtml();
?>
      <button type="submit">OK</button>
    </form>

  </body>
</html>

<?php
} else {
  try {
    if ($captcha->isCaptchaResolved()) 
      echo "OK c'est good";
    else 
      echo "bad luck";
  } catch (Exception $e) {
    echo "Error (".$e->getCode().") : ".$e->getMessage();
    die();
  }
}
