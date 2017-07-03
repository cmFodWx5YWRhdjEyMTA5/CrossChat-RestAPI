<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use PDO;
use PDOException;
use DB;
use JWTException;

class Registration extends Controller
{
    public function registerUser(Request $request){
    	// $this->validate($request, [
     //        'full_name' => 'required',
     //        'email_address' => 'required|email',
     //        'password'=> 'required|alphaNum|min:5',
     //        'current_address' => 'required',
     //        'country' => 'required',
     //        'city' => 'required',
     //        'username'=>'required'
     //        ]);

    	$full_name = $request->input('full_name');
    	$mobile_number = $request->input('mobile_number');
    	$email_address = $request->input('email_address');
    	$password = bcrypt($request->input('password'));
    	$current_address = $request->input('current_address');
    	$permanent_address = $request->input('permanent_address');
    	$field_of_interest = $request->input('field_of_interest');
    	$education_level = $request->input('education_level');
    	$specialization = $request->input('specialization');
    	$country = $request->input('country');
    	$city = $request->input('city');
    	$latitude = $request->input('latitude');
    	$longitude = $request->input('longitude');
    	$username=$request->input('username');
        $plainPassword=$request->input('password');
    	// $indication_flag = $request->input('indication_flag');

    	/*create PDO object for handling database query*/

        $pdoObect = DB::connection()->getPdo();
        try{
        $pdoObect->beginTransaction();
        $query="insert into users (full_name,mobile_number,email_address,password,current_address,permanent_address,field_of_interest,education_level,specialization,country,city,latitude,longitude,username,
        user_password) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $pdoObect->prepare($query);
        $stmt->bindParam(1,$full_name);
        $stmt->bindParam(2,$mobile_number);
        $stmt->bindParam(3,$email_address);
        $stmt->bindParam(4,$password);
        $stmt->bindParam(5,$current_address);
        $stmt->bindParam(6,$permanent_address);
        $stmt->bindParam(7,$field_of_interest);
        $stmt->bindParam(8,$education_level);
        $stmt->bindParam(9,$specialization);
        $stmt->bindParam(10,$country);
        $stmt->bindParam(11,$city);
        $stmt->bindParam(12,$latitude);
        $stmt->bindParam(13,$longitude);
        $stmt->bindParam(14,$username);
        $stmt->bindParam(15,$plainPassword);
        $result=$stmt->execute();
        if($result){
            $pdoObect->commit();
            return response()->json(["Success"=>true,"Message"=>"You are successfully registered","code"=>201]);
        }
        else{
            $stmt->rollback();
            return response()->json(400);
        }
    }
    catch(PDOException $e)
    {
    	$errorMessage=explode(" ",$e->getMessage());
    	if($errorMessage[10]=="'email_address'")
    		return response()->json(["Success"=>false,"Message"=>"Email is already registered","code"=>401]);
    	if($errorMessage[10]=="'username'");
    	return response()->json(["Success"=>false,"Message"=>"Username is already registered","code"=>402]);
    	if($errorMessage[10]=="'mobile_number'");
    	    return response()->json(["Success"=>false,"Message"=>"Mobile number is already registered","code"=>403]);

    }
}
}
