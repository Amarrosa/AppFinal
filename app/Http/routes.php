<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\User;
use Illuminate\Http\Request;

Route::group(['middleware' => 'auth'], function () {
  Route::get('/', ['as' => 'inicio', 'uses' => 'HomeController@index']);
  Route::post('guardaTareas', ['as' => 'guardaTareas', 'uses' => 'HomeController@store']);
  Route::delete('borraTareas/{id}', ['as' => 'borraTareas', 'uses' => 'HomeController@destroy']);
  Route::resource('usuarios', 'UsuarioController');
  Route::resource('contactos', 'ContactoController');
  Route::resource('socios', 'SocioController');
 	Route::post('guardaEventos', array('as' => 'guardaEventos','uses' => 'CalendarController@create'));
 	Route::any('cargaEventos',array('as'=>'cargaEventos','uses'=>'CalendarController@cargadorEventos'));
 	Route::post('actualizaEventos','CalendarController@update');
 	Route::post('eliminarEvento','CalendarController@delete');
  Route::get('calendario','CalendarController@index');

  Route::get('actualizacion/{id}', ['as' => 'actuLopd', 'uses' => 'PdfController@actualizacionLopd']);

  Route::any('getdata', function(){
   $term = Input::get('term');
   
   // 4: check if any matches found in the database table 
    $data = DB::table('socios')->where('nombre','LIKE',$term.'%')
          ->orWhere('apellido1','LIKE', $term.'%')
          ->orWhere('apellido2','LIKE', $term.'%')
          ->groupBy('nombre','apellido1','apellido2')->take(10)->get();
    foreach ($data as $v) {
      $return_array[] = array('value' =>$v->id.' '.$v->nombre.' '.$v->apellido1.' '.$v->apellido2);
    }
    // if matches found it first create the array of the result and then convert it to json format so that 
    // it can be processed in the autocomplete script
    return Response::json($return_array);
  });
  Route::any('getUsuario', function(){
   $term = Input::get('term');
   
   // 4: check if any matches found in the database table 
    $data = DB::table('usuarios')->where('nombre','LIKE',$term.'%')
          ->orWhere('apellido1','LIKE', $term.'%')
          ->orWhere('apellido2','LIKE', $term.'%')
          ->groupBy('nombre','apellido1','apellido2')->take(10)->get();
    foreach ($data as $v) {
      $return_array[] = array('value' =>$v->id.' '.$v->nombre.' '.$v->apellido1.' '.$v->apellido2);
    }
    // if matches found it first create the array of the result and then convert it to json format so that 
    // it can be processed in the autocomplete script
    return Response::json($return_array);
  });

      Route::get("test-email", function() {
        Mail::send("emails.bienvenido", [], function($message) {
            $message->to("tehenua@gmail.com", "Usue")
            ->subject("Probando el correo!");
        });
    });

});

