<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use JWTAuth;
use DB;
use PDOException;
use PDO;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        
        $credential = $request->only('email_address','password');

        try {
            if (! $token = JWTAuth::attempt($credential))
            {
                return response()->json(['message' => 'Invalid Credential',"response_status"=>"Failed","code"=>401]);
            }

        } catch(JWTException $e)
        {
                return response()->json(['message' => 'Could not create token',"response_status"=>"SError","code"=>500],500);
        }
        $user=JWTAuth::authenticate($token);
        return response()->json(["response_status"=>"Success","username"=>$user->username,'token' => $token,"permanent_address"=>$user->permanent_address,"specialization"=>$user->specialization,
            "city"=>$user->city,"country"=>$user->country,"latitude"=>$user->latitude,"longitude"=>$user->longitude,"email_address"=>$user->email_address,"mobile_number"=>$user->mobile_number,
            "education_level"=>$user->education_level,"field_of_interest"=>$user->field_of_interest,"full_name"=>$user->full_name,"code"=>201]);
    }

    public function forgotPassword(Request $request){
        $email_address=$request->email_address;
        $security_question=$request->security_question;
        $answer=$request->answer;
        if(isset($email_address)&&(!isset($security_question))&&(!isset($answer))){
            try{
                $pdoObect = DB::connection()->getPdo();
                $query="select email_address ,security_question from users where email_address=?";
                $stmt = $pdoObect->prepare($query);
                $stmt->bindParam(1,$email_address);
                $stmt->execute();
                $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($res as $middle) {
                    $email=$middle['email_address'];
                    $question=$middle['security_question'];
                }
                if(isset($email)){
                    return response()->json(["message"=>$email,"code"=>222,"security_question"=>$question]);
                }
                else{
                    return response()->json(["message"=>"invalidEmail","code"=>401]);
                }
                
            }
            catch(PDOException $e){
                return response()->json(["message"=>"Error occured in our database.","code"=>500]);
            }
            finally{
                $pdoObect=null;
            }
        }
        if(isset($email_address)&&isset($security_question)&&isset($answer)){
            try{
            $pdoObect = DB::connection()->getPdo();
            $query="select security_question,security_answer,user_password from users where email_address=?";
            $stmt = $pdoObect->prepare($query);
            $stmt->bindParam(1,$email_address);
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
           // return response()->json($result);
            foreach ($result as $user)
            {
               if($security_question==$user['security_question'] && $answer==$user['security_answer']){
                    return response()->json(['message'=> $user['user_password'],"code"=>222]);
               }
               else{
                   return response()->json(['message'=>"Invalid answer","code"=>444]);
               }
            }
        }
        catch(PDOException $e){
            return response()->json(["message"=>"PDOException","code"=>501]);
        }
            
        }
    }
}
