<?php

class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

	public function createPatient($name,$phone,$periodontal,$systemic,$cost){


		if(!$this ->isPatientExists($phone)){
			$stmt = $this->con->prepare("INSERT INTO patients (name, phone, periodontal, systemic, cost) VALUES (?, ?, ?, ?, ?);");
			$stmt->bind_param("sssss", $name, $phone, $periodontal, $systemic, $cost);
	         $result = $stmt->execute();
	         $stmt->close();
	            
            if ($result) {
                //Returning 0 means student created successfully
                return 0;
            } else {
                //Returning 1 means failed to create student
                return 1;
            }
        } else {
            //returning 2 means user already exist in the database
            return 2;
        }
    }
 public function createSession($session_date,$details,$cost, $patient_id){
        $stmt = $this->con->prepare("INSERT INTO sessions (session_date, details, cost, patient_id) VALUES (?, ?, ?, ?);");
            $stmt->bind_param("sssi", $session_date, $details, $cost, $patient_id);
             $result = $stmt->execute();
             $stmt->close();
             if($result){
                            return true;
                        }
                        return false;

    }

    public function getAllPatients(){
        $stmt = $this->con->prepare("SELECT * FROM patients");
        $stmt->execute();
        $patients = $stmt->get_result();
        $stmt->close();
        return $patients;
    }



    public function searchPatient($phone){
        $stmt = $this->con->prepare("SELECT * FROM patients WHERE phone=?");
        $stmt->bind_param("s",$phone);
        $stmt->execute();
        //Getting the student result array
        $student = $stmt->get_result();
        $stmt->close();
        //returning the student
        return $student;
    }

    public function getPatient($id){
        $stmt = $this->con->prepare("SELECT * FROM patients WHERE id=?");
        $stmt->bind_param("s",$id);
        $stmt->execute();
        //Getting the student result array
        $student = $stmt->get_result();
        $stmt->close();
        //returning the st udent
        return $student;
    }

 
   
     public function getSessions($patient_id){
        $stmt = $this->con->prepare("SELECT * FROM sessions WHERE patient_id=?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $sessions = $stmt->get_result();
        $stmt->close();
        return $sessions;
    }
    //Checking whether a student already exist
    private function isPatientExists($phone) {
        $stmt = $this->con->prepare("SELECT id from patients WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
}