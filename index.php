<?php
//==============================================================================
// index.php for SimpleExample app //

// Create f3 object then set various global properties of it
// These are available to the routing code below, but also to any
// classes defined in autoloaded definitions

$home = '/home/'.get_current_user();

$f3 = require($home.'/AboveWebRoot/fatfree-master/lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD','autoload/;'.$home.'/AboveWebRoot/autoload/');

$db = DatabaseConnection::connect(); // defined as autoloaded class in AboveWebRoot/autoload/
$f3->set('DB', $db);

$f3->set('DEBUG',3);		// set maximum debug level
$f3->set('UI','ui/');		// folder for View templates

new \DB\SQL\Session($f3->get('DB'));

if (!$f3->exists('SESSION.username')) $f3->set('SESSION.username', 'UNSET');
$f3->set('thisIsLoginPage', 'false');
//==============================================================================
// Simple Example URL application routings

//==============================================================================
// Home Page
//==============================================================================
$f3->route('GET /',
    function ($f3)
    {
        $f3->set('html_title','Simple Example Home');
        $f3->set('content','home.html');
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Log In Messages
//==============================================================================
$f3->route('GET /login/@msg',				// @msg is a parameter that tells us which message to give the user
    function($f3) {
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
        $f3->set('html_title','Simple Example Home');
        $f3->set('message', $msg);				// set message that will be shown to user in the login.html template
        $f3->set('thisIsLoginPage', 'true');	// set flag that will be tested in layout.html, to say this is login page
        $f3->set('content', 'login.html');		// the login form that will be shown to the user
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Log In Route
//==============================================================================
$f3->route('GET /login',
    function($f3)
    {
        $f3->set('html_title','Simple Example Home');
        $f3->set('content','login.html');
        echo template::instance()->render('layout.html');
    }
);

$f3->route('POST /login',
    function($f3)
    {
        $username = $f3->get('POST.username');
        $password = $f3->get('POST.password');

        $users = new SimpleController('users');
        if($users->loginUser($f3->get('POST.username'), $f3->get('POST.password')))
        {
            $f3->set('SESSION.username', $f3->get('POST.username'));
            $f3->reroute('/map');
        }
        else
        {
            echo 'wrong password';
        }
    }
);
//==============================================================================
// Sign Up Route
//==============================================================================
$f3->route('POST /register',
    function($f3)
    {
        $signupdata = array();
        $email = $f3->get('POST.userEmail');
        $signupdata["username"] = $f3->get('POST.newUsername');
        $signupdata["password"] = $f3->get('POST.newPassword');

        $users = new SimpleController('users');
        $userData = $users->GetData();

        if(!preg_match('/^[a-zA-Z0-9._%+-]+@ed\.ac\.uk$/', $email ))
        {
            echo 'Use a valid Edinburgh e-mail address';
            return;
        }

        foreach($userData as $user)
        {
            if($user->username == $signupdata["username"])
            {
                echo 'Username already exists';
                return;
            }
        }

        $users->setNewUser($signupdata);
        if($users->loginUser($signupdata["username"],  $signupdata["password"]))
        {
            $f3->set('SESSION.username', $signupdata["username"]);
            $f3->reroute('/map');
        }
    }
);
//==============================================================================
// Sign Up Route
//==============================================================================
$f3->route('POST /logout',
    function($f3) {
        $f3->set('SESSION.username', 'UNSET');
        $f3->reroute('/login');		// return to login page with the message that the user has been logged out
    }
);
//==============================================================================
// Account
//==============================================================================
$f3->route('POST /account',
    function($f3)
    {
        $notesController = new SimpleController('notes');
        $userController = new SimpleController('users');
        $userID = $userController->getUserId($f3, $f3->get('SESSION.username'));
        $notes = $notesController->getNotesByUser($f3,$userID);
        
        $f3->set('notes', $notes);
        $f3->set('html_title', 'Account');
        $f3->set('content', 'account.html');	
        echo template::instance()->render('layout.html');
    }
);
//==============================================================================
// Load the data of the third places to display it
//==============================================================================
function LoadThirdplacesData($f3){
        $thirdplaces = new SimpleController('thirdplaces');
        $thirdplacesData = $thirdplaces->getData();

        $f3->set('thirdplacesData', $thirdplacesData);

}
//==============================================================================
// Map
//==============================================================================
$f3->route('GET /map',
    function($f3)
    {
        LoadThirdplacesData($f3);
        $f3->set('error', '');
        echo template::instance()->render('map.html');
    }
);
//==============================================================================
// Post Reason
//==============================================================================
$f3->route('POST /submitReason',
    function($f3)
    {
        $reason = $f3->get('POST.reason');
         //Check for profanity
        $url = "https://www.purgomalum.com/service/containsprofanity?text=" . urlencode($reason);
        $containsProfanity = file_get_contents($url);

        if ($containsProfanity === "true") {
            $f3->error(400, 'Your input contains profanity. Please try again.');
            return;
        }

        $controller = new SimpleController('notes');
        $userID = $controller->getUserId($f3, $f3->get('SESSION.username'));
        $thirdplaceID = $controller->getThirdplaceId($f3, $f3->get('POST.thirdplace'));
        $controller->insertNote($reason, $thirdplaceID, $userID);

        LoadThirdplacesData($f3);
        $f3->reroute('/map');
    }
);
//==============================================================================
// Search Map for thirdplaces
//==============================================================================
$f3->route('GET /search/@query',
    function($f3) {
        $str = $f3->get('PARAMS.query');
        $thirdplaces = new SimpleController('thirdplaces');
        $userHint = $thirdplaces->getUserHint($str);
        echo $userHint;
    });
//==============================================================================
// Report
//==============================================================================
$f3->route('GET /report',
    function($f3)
    {
        $f3->set('html_title',"Report");
        $f3->set('content','report.html');
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

$f3->set('ONERROR', // what to do if something goes wrong.
    function($f3) {
        $f3->set('html_title',$f3['ERROR']['code']);
        $f3->set('DUMP', pprint_var($f3['ERROR']));
        $f3->set('content','error.html');
        echo template::instance()->render('layout.html');
    }
);

//==============================================================================
// Run the FFF engine //
$f3->run();
