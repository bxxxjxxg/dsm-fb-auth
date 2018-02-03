<html>
<head>
<?php
	$configs = include('config.php');
	echo '<title>' . $configs['title'] . '</title>';
?>
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
</head>
<body>

<?php
session_start();
require_once 'functions.php';

// load configuration
$configs = include('config.php');

$fb = new Facebook\Facebook([
  'app_id' => $configs['app_id'],
  'app_secret' => $configs['app_secret'],
  'default_graph_version' => 'v2.2',
]);

if (isset($_GET["group_id"]) && isset($_COOKIE['fb_access_token'])) {
  $group_id = $_GET["group_id"];
  $accessToken = $_COOKIE['fb_access_token'];
  $fbid = get_fbid($fb, $accessToken);
  $members = get_members($fb, $group_id, $accessToken);

  if (isMember($fbid, $members)) {
	$url = $configs['dsm_auth_url'] . $configs['dsm_auth_params'];
	$url .= '&account=' . $configs['groups'][$group_id]['account'];
	$url .= '&passwd=' . $configs['groups'][$group_id]['passwd'];
	$data = file_get_contents($url);
	$json = json_decode($data, true);
    setcookie("id", $json["data"]["sid"], time() + 3600, "/");
    setcookie("stay_login", "1", time() + 3600, "/");
    header('Location: ' . $configs['dsm_url']);
  }
  exit;
}

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } 

  $permissions = [];
  $loginUrl = $helper->getLoginUrl($configs['redirect_url'], $permissions);
  echo '<form><a href="' . htmlspecialchars($loginUrl) . '">';
  echo '<button type="button" class="button buttonBlue">Login with Facebook';
  echo '<div class="ripples buttonRipples"><span class="ripplesCircle"></span></div>';
  echo '</button></a></form>';
} else {

  // The OAuth 2.0 client handler helps us manage access tokens
  $oAuth2Client = $fb->getOAuth2Client();

  // Get the access token metadata from /debug_token
  $tokenMetadata = $oAuth2Client->debugToken($accessToken);
  $tokenMetadata->validateAppId($configs['app_id']);
  $tokenMetadata->validateExpiration();

  if (! $accessToken->isLongLived()) {
    try {
      $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
      echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
      exit;
    }
  }

  setcookie('fb_access_token', (string) $accessToken);
  
  $fbid = get_fbid($fb, $accessToken);
  $valid_count = 0;
  echo '<div class="collection">';
  foreach ($configs['groups'] as $group_id => $tmp) {
    $members = get_members($fb, $group_id, $accessToken);
	if (isMember($fbid, $members)) {
	  $group_name = get_gname($fb, $group_id, $accessToken);
	  $group_cover = get_gcover($fb, $group_id, $accessToken);
	  $group_y_shift = -get_gcover_yshift($fb, $group_id, $accessToken) - 43; 
	  echo '<div class="blog-container">';
	  echo '  <div class="blog-header">';
	  echo '    <div class="blog-cover" style="';
	  echo 'background-image: url(' . $group_cover . ');';
	  echo 'background-position: 0px ' . (string)$group_y_shift .'px;"></div>';
	  echo '  </div>';
	  echo '  <div class="blog-body">';
	  echo '    <div class="blog-title">';
	  echo '      <h1><a href="?group_id=' . $group_id .'">' . $group_name . '</a></h1>';
	  echo '    </div>';
	  echo '  </div>';
	  echo '  <div class="blog-footer"></div>';
	  echo '  </div>';
	  $valid_count += 1;
	}
  }
  if ($valid_count <= 0) {
	  echo "<h3>You're not allowed to access.</h3>";
  }
  echo '</div>';
}
?>

<footer>
  <p>Github: 
  <a href="https://github.com/bxxxjxxg/dsm-fb-auth/">bxxxjxxg/dsm-fb-auth</a>,  
  <a href="privacy.php" target="_blank">Privacy</a>
  </p>
</footer>

</body>
</html>
