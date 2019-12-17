<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Profesional; 
use App\TipoProfesional;
use App\Opinion;
use App\Helpers\JwtAuth;

class OpinionCotroller extends Controller
{

    public function insertOpinion(Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        
        if($checkToken){
            //Recoger POST
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);

            //Verificacion de datos
            $nombre=(!is_null($json) && isset($params->nombre)) ? $params->nombre : null;
            $email=(!is_null($json) && isset($params->email)) ? $params->email : null;
            $telefono=(!is_null($json) && isset($params->telefono)) ? $params->telefono : null;
            $descripcion=(!is_null($json) && isset($params->descripcion)) ? $params->descripcion : null;
            $aprobado=(!is_null($json) && isset($params->aprobado)) ? $params->aprobado : null;
            $profesional=(!is_null($json) && isset($params->profesional)) ? $params->profesional : null;

            //chequeamos que tengamos datos por POST
            if(!is_null($params_array)){
                $validar= \Validator::make($params_array,[
                    'nombre' => 'required',
                    'email'=> 'required|email',
                    'telefono' => 'required',
                    'descripcion'=>'required',
                    'aprobado'=>'required',
                    'profesional'=>'required'
                 ]);

                 //si hay errores envio el error
                 if($validar->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'errores' => $this->errores($validar->errors()),
                        'messages' => 'Campos no validos',
                     );
      
                     return response()->json($data,200);  
                 }
                 // verificar que existe el profesional
                 $profesional = Profesional::find($profesional);
                 if(is_null($tipo_profesional)){
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'errores' => ['El profesional no existe'],
                        'messages' => 'No existe el profesional ingresado',
                     );
                     return response()->json($data,200);  
                 }

                 //crear modelo
                 $opinion = new Opinion;
                 $opinion->nombre=$nombre;
                 $opinion->email=$email;
                 $opinion->telefono=$telefono;
                 $opinion->descripcio=$descripcion;
                 $opinion->aprobado=$aprobado;
                 $opinion->id_profesional=$profesional;

                 $opinion->save();
                 $opinion->load('profesional');
                 $data = array(
                    'status' => 'success',
                    'opinion' => $opinion,
                    'code' => 200,
                    'message' => 'Se agregó la nueva opinion'
                 );
                 return response()->json($data,200);  

            }else{
                //No hay datos por POST
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'messages' => 'No hay datos por POST',
                 );
                 return response()->json($data,200); 
            }

        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'messages' => 'Fallo autentificacion',
             );
             return response()->json($data,200);         
        }
    }

    public function getOpinionByProfesional($profesional_id){
        $opinion= Opinion::where([['id_profesional', '=', $profesional_id]])->get();
        
        if(!is_null($opinion)){
            $opinion->load('profesional');
            return response()->json(array(
               'opinion'=>$opinion,
               'status'=>'success'
            ),200);
   
         }else{
            //El profesional con ese id no existe
            return response()->json(array(
                'message' => 'El profesional no existe.',
                'status' => 'error'
            ), 200);
         }
    }
    
    public function updateOpinion($id, Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
        
        if($checkToken){
            //Recoger POST
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);

            //Verificacion de datos
            $nombre=(!is_null($json) && isset($params->nombre)) ? $params->nombre : null;
            $email=(!is_null($json) && isset($params->email)) ? $params->email : null;
            $telefono=(!is_null($json) && isset($params->telefono)) ? $params->telefono : null;
            $descripcion=(!is_null($json) && isset($params->descripcion)) ? $params->descripcion : null;
            $aprobado=(!is_null($json) && isset($params->aprobado)) ? $params->aprobado : null;
            $profesional=(!is_null($json) && isset($params->profesional)) ? $params->profesional : null;

            //chequeamos que tengamos datos por POST
            if(!is_null($params_array)){
                $validar= \Validator::make($params_array,[
                    'nombre' => 'required',
                    'email'=> 'required|email',
                    'telefono' => 'required',
                    'descripcion'=>'required',
                    'aprobado'=>'required',
                    'profesional'=>'required'
                 ]);

                 //si hay errores envio el error
                 if($validar->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'errores' => $this->errores($validar->errors()),
                        'messages' => 'Campos no validos',
                     );
      
                     return response()->json($data,200);  
                 }
                 // verificar que existe el profesional
                 $profesional = Profesional::find($profesional);
                 if(is_null($tipo_profesional)){
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'errores' => ['La opinion no existe'],
                        'messages' => 'No existe la opinion ingresada',
                     );
                     return response()->json($data,200);  
                 }

                 //Busco la opinion 
                 $opinion =Opinion::find($id);
                 if(!isnull($$opinion)){

                    $opinion->nombre=$nombre;
                    $opinion->email=$email;
                    $opinion->telefono=$telefono;
                    $opinion->descripcio=$descripcion;
                    $opinion->aprobado=$aprobado;
                    $opinion->id_profesional=$profesional;

                    $opinion->save();
                    $opinion->load('profesional');
                    $data = array(
                    'status' => 'success',
                    'opinion' => $opinion,
                    'code' => 200,
                    'message' => 'Se agregó la nueva opinion'
                    );
                    return response()->json($data,200); 
                }else{
                    $data = array(
                        'status' => 'error',
                        'errores' => ['No existe el profesional'],
                        'message' => 'Error al subir el profesional'
                    );
                    return response()->json($data,200);     

                }

            }else{
                //No hay datos por POST
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'messages' => 'No hay datos por POST',
                 );
                 return response()->json($data,200); 
            }

        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'messages' => 'Fallo autentificacion',
             );
             return response()->json($data,200);         
        }
    }

    public function deleteOpinion($id, Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

      
        //verificar si es un token valido
        if($checkToken){
         //buscar profesional
         $opinion = Opinion::find($id);
         //si existe el tipo
         if(!is_null($opinion)){
             //cargamos profesionales
             $opinion = $opinion->load('profesional');
             // guardar tipo
             $opinion->delete();
            // mensaje de retorno
            $data = array(
                'status' => 'success',
                'profesional' => $profesional,
                'code' => 200,
                'message' => 'Se eliminó la opinion'
            );
                             
         }else{
             // mensaje de retorno
             $data = array(
                 'status' => 'error',
                 'errores' => ['No existe la opinion'],
                 'message' => 'Error la opinion'
             );
         }
     }else{
         $data = array(
           'status' => 'error',
           'errores' => ['No inició sesión'],
           'message' => 'Error al eliminar la opinion'
         );
     }
       
     return $data;
    }
}
