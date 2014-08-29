<?php
require("functions.php");

class Captcha {

  function __construct() {
    if (empty($_GET['action'])) return ;
    switch ($_GET['action']) {
    case "grid":
      $this->getGrid();
      break ;
    case "js":
      $this->getJs();
      break ;
    case "image":
      $this->getImage();
      break;
    }

  }

  function getGrid() {
    if (!session_id()){
      session_start();
    }
  
    $_coupon = "blabla_coupon";
    $postID = 42;
    
    unset($_SESSION[$_coupon.'_'.$postID]); //delete old coupon transient if any
    
    $captcha_key = array(arand().uniqid(),arand().uniqid(),arand().uniqid(),arand().uniqid());
    
    $css = ' display:block; background-image:url(?action=image&i='.uniqid().');  float:left; width:75px; height:75px; overflow:hidden; margin:0px; padding:0px; clear:none; ';
    $puzzle[$captcha_key[0]] = 'background-position:0px 0px; width: 75px; height: 75px;'.$css;    
    $puzzle[$captcha_key[1]] = 'background-position:75px 0px; width: 75px; height: 75px;'.$css;   
    $puzzle[$captcha_key[2]] = 'background-position:0px 75px; width: 75px; height: 75px;'.$css;   
    $puzzle[$captcha_key[3]] = 'background-position:75px 75px; width: 75px; height: 75px;'.$css;  
    
    $_code = implode("|",$captcha_key);
    $_coupon = arand().uniqid();
    
    $_SESSION[$_coupon.'_'.$postID] = $_code; //, 60*60*1 ); //Exire after one hour.
    
    $puzzle = shuffle_the_puzzle($puzzle); //lets mix up
    
    $out = '<style type="text/css">
    .captcha .puzzle_container .captcha_grid { overflow:hidden;list-style:none; width:150px; height: 150px}';
    /*/echo "<pre>"; print_r($puzzle); /**/
    foreach($puzzle as $key => $css) {
      $out .= ' .'.$key.' { '.$css.' }'."\n";
    }   
    $out .= '</style>';
    $out .= '<div class="captcha_grid">';

    foreach($puzzle as $class => $css) {
      $out .= '<div class="'.$class.'"></div>';
    }
    $out .= '</div>';
    $out = str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$out), "\0..\37'\\"))); 
    echo '$(".puzzle_container").html("'.$out.'"); $(".captcha_coupon").val("'.$_coupon.'");';
    exit ;
  }

  function getImage() {
    $puzzle_images = array_diff(scandir("puzzle/"), array('..', '.'));
    shuffle($puzzle_images);
    $rand_key = array_rand($puzzle_images, 1);
    $get_img = file_get_contents("puzzle/".$puzzle_images[$rand_key]);
    header('Content-type: image/jpg');
    echo $get_img;
    exit;
  }

  function getHtml() {
    $html = <<<EOF
      <script src="https://vod.ebonylifetv.com/bundles/summviewebony/js/jquery.js"></script>
      <script src="?action=js"></script>
      <div class="captcha">
        <span class="">Drag it to solve it</span>
        <img src="data:image/gif;base64,R0lGODlhKwALAPEAANvb2yUlJYKCgiUlJSH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAKwALAAACMoSOCMuW2diD88UKG95W88uF4DaGWFmhZid93pq+pwxnLUnXh8ou+sSz+T64oCAyTBUAACH5BAkKAAAALAAAAAArAAsAAAI9xI4IyyAPYWOxmoTHrHzzmGHe94xkmJifyqFKQ0pwLLgHa82xrekkDrIBZRQab1jyfY7KTtPimixiUsevAAAh+QQJCgAAACwAAAAAKwALAAACPYSOCMswD2FjqZpqW9xv4g8KE7d54XmMpNSgqLoOpgvC60xjNonnyc7p+VKamKw1zDCMR8rp8pksYlKorgAAIfkECQoAAAAsAAAAACsACwAAAkCEjgjLltnYmJS6Bxt+sfq5ZUyoNJ9HHlEqdCfFrqn7DrE2m7Wdj/2y45FkQ13t5itKdshFExC8YCLOEBX6AhQAADsAAAAAAAAAAAA=" style="display:none;" class="loader"/>
        <div class="puzzle_container">   
        </div>
        <a href="javascript:load_puzzle()" class="refr"> Refresh </a>
      </div>
      <input type="hidden" name="captcha_code" class="captcha_code"/>
      <input type="hidden" name="captcha_coupon" class="captcha_coupon"/>
EOF;
    echo $html;
  }

  function getJs() {
    header("Content-Type: text/css");
    echo file_get_contents("captcha.js");
    exit;
  }

  function isCaptchaResolved() {
    if (empty($_POST['captcha_code'])) throw (new Exception("captcha_code not fullfil", 400));
    if (empty($_POST['captcha_coupon'])) throw (new Exception("captcha_coupon not fullfil", 400));
    
    if (!session_id()){
      session_start();
    }

    $_coupon = $_POST['captcha_coupon'];
    $postID = 42;

    $serverCode = $_SESSION[$_coupon.'_'.$postID];
    $userCode = $_POST['captcha_code'];

    return ($serverCode === $userCode);
  }
}


