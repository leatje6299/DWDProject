<?php
//==============================================================================
// index.php for SimpleExample app //

// Create f3 object then set various global properties of it
// These are available to the routing code below, but also to any
// classes defined in autoloaded definitions

$home = '/home/' . get_current_user();

$f3 = require ($home . '/AboveWebRoot/fatfree-master/lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD', 'autoload/;' . $home . '/AboveWebRoot/autoload/');

$db = DatabaseConnection::connect(); // defined as autoloaded class in AboveWebRoot/autoload/
$f3->set('DB', $db);

$f3->set('DEBUG', 3);		// set maximum debug level
$f3->set('UI', 'ui/');		// folder for View templates

new \DB\SQL\Session($f3->get('DB'));

if (!$f3->exists('SESSION.username'))
    $f3->set('SESSION.username', 'UNSET');
$f3->set('thisIsLoginPage', 'false');
//==============================================================================
// Simple Example URL application routings

//==============================================================================
// Home Page
//==============================================================================
$f3->route(
    'GET /',
    function ($f3) {
        $f3->set('html_title', 'Simple Example Home');
        $f3->set('content', 'home.html');
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Log In Messages
//==============================================================================
$f3->route(
    'GET /login/@msg',				// @msg is a parameter that tells us which message to give the user
    function ($f3) {
        switch ($f3->get('PARAMS.msg')) {		// PARAMS.msg is whatever was the last element of the URL
            case "err":
                $msg = "Wrong user name and/or password; please try again.";
                break;
            case "lo":
                $msg = "You have been logged out.";
                break;
            default:						// this is the case if neither of the above cases is matched
                $msg = "Login here";
        }
        $f3->set('html_title', 'Simple Example Home');
        $f3->set('message', $msg);				// set message that will be shown to user in the login.html template
        $f3->set('thisIsLoginPage', 'true');	// set flag that will be tested in layout.html, to say this is login page
        $f3->set('content', 'login.html');		// the login form that will be shown to the user
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Log In Route
//==============================================================================
$f3->route(
    'GET /login',
    function ($f3) {
        $f3->set('html_title', 'Simple Example Home');
        $f3->set('content', 'login.html');
        $f3->set('thisIsLoginPage', 'true');
        echo template::instance()->render('layout.html');
    }
);

$f3->route(
    'POST /login',
    function ($f3) {
        $username = $f3->get('POST.username');
        $password = $f3->get('POST.password');

        $users = new SimpleController('users');
        if ($users->loginUser($f3->get('POST.username'), $f3->get('POST.password'))) {
            $f3->set('SESSION.username', $f3->get('POST.username'));
            $f3->reroute('/map');
            return true;
        } else {
            $errorMsg = 'Wrong username or password';
            echo json_encode(['error' => $errorMsg]);
            http_response_code(400);
            return false;
        }
    }
);
//==============================================================================
// Sign Up Route
//==============================================================================
$f3->route(
    'POST /register',
    function ($f3) {
        $signupdata = array ();
        $email = $f3->get('POST.email');
        $signupdata["username"] = $f3->get('POST.username');
        $signupdata["password"] = password_hash($f3->get('POST.password'), PASSWORD_DEFAULT);

        $users = new SimpleController('users');
        $userData = $users->getData();

        if (!preg_match('/^[a-zA-Z0-9._%+-]+@ed\.ac\.uk$/', $email)) {
            $errorMsg = 'Use a valid Edinburgh e-mail address';
            echo json_encode(['error' => $errorMsg]);
            http_response_code(400);
            return false;
        }

        foreach ($userData as $user) {
            if ($user->username == $signupdata["username"]) {
                $errorMsg = 'Username already exists';
                echo json_encode(['error' => $errorMsg]);
                http_response_code(400);
                return false;
            }
        }

        $users->setNewUser($signupdata);
        if ($users->loginUser($signupdata["username"], $f3->get('POST.password'))) {
            $f3->set('SESSION.username', $signupdata["username"]);
            $f3->reroute('/map');
        }
    }
);
//==============================================================================
// Sign Up Route
//==============================================================================
$f3->route(
    'POST /logout',
    function ($f3) {
        $f3->set('SESSION.username', 'UNSET');
        $f3->reroute('/login');		// return to login page with the message that the user has been logged out
    }
);
//==============================================================================
// Account
//==============================================================================
$f3->route(
    'POST /account',
    function ($f3) {
        $notesController = new SimpleController('notes');
        $userController = new SimpleController('users');
        $userID = $userController->getUserId($f3, $f3->get('SESSION.username'));
        $notes = $notesController->getNotesByUser($f3, $userID);

        $f3->set('notes', $notes);
        $f3->set('html_title', 'Account');
        $f3->set('content', 'account.html');
        echo template::instance()->render('layout.html');
    }
);

$f3->route(
    'GET /acc',
    function ($f3) {
        $notesController = new SimpleController('notes');
        $userController = new SimpleController('users');
        $userID = $userController->getUserId($f3, $f3->get('SESSION.username'));
        $notes = $notesController->getNotesByUser($f3, $userID);

        $f3->set('notes', $notes);
        $f3->set('html_title', "Info");
        $f3->set('content', 'account.html');
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Change username
//==============================================================================
$f3->route(
    'POST /changeUsername',
    function ($f3) {
        $userController = new SimpleController('users');
        $userID = $userController->getUserId($f3, $f3->get('SESSION.username'));

        $newUsername = $f3->get('POST.username');
        $password = $f3->get('POST.password');

        if (!$userController->loginUser($f3->get('SESSION.username'), $password)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password']);
            return;
        }
        if ($userController->usernameExists($f3, $newUsername)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username already exists']);
        } else {
            $userController->changeUsername($f3, $userID, $newUsername);
            http_response_code(200);
            $f3->set('SESSION.username', $newUsername);
            $userController->loginUser($f3->get('SESSION.username'), $password);
            echo json_encode(['success' => 'Username changed successfully']);
        }
    }
);
//==============================================================================
// Change password
//==============================================================================
$f3->route(
    'POST /changePassword',
    function ($f3) {
        $userController = new SimpleController('users');
        $userID = $userController->getUserId($f3, $f3->get('SESSION.username'));

        $oldPassword = $f3->get('POST.old_password');
        $newPassword = $f3->get('POST.password');
        if (!empty ($newPassword) && $userController->loginUser($f3->get('SESSION.username'), $oldPassword)) {
            $userController->changePassword($f3, $userID, $newPassword);
            echo json_encode(['success' => 'Password changed successfully']);
        } else {
            echo json_encode(['error' => 'Old password is incorrect']);
        }
    }
);
//==============================================================================
// Load the data of the third places to display it
//==============================================================================
function LoadThirdplacesData($f3)
{
    $thirdplaces = new SimpleController('thirdplaces');
    $thirdplacesData = $thirdplaces->getThirdplaceData($f3, []);
    $f3->set('thirdplacesData', $thirdplacesData);
}
;

$f3->route('GET /updatePins', function ($f3) {
    $pinsHTML = '';
    $thirdplaces = new SimpleController('thirdplaces');

    $types = $f3->get('GET.types');
    if (empty ($types)) {
        $thirdplacesData = $thirdplaces->getThirdplaceData($f3, '');
    } else {
        $thirdplacesData = [];
        foreach ($types as $type) {
            $thirdplacesData = array_merge($thirdplacesData, $thirdplaces->getThirdplaceData($f3, $type));
        }
    }

    foreach ($thirdplacesData as $place) {
        $pinsHTML .= '<div class="pin ' . $place['type'] . '" style="left: ' . ($place['position_x'] - 10) . 'px; top: ' . ($place['position_y'] - 15) . 'px;" onclick="openModal(\'' . $place['name'] . '\')">' .
            '<p class="pin-number">' . $place['note_count'] . '</p>' .
            '</div>';
    }
    echo $pinsHTML;
});
//==============================================================================
// Map
//==============================================================================
$f3->route(
    'GET /map',
    function ($f3) {
        LoadThirdplacesData($f3);
        $f3->set('error', '');
        echo template::instance()->render('map.html');
    }
);
//==============================================================================
// Post Reason
//==============================================================================
function checkProfanityAndInsertNote($f3, $reason, $thirdplace_name, $isAnonymous)
{
    $url = "https://www.purgomalum.com/service/containsprofanity?text=" . urlencode($reason);
    $containsProfanity = file_get_contents($url);

    if ($containsProfanity === "true") {
        $errorMsg = 'Your input contains profanity. Please try again.';
        echo json_encode(['error' => $errorMsg]);
        http_response_code(400);
        return false;
    }

    $controller = new SimpleController('notes');
    $userID = $controller->getUserId($f3, $f3->get('SESSION.username'));
    $thirdplaceID = $controller->getThirdplaceByName($f3, $thirdplace_name);
    $noteInsertionSuccess = $controller->insertNote($reason, $thirdplaceID['id'], $userID, $isAnonymous);

    if ($noteInsertionSuccess) {
        echo json_encode(['success' => 'Your note was submitted']);
        $f3->status(200);
        return true;
    } else {
        echo json_encode(['error' => 'Your note could not be submitted']);
        http_response_code(500);
        return false;
    }
}

$f3->route(
    'POST /submitReason',
    function ($f3) {
        $reason = $f3->get('POST.reason');
        $thirdplace_name = $f3->get('POST.thirdplace');
        $isAnonymous = $f3->get('POST.isAnonymous');
        checkProfanityAndInsertNote($f3, $reason, $thirdplace_name, $isAnonymous);
    }
);

$f3->route(
    'POST /submitReasonAndLocation',
    function ($f3) {
        $thirdplace_name = $f3->get('POST.thirdplace');
        $thirdplace_pos_x = $f3->get('POST.position_x');
        $thirdplace_pos_y = $f3->get('POST.position_y');
        $thirdplace_type = $f3->get('POST.thirdplace_type');
        $isAnonymous = $f3->get('POST.isAnonymous');
        $reason = $f3->get('POST.reason');

        $thirdplace_controller = new SimpleController('thirdplaces');

        $existing_thirdplace = $thirdplace_controller->getThirdplaceByName($f3, $thirdplace_name);
        if ($existing_thirdplace) {
            checkProfanityAndInsertNote($f3, $reason, $thirdplace_name, $isAnonymous);
            return;
        }

        $thirdplace_controller->insertThirdplace($thirdplace_name, $thirdplace_pos_x, $thirdplace_pos_y, $thirdplace_type);

        checkProfanityAndInsertNote($f3, $reason, $thirdplace_name, $isAnonymous);
    }
);

$f3->route(
    'POST /modifyNote',
    function ($f3) {
        $noteId = $f3->get('POST.noteId');
        $newReason = $f3->get('POST.newReason');
        $noteController = new SimpleController('notes');

        // Check for profanity in the newReason
        $url = "https://www.purgomalum.com/service/containsprofanity?text=" . urlencode($newReason);
        $containsProfanity = file_get_contents($url);

        if ($containsProfanity === "true") {
            $errorMsg = 'Your input contains profanity. Please try again.';
            echo json_encode(['error' => $errorMsg]);
            http_response_code(400);
            return false;
        }

        $result = $noteController->changeNote($f3, $noteId, $newReason);

        if ($result) {
            echo json_encode(['success' => 'Note updated successfully']);
        } else {
            echo json_encode(['error' => 'Error updating note']);
            http_response_code(500);
        }
    }
);
//==============================================================================
// Submit problem
//==============================================================================
$f3->route(
    'POST /reportAProblem',
    function ($f3) {
        $content = $f3->get('POST.content');

        // Check for profanity
        $url = "https://www.purgomalum.com/service/containsprofanity?text=" . urlencode($content);
        $containsProfanity = file_get_contents($url);

        if ($containsProfanity === "true") {
            $errorMsg = 'Your input contains profanity. Please try again.';
            echo json_encode(['error' => $errorMsg]);
            http_response_code(400);
            return false;
        }

        $controller = new SimpleController('reports');
        $userID = $controller->getUserId($f3, $f3->get('SESSION.username'));
        $insertionSuccess = $controller->insertReport($content, $userID);

        if ($insertionSuccess) {
            echo json_encode(['success' => 'Your report was submitted']);
        } else {
            echo json_encode(['error' => 'Your report could not be submitted']);
            http_response_code(500);
        }
    }
);

$f3->route(
    'GET /reports',
    function ($f3) {

        $f3->set('html_title', "Reports");
        $f3->set('content', 'reports.html');
        echo template::instance()->render('layout.html');

    }
);
//==============================================================================
// Search Map for thirdplaces
//==============================================================================
$f3->route(
    'GET /search/@query',
    function ($f3) {
        $str = $f3->get('PARAMS.query');
        $thirdplaces = new SimpleController('thirdplaces');
        $userHint = $thirdplaces->getUserHint($str);
        echo $userHint;
    }
);

$f3->route(
    'GET /location/@query',
    function ($f3) {
        $location = $f3->get('PARAMS.query');
        $thirdplaces = new SimpleController('thirdplaces');
        $positionData = $thirdplaces->getThirdplaceByName($f3, $location);
        $x = $positionData['position_x'];
        $y = $positionData['position_y'];

        // Print out x and y coordinates
        echo "$x,$y";

    }
);

$f3->route(
    'GET /notesByThirdplace/@query',
    function ($f3) {
        $thirdplace_name = $f3->get('PARAMS.query');
        pprint_var($thirdplace_name);
        $controller = new SimpleController('notes');
        $notes = $controller->getNotesByThirdplace($f3, $thirdplace_name);
        echo json_encode($notes);
    }
);
//==============================================================================
// Delete reason
//==============================================================================
$f3->route(
    'DELETE /notes/@id',
    function ($f3) {
        $noteId = $f3->get('PARAMS.id');
        $notes = new SimpleController('notes');
        if ($notes->deleteFromDatabase($noteId)) {
            echo json_encode(['success' => 'Note deleted']);
        } else {
            echo json_encode(['error' => 'Note not found']);
            http_response_code(400);

        }
    }
);
//==============================================================================
// Delete acc
//==============================================================================
$f3->route(
    'POST /deleteAccount',
    function ($f3) {
        $userController = new SimpleController('users');
        $userId = $userController->getUserId($f3, $f3->get('SESSION.username'));
        $noteController = new SimpleController('notes');
        $reportController = new SimpleController('reports');

        $notes = $noteController->getNotesByUser($f3, $userId);
        $reports = $reportController->getReportsByUser($f3, $userId);

        foreach ($reports as $report) {
            $reportController->deleteFromDatabase($report['id']);
        }
        foreach ($notes as $note) {
            $noteController->deleteFromDatabase($note['id']);
        }

        $userController->deleteFromDatabase($userId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        $f3->set('SESSION.username', 'UNSET');
    }
);
//==============================================================================
// Report
//==============================================================================
$f3->route(
    'GET /report',
    function ($f3) {
        $f3->set('html_title', "Report");
        $f3->set('content', 'report.html');
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Info
//==============================================================================
$f3->route(
    'GET /info',
    function ($f3) {
        $f3->set('html_title', "Info");
        $f3->set('content', 'info.html');
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
function pprint_var($var)
{
    ob_start();
    var_dump($var);
    return ob_get_clean();
}

$f3->set(
    'ONERROR', // what to do if something goes wrong.
    function ($f3) {
        $f3->set('html_title', $f3['ERROR']['code']);
        $f3->set('DUMP', pprint_var($f3['ERROR']));
        $f3->set('content', 'error.html');
        echo template::instance()->render('layout.html');
    }
);

//==============================================================================
// Run the FFF engine //
$f3->run();
