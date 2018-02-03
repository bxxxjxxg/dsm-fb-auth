<?php
require_once 'src/Facebook/autoload.php';

function send_request($fb, $req_string, $accessToken) {
  try {
    return $fb->get($req_string, $accessToken);
  } catch (Facebook\Exceptions\FacebookResponseException $e) {
	echo $e; 
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
	echo $e;
  } 
  exit;
}

function get_fbid($fb, $accessToken) {
  $response = send_request($fb, '/me', $accessToken);
  $graphNode = $response->getGraphNode();
  return $graphNode['id'];
}

function get_gname($fb, $group_id, $accessToken) {
	$response = send_request($fb, '/' . $group_id, $accessToken);
    $graphNode = $response->getGraphNode();
	return $graphNode['name'];
}

function get_gcover($fb, $group_id, $accessToken) {
	$response = send_request($fb, '/' . $group_id . '?fields=cover', $accessToken);
    $graphNode = $response->getGraphNode();
	return $graphNode['cover']['source'];
}

function get_gcover_yshift($fb, $group_id, $accessToken) {
	$response = send_request($fb, '/' . $group_id . '?fields=cover', $accessToken);
    $graphNode = $response->getGraphNode();
	return $graphNode['cover']['offset_y'];
}

function get_members($fb, $group_id, $accessToken) {
  $response = send_request($fb, '/' . $group_id . '/members', $accessToken);
  $graphEdge = $response->getGraphEdge();
  $members = array();
  while ($graphEdge != null) {
    $members = array_merge($members, $graphEdge->asArray());
	$graphEdge = $fb->next($graphEdge);
  }
  return $members;
}

function isMember($fbid, $members) {
  foreach ($members as $member) {
	if (strcmp($member['id'], $fbid) == 0) {
	  return TRUE;
	}
  }
  return FALSE;
}

?>
