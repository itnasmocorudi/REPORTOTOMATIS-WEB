<?php 

ini_set('memory_limit', '512M'); 
class UserClient{

    public $instance = NULL;

    public function __construct()
    {
        ini_set('default_socket_timeout', 5000);
        $params = array(
            'location'=> HTTP_API_SERVER.'soap_login.php',
            'uri' =>  URN_API_SERVER.'soap_login.php'  ,
            'trace'=>1,'cache_wsdl'=>WSDL_CACHE_NONE    );
        $this->instance =  new SoapClient(NULL, $params);       
    }
 
    public function LoginUser($id_array){
        return $this->instance->__soapCall('GetLogin', $id_array);
    } 

    public function GetUserx($id_array){
        return $this->instance->__soapCall('GetUserx', $id_array);
    } 
	 
    public function AddUser($id_array){
        return $this->instance->__soapCall('AddUser', $id_array);
    } 
	
    public function GetUserByID($id_array){
        return $this->instance->__soapCall('GetUserByID', $id_array);
    } 
	 
    public function EditUser($id_array){
        return $this->instance->__soapCall('EditUser', $id_array);
    } 
	 
    public function DeleteUser($id_array){
        return $this->instance->__soapCall('DeleteUser', $id_array);
    } 

    public function GetEmailx($id_array){
        return $this->instance->__soapCall('GetEmailx', $id_array);
    } 
	 
    public function AddEmail($id_array){
        return $this->instance->__soapCall('AddEmail', $id_array);
    } 
	
    public function GetEmailByID($id_array){
        return $this->instance->__soapCall('GetEmailByID', $id_array);
    } 
	 
    public function EditEmail($id_array){
        return $this->instance->__soapCall('EditEmail', $id_array);
    } 
	 
    public function DeleteEmail($id_array){
        return $this->instance->__soapCall('DeleteEmail', $id_array);
    } 

    public function GetPeriodex($id_array){
        return $this->instance->__soapCall('GetPeriodex', $id_array);
    } 

    public function GetDataTipeReport($id_array){
        return $this->instance->__soapCall('GetDataTipeReport',$id_array);
    }
	 
    public function AddPeriode($id_array){
        return $this->instance->__soapCall('AddPeriode', $id_array);
    } 
	 
    public function GetPeriodeByID($id_array){
        return $this->instance->__soapCall('GetPeriodeByID', $id_array);
    } 
	 
    public function EditPeriode($id_array){
        return $this->instance->__soapCall('EditPeriode', $id_array);
    } 
	 
    public function DeletePeriode($id_array){
        return $this->instance->__soapCall('DeletePeriode', $id_array);
    } 
/**/
    public function GetManualx($id_array){
        return $this->instance->__soapCall('GetManualx', $id_array);
    } 

    public function GetDataDealer($id_array){
        return $this->instance->__soapCall('GetDataDealer', $id_array);
    } 

    public function AddManual($id_array){
        return $this->instance->__soapCall('AddManual', $id_array);
    } 

    public function GetLogx($id_array){
        return $this->instance->__soapCall('GetLogx', $id_array);
    } 
    
    
}
 
?>