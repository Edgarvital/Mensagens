<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Controllers\Goutte;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use DateTime;
include_once('simplehtmldom_1_9_1/simple_html_dom.php');

class ClippingController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function create(){
    	return view('clipping.create');
    }

    public function gerar(Request $request){
        //dd($request);
        $request->validate([
            'dataInicio'       => ['required', 'date_format:d/m/Y'],
            'dataFinal'        => ['required', 'date_format:d/m/Y'],
            'titulo.*'         => ['required_with:link.*'],
            'link.*'           => ['required_with:titulo.*'],
        ]);

        $dataI = str_replace ('/', '-', explode(" ",  $request->dataInicio)[0]);
        $dataF = str_replace ('/', '-', explode(" ",  $request->dataFinal)[0]);

        $dataInicio = $request->dataI . " 00:00:00";
        $dataFinal = $request->dataF . " 23:59:59";


        $noticias = $this->gerarNoticias($dataInicio, $dataFinal);
        $comunicados = $this->gerarComunicados($dataInicio, $dataFinal);
        $agenda = $this->gerarAgenda($dataInicio, $dataFinal);
        $editais = $this->gerarEditais($dataInicio, $dataFinal);

        // dd($noticias, $comunicados, $agenda, $editais);

        return view('clipping.exibir', compact('noticias', 'comunicados', 'agenda', 'editais'));
    }

    public function gerarEmail(){
        $dataFinal = date("d-m-Y") . " 00:00:00";
        $dataInicio = date("d-m-Y", strtotime("-1 week")) . " 23:59:59";


        $noticias = $this->gerarNoticias($dataInicio, $dataFinal);
        $comunicados = $this->gerarComunicados($dataInicio, $dataFinal);
        $agenda = $this->gerarAgenda($dataInicio, $dataFinal);
        $editais = $this->gerarEditais($dataInicio, $dataFinal);

        \Illuminate\Support\Facades\Mail::send(new \App\Mail\ClippingMail($noticias, $comunicados, $agenda, $editais));
    }
    
    public function gerarNoticias($dataInicio, $dataFinal){
        $dataInicio = new DateTime($dataInicio);
        $dataFinal = new DateTime($dataFinal);

        $noticias = [];

        $urlNoticias = 'http://ufape.edu.br/br/noticias';

        // $client = new Client();
        $client = new Client(HttpClient::create(['timeout' => 60]));
        
             
        try {
          $crawler = $client->request('GET', $urlNoticias); 
          
        } catch (\Throwable $e) {

          return $noticias[0] = ["erro"];
        }
        
       
        do {
            $noticiasData = $crawler->filter('div.view-content > div > div > span')->each(function ($node) {
                return $node->text();          
            });
            
            $noticiasTexto = $crawler->filter('div.view-content > div > div > h4 > a')->each(function ($node) {
                return $node->text();            
            });

            $noticiasLink = $crawler->filter('div.view-content > div > div > h4 > a')->each(function ($node) {
                return $node->attr('href');            
            });      
            foreach ($noticiasData as $key => $value) {
                $data = new DateTime(str_replace ('/', '-',  explode(" - ", $noticiasData[$key])[0]));
                 // dd($dataInicio);

                try {
                    $crawler1 = $client->request('GET', 'http://ufape.edu.br'.$noticiasLink[$key]);
                } catch (\Throwable $e) {

                    return $noticias[0] = ["erro"];
                }

                $noticia = $crawler1->filter('div.field-item')->each(function ($node) {
                    return $node->text();
                });
                $noticiaTexto = substr(reset($noticia), 0 , 250).'...';
                if($dataInicio <= $data && $dataFinal >= $data ){
                    array_push($noticias, [ $noticiasTexto[$key], $noticiasLink[$key], $noticiaTexto]);
                }                  
            }
            
            $link = $crawler->selectLink('próximo ›')->link();
            $crawler = $client->click($link); 
            $data = new DateTime(str_replace ('/', '-', explode(" - ", end($noticiasData))[0]  ));      
        } while ($dataInicio <= $data && $dataFinal >= $data);      

        return $noticias;
    }

    public function gerarComunicados($dataInicio, $dataFinal){
        $dataInicio = new DateTime($dataInicio);
        $dataFinal = new DateTime($dataFinal);

        $comunicados = [];

        $urlComunicados = 'http://ufape.edu.br/br/comunicados';


        $client = new Client(HttpClient::create(['timeout' => 60]));
        
              
        try {
          $crawler = $client->request('GET', $urlComunicados); 
          
        } catch (\Throwable $e) {
          return $comunicados[0] = ["erro"];
        }

        do {
            $comunicadosData = $crawler->filter('div.view-content > div > div > span')->each(function ($node) {
                return $node->text();
                 // return $node->attr('href');            
            });
            
            $comunicadosTexto = $crawler->filter('div.view-content > div > div > h4 > a')->each(function ($node) {
                return $node->text();
                 // return $node->attr('href');            
            });

            $comunicadosLink = $crawler->filter('div.view-content > div > div > h4 > a')->each(function ($node) {
                return $node->attr('href');
                 // return $node->attr('href');            
            });      
            foreach ($comunicadosTexto as $key => $value) {
                $data = new DateTime(str_replace ('/', '-',  explode(" - ", $comunicadosData[$key])[0]));
                // dd($data);
                try {
                    $crawler1 = $client->request('GET', 'http://ufape.edu.br'.$comunicadosLink[$key]);
                } catch (\Throwable $e) {

                    return $comunicados[0] = ["erro"];
                }

                $comunicado = $crawler1->filter('div.field-item')->each(function ($node) {
                    return $node->text();
                });
                $comunicadoTexto = substr(reset($comunicado), 0 , 250).'...';

                if($dataInicio <= $data && $dataFinal >= $data ){
                    array_push($comunicados, [ $comunicadosTexto[$key], $comunicadosLink[$key], $comunicadoTexto ]);
                }                  
            }
            
            $link = $crawler->selectLink('próximo ›')->link();
            $crawler = $client->click($link); 
            $data = new DateTime(str_replace ('/', '-', explode(" - ", end($comunicadosData))[0]  ));      
        } while ($dataInicio <= $data && $dataFinal >= $data);

        
        return $comunicados;
    }

    public function gerarAgenda($dataInicio, $dataFinal){
        $dataInicio = new DateTime($dataInicio);
        $dataFinal = new DateTime($dataFinal);

        $agenda = [];
        $urlAgenda = 'http://ufape.edu.br/br/eventos';

        $client = new Client(HttpClient::create(['timeout' => 60]));
            
        try {
          $crawler = $client->request('GET', $urlAgenda);  
          
        } catch (\Throwable $e) {
          return $agenda[0] = ["erro"];
        }
        

        do {
            $agendaData = $crawler->filter('div.item-list > ul > li > div[class="views-field views-field-field-data"]')->each(function ($node) {
                return $node->text();         
            });
            $agendaTexto = $crawler->filter('div.item-list > ul > li > div[class="views-field views-field-title"] > span > a')->each(function ($node) {
                return $node->text();           
            });

            $agendaLink = $crawler->filter('div.item-list > ul > li > div[class="views-field views-field-title"] > span > a')->each(function ($node) {
                return $node->attr('href');           
            });      
            foreach ($agendaTexto as $key => $value) {
                $data = new DateTime(str_replace ('/', '-', $agendaData[$key]));

                try {
                    $crawler1 = $client->request('GET', 'http://ufape.edu.br'.$agendaLink[$key]);
                } catch (\Throwable $e) {

                    return $agenda[0] = ["erro"];
                }

                $agend = $crawler1->filter('div.field-item')->each(function ($node) {
                    return $node->text();
                });
                $agendaTexto = substr(reset($agend), 0 , 250).'...';

                if($dataInicio <= $data && $dataFinal >= $data ){
                    array_push($agenda, [ $agendaData[$key], $value, $agendaLink[$key], $agendaTexto]);
                }                  
            }
            
            $link = $crawler->selectLink('próximo ›')->link();
            $crawler = $client->click($link); 
            $data = new DateTime(str_replace ('/', '-', end($agendaData) ));      
        } while ($dataInicio <= $data && $dataFinal >= $data);
                
        
        return $agenda;
    }

    public function gerarEditais($dataInicio, $dataFinal){
        $dataInicio = new DateTime($dataInicio);
        $dataFinal = new DateTime($dataFinal);

        $urlEdital = 'http://ufape.edu.br/br/editais-e-selecoes';
        $editais = [];

        $client = new Client(HttpClient::create(['timeout' => 60]));
        
        try {
          $crawler = $client->request('GET', $urlEdital);   
          
        } catch (\Throwable $e) {
          return $editais[0] = ["erro"];
        }

        do {
            $editalData = $crawler->filter('div.view-content > div.views-row > div[class="views-field views-field-created"]')->each(function ($node) {
                return $node->text();                       
            });
            //dd();
            $editalTitulo = $crawler->filter('div.view-content > div.views-row > div > strong')->each(function ($node) {
                return $node->text();           
            });

            $editalLink = $crawler->filter('div.view-content > div.views-row > div > strong > a')->each(function ($node) {
                return $node->attr('href');          
            });        
        
            foreach ($editalTitulo as $key => $value) {
                $data = new DateTime(str_replace ('/', '-', explode(" ",  $editalData[$key])[0]));

                if($dataInicio <= $data && $dataFinal >= $data ){
                    array_push($editais, [$value, $editalLink[$key]]);                  
                }             
            }
            $link = $crawler->selectLink('próximo ›')->link();
            $crawler = $client->click($link); 
            $data = new DateTime(str_replace ('/', '-', explode(" ",  end($editalData))[0]));      
            // print '1';
        } while ($dataInicio <= $data && $dataFinal >= $data);

        return $editais;
    }

    
}