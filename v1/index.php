<?php

//including the required files
require_once '../include/DbOperation.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/* *
 * URL: http://localhost/clinicManage/v1/createstudent
 * Parameters: name, phone, periodontal, systemic, cost
 * Method: POST
 * */
$app->post('/createPatient', function () use ($app) {
    verifyRequiredParams(array('name', 'phone', 'periodontal', 'systemic', 'cost'));
    $response = array();
    $name = $app->request->post('name');
    $phone = $app->request->post('phone');
    $periodontal = $app->request->post('periodontal');
    $systemic = $app->request->post('systemic');
    $cost = $app->request->post('cost');

    $db = new DbOperation();
    $res = $db->createPatient($name, $phone, $periodontal, $systemic, $cost);
    if ($res == 0) {

    	
        //Making the response error false
        $response["error"] = false;
        //Adding a success message
        $response["message"] = "You are successfully registered";
        //Displaying response
        echoResponse(201, $response);
 
    //If the result returned is 1 means failure
    } else if ($res == 1) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
        echoResponse(200, $response);
 
    //If the result returned is 2 means user already exist
    } else if ($res == 2) {

        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
        echoResponse(200, $response);
    }
});



/* *
 * URL: http://localhost/StudentApp/v1/createsession
 * Parameters: details, patient_id
 * Method: POST
 * */
$app->post('/createsession', function () use ($app) {
    verifyRequiredParams(array('session_date', 'details', 'cost', 'patient_id'));
     $session_date = $app->request->post('session_date');
     $details = $app->request->post('details');
    $cost = $app->request->post('cost');
    $patient_id = $app->request->post('patient_id');

   

    $db = new DbOperation();
    $response = array();

    if($db->createSession($session_date,$details,$cost, $patient_id)){
        $response['error'] = false;
        $response['message'] = "session created successfully";
    }else{
        $response['error'] = true;
        $response['message'] = "Could not create session";
    }

    echoResponse(200,$response);
    
});


$app->get('/patients', function() use ($app){
    $db = new DbOperation();
    $result = $db->getAllPatients();
    $response = array();
    $response['patients'] = array();

    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id']=$row['id'];
        $temp['name'] = $row['name'];
        $temp['phone'] = $row['phone'];
        $temp['periodontal'] = $row['periodontal'];
        $temp['systemic'] = $row['systemic'];
        $temp['cost'] = $row['cost'];
        array_push($response['patients'],$temp);
    }
    echoResponse(200,$response);
});


$app->get('/patients/:id', function($patient_id) use ($app){
    $db = new DbOperation();
    $result = $db->getPatient($patient_id);
    $response = array();
    $response['patient'] = array();

    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id']=$row['id'];
        $temp['name'] = $row['name'];
        $temp['phone'] = $row['phone'];
        $temp['periodontal'] = $row['periodontal'];
        $temp['systemic'] = $row['systemic'];
        $temp['cost'] = $row['cost'];
        array_push($response['patient'],$temp);
    }
    echoResponse(200,$response);
});

$app->get('/patients/s/:phone', function($phone) use ($app){
    $db = new DbOperation();
    $result = $db->searchPatient($phone);
    $response = array();
    $response['patient'] = array();

    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id']=$row['id'];
        $temp['name'] = $row['name'];
        $temp['phone'] = $row['phone'];
        $temp['periodontal'] = $row['periodontal'];
        $temp['systemic'] = $row['systemic'];
        $temp['cost'] = $row['cost'];
        array_push($response['patient'],$temp);
    }
    echoResponse(200,$response);
});

$app->get('/patient/:id', function($patient_id) use ($app){
        $db = new DbOperation();
        $result = $db->getSessions($patient_id);
        $response = array();
        $response['sessions'] = array();
        while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id']=$row['id'];
        $temp['session_date'] = $row['session_date'];
        $temp['details'] = $row['details'];
        $temp['cost'] = $row['cost'];
        $temp['patient_id'] = $row['patient_id'];
        array_push($response['sessions'],$temp);
    }
    echoResponse(200,$response);
});






function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

$app->run();