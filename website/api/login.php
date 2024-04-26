<?php
/**
 * Returns a GUIDv4 string
 *
 * Uses the best cryptographically secure method 
 * for all supported pltforms with fallback to an older, 
 * less secure version.
 *
 * @param bool $trim
 * @return string
 */
function GUIDv4 ($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
              substr($charid,  0,  8).$hyphen.
              substr($charid,  8,  4).$hyphen.
              substr($charid, 12,  4).$hyphen.
              substr($charid, 16,  4).$hyphen.
              substr($charid, 20, 12).
              $rbrace;
    return $guidv4;
}
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
?>

<?php
header('Content-Type: application/json; charset=utf-8');

if (!empty($_POST['username']) && !empty($_POST['password'])) {
	require '../connect.php';

	$user = $_POST['username'];
	$pass = $_POST['password'];
	$res = query_p("SELECT id, password FROM accounts WHERE username = :user",
		[':user' => $user]);
	$userExists = count( $res ) > 0;
	if (!$userExists) {
		http_response_code(400);
		echo json_encode(['msg' => 'User does not exist.']);
	}
	else {
		if ($pass === $res[0]['password']) {
			$req = query_p("SELECT id FROM sessions WHERE id = :session",
				[':session' => session_id()]);
			$sessionExists = count($req) > 0;
			// Gotta handle expired and revoked tokens
			if (isset($_COOKIE['token'])) {
				http_response_code(400);
				echo json_encode(['msg' => 'You are already logged in.']);
			}
			else {
				$newToken = GUID();
				setcookie('token', $newToken, 0, '/', '', false, true);
				query2json("INSERT INTO sessions (id, accountId) VALUES ('$newToken', {$res[0]['id']})");
				echo json_encode(['msg' => 'Success.']);
			}
		}
		else {
			http_response_code(400);
			echo json_encode(['msg' => 'Invalid password.']);
		}
	}
}
else {
	http_response_code(400);
	echo json_encode(['msg' => 'No username and/or password provided.']);
}
?>