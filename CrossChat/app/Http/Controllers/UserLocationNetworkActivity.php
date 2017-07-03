<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use PDO;
use DB;
use JWTAuth;
use JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
class UserLocationNetworkActivity extends Controller
{
    public function __contruct(){
        $this->middleware('jwt.auth',['only'=>['updateCurrentLocation','searchInterestedField']]);
    }
     function getNearbyPlaces(Request $request){
        //$this->validate($request,['token'=>'required','latitude'=>'required','longitude'=>'required']);    
        //All units of the angle here is in radian so we must provide input as radian
        
    	$earthRadius=6371;
         //arccos(sin(lat1) · sin(lat2) + cos(lat1) · cos(lat2) · cos(lon1 - lon2)) · R
    	
        $userCredential=$request->input('token');
    	$lattitude=$request->input('latitude');
    	$longitude=$request->input('longitude');
    	
        $radius=$request->input('radius');
    	$angularRadius=$radius/6371;
    	
        $maxLat=$lattitude+$angularRadius;
    	$minLat=$lattitude-$angularRadius;
        
        $tangentLattitude=asin(sin($lattitude)/cos($lattitude));
    	
        $maxLon=$longitude+$tangentLattitude;
        $minLon=$longitude-$tangentLattitude;
        //return response()->json(['maxLat'=>$maxLat,'minLat'=>$minLat,'minLon'=>$minLon,'maxLon'=>$maxLon]);
        
        $connection=DB::connection()->getPdo();
        $rs=$connection->prepare("select * from users where (latitude<=? and latitude>=?) and (longitude<=? and longitude>=?) and "
                . "(acos((sin(?)*sin(latitude))+(cos(?)*cos(latitude)*cos(?-longitude)))*?)<=?");
        $rs->bindParam(1,$maxLat);
        $rs->bindParam(2,$minLat);
        $rs->bindParam(3,$maxLon);
        $rs->bindParam(4,$minLon);
        $rs->bindParam(5,$lattitude);
        $rs->bindParam(6,$lattitude);
        $rs->bindParam(7,$longitude);
        $rs->bindParam(8,$earthRadius);
        $rs->bindParam(9,$radius);
        $rs->execute();
        $result=$rs->fetchAll(PDO::FETCH_ASSOC);
        return response()->json(["response_code"=>201,"response_status"=>"success","data"=>$result]);
        //return response()->json("Hello");
    }
    
//    public function getPdoObject(){
//    	$object=DB::connection()->getPdo();
//        return $object;
//    }
    function simpleTesting(Request $request){
        $conn=DB::connection()->getPdo();
        $res=$conn->prepare("select * from users");
        $res->execute();
        $result=$res->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($result);
    }
    function testingMethod(Request $request){
        $user=$request->input('user');
        return $user;
    }
    function updateCurrentLocation(Request $request){
        
        try{
            if(!$user=JWTAuth::parseToken()->authenticate()){
                 return response()->json(["message"=>"User is not authenticated","code"=>402]);
             }
        $latitude=$request->input("latitude");
        $longitude=$request->input("longitude");
        $id=$user->id;
        $query="update users set latitude = ?,longitude = ? where id = ?";
        $connection=DB::connection()->getPdo();
            $connection->beginTransaction();
            $result=$connection->prepare($query);
            $result->bindParam(1,$latitude);
            $result->bindParam(2,$longitude);
            $result->bindParam(3,$id);
            $res=$result->execute();
            if($res){
                $connection->commit();
                return response()->json(["message"=>"success","code"=>201]);
            }
            else{
                $connection->rollback();
                return response()->json(["message"=>"failed","code"=>401]);
            }
            
        }
        catch(PDOException $e){
            $connection->rollback();
            return response()->json(["message"=>$e->getMessage(),"code"=>500]);
        }
        catch(TokenExpiredException $e){
            return response()->json(["message"=>"TokenExpiredException","code"=>502]);
        }
        finally{
            $connection=null;
        }
    }


 public function interestedFieldSearch(Request $request){
       try{
       // if(!$user=JWTAuth::parseToken()->authenticate()){
       //    return response()->json(["message"=>"User is not authenticated","code"=>402]);
       // }
       //$userCredential=$request->input('token');
        $latitude=$request->input('latitude');
        $longitude=$request->input('longitude');
        
        $radius=$request->input('radius');
        $earthRadius=6371;
        $angularRadius=$radius/6371;
        
        $maxLat=$latitude+$angularRadius;
        $minLat=$latitude-$angularRadius;
        
        $tangentLattitude=asin(sin($latitude)/cos($latitude));
        
        $maxLon=$longitude+$tangentLattitude;
        $minLon=$longitude-$tangentLattitude;
        //return response()->json(['maxLat'=>$maxLat,'minLat'=>$minLat,'minLon'=>$minLon,'maxLon'=>$maxLon]);

        $searchString=$request->input("query_string");
         $query1="select username, full_name, mobile_number, email_address, permanent_address, field_of_interest, education_level, specialization, country, city, latitude, longitude,current_address from users ";
$query2="where field_of_interest like  '".$searchString."' and ((latitude<=".$maxLat." and latitude>=".$minLat.") and (longitude<=".$maxLon." and longitude>=".$minLon.") and "
                . "(acos((sin(".$latitude.")*sin(latitude))+(cos(".$latitude.")*cos(latitude)*cos(".$longitude."-longitude)))*".$earthRadius.")<=".$radius.")";
$query=$query1.$query2;
         $con=DB::connection()->getPdo();
             $result=$con->prepare($query);
           // $result->bindParam(1,$searchString);
            // $result->bindParam(1,$maxLat);
            // $result->bindParam(2,$minLat);
            // $result->bindParam(3,$maxLon);
            // $result->bindParam(4,$minLon);
            // $result->bindParam(5,$latitude);
            // $result->bindParam(6,$latitude);
            // $result->bindParam(7,$longitude);
            // $result->bindParam(8,$earthRadius);
            // $result->bindParam(9,$radius);
            $result->execute();
            $res=$result->fetchAll(PDO::FETCH_ASSOC);
            return response()->json(["response_code"=>201,"response_status"=>"success","data"=>$res]);
        }
        catch(PDOException $e){
            return response()->json(["message"=>"TokenExpiredException","code"=>501]);
            }
        catch(TokenExpiredException $e){
            return response()->json(["message"=>"TokenExpiredException","code"=>502]);
        }
        finally{
            $conn=null;
        }
    }
}
