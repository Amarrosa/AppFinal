<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Calendario;
use Input;
use Illuminate\Support\Facades\Redirect;


class CalendarController extends Controller
{

	public function index()
    {
        $data = array(); //declaramos un array principal que va contener los datos
        $id = Calendario::all()->lists('id'); //listamos todos los id de los eventos
        $titulo = Calendario::all()->lists('titulo'); //lo mismo para lugar y fecha
        $fechaIni = Calendario::all()->lists('fechaIni');
        $fechaFin = Calendario::all()->lists('fechaFin');
        $allDay = Calendario::all()->lists('todoeldia');
        $background = Calendario::all()->lists('color');
        $user_id = Calendario::all()->lists('user_id');
        
        $count = count($id); //contamos los ids obtenidos para saber el numero exacto de eventos
        
        //hacemos un ciclo para anidar los valores obtenidos a nuestro array principal $data 
        $j=0;
        for($i=0;$i<$count;$i++){
            if(Auth::user()->rol == 'administrativo'){
                $nombre = DB::table('users')->where('id',$user_id[$i])->value('name');
                $data[$i] = array(
                    "title"=>$titulo[$i], //obligatoriamente "title", "start" y "url" son campos requeridos
                    "start"=>$fechaIni[$i], //por el plugin asi que asignamos a cada uno el valor correspondiente
                    "end"=>$fechaFin[$i],
                    "allDay"=>$allDay[$i],
                    "backgroundColor"=>$background[$i],
                    "id"=>$id[$i],
                    "user"=>$nombre
                );
             }else{

                if($user_id[$i] == Auth::user()->id){
                   $nombre = DB::table('users')->where('id',Auth::user()->id)->value('name');
                    $data[$j] = array(
                        "title"=>$titulo[$i], //obligatoriamente "title", "start" y "url" son campos requeridos
                        "start"=>$fechaIni[$i], //por el plugin asi que asignamos a cada uno el valor correspondiente
                        "end"=>$fechaFin[$i],
                        "allDay"=>$allDay[$i],
                        "backgroundColor"=>$background[$i],
                        "id"=>$id[$i],
                        "user"=>$nombre
                    );   
                    $j++;

                }
            }
            
                //"url"=>"cargaEventos".$id[$i]
                //en el campo "url" concatenamos el el URL con el id del evento para luego
                //en el evento onclick de JS hacer referencia a este y usar el método show
                //para mostrar los datos completos de un evento
        }
   
        json_encode($data); //convertimos el array principal $data a un objeto Json 
       return $data; //para luego retornarlo y estar listo para consumirlo
    }


    public function create(){
       
        $evento = new Calendario;

        $evento->titulo = Input::get('titulo');
        $evento->fechaIni = Input::get('fechaIni');
        $evento->fechaFin = Input::get('fechaFin');
        $evento->user_id = Auth::user()->id;
        
        $evento->usuario_id = 
        $evento->save();
        return view('calendario');
    }

    public function update(){
        //Valores recibidos via ajax
        $id = $_POST['id'];
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $allDay = $_POST['allday'];
        $back = $_POST['background'];

        $evento=Calendario::find($id);
        if($end=='NULL'){
            $evento->fechaFin=NULL;
        }else{
            $evento->fechaFin=$end;
        }
        $evento->fechaIni=$start;
        $evento->todoeldia=$allDay;
        $evento->color=$back;
        $evento->titulo=$title;
        //$evento->fechaFin=$end;

        $evento->save();
   }

   public function delete(){
        //Valor id recibidos via ajax
        $id = $_POST['id'];

        Calendario::destroy($id);
   }

}
