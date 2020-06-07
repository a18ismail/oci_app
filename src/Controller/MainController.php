<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    private $url;

    public function __construct()
    {
        $this->url = 'http://localhost:8000/api';
    }

    /**
     * @Route("/index", name="index")
     */
    public function index()
    {

        $requestURL = $this->url . '/getAll';

        $client = HttpClient::create();
        $response = $client->request('GET', $requestURL);

        $statusCode = $response->getStatusCode();

        if( $statusCode == 200 ){
            $content = $response->getContent();

            $data = json_decode($content);

            $receivedData = array();

            for ($i=0; $i<sizeof($data); $i++){
                $id = $data[$i]->id;
                $nom = $data[$i]->nom;
                $genere = $data[$i]->genere;
                $descripcio = $data[$i]->descripcio;

                $item = array(
                    'id' => $id,
                    'nom' => $nom,
                    'genere' => $genere,
                    'descripcio' => $descripcio
                );

                array_push($receivedData, $item);
            }

            return $this->render('main/index.html.twig', [
                'ofertes' => $receivedData,
                'url' => $this->url
            ]);

        }else{
            return $this->render('main/error.html.twig');
        }

    }

    /**
     * @Route("/afegir", name="afegir")
     */
    public function afegirOferta(Request $request)
    {

        $nom = $request->get('nom');
        $genere = $request->get('genere');
        $descripcio = $request->get('descripcio');

        $client = HttpClient::create();

        $response = $client->request('POST', $this->url.'/pelicula', [
            'body' => '{
                            "nom": "'.$nom.'",
                            "genere": "'.$genere.'",
                            "descripcio": "'.$descripcio.'"
                        }'
        ]);

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * @Route("/editar", name="editar")
     */
    public function editarOferta(Request $request)
    {
        $id = $request->get('inputEditarId');
        $nom = $request->get('inputEditarNom');
        $genere = $request->get('inputEditarGenere');
        $descripcio = $request->get('inputEditarDescripcio');

        $client = HttpClient::create();

        $response = $client->request('PUT', $this->url.'/pelicula/'.$id, [
            'body' => '{
                            "nom": "'.$nom.'",
                            "genere": "'.$genere.'",
                            "descripcio": "'.$descripcio.'"
                        }'
        ]);

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * @Route("/eliminar", name="eliminar")
     */
    public function eliminarOferta(Request $request)
    {
        $id = $request->get('inputEliminarId');

        $client = HttpClient::create();

        $response = $client->request('DELETE', $this->url.'/pelicula/'.$id);

        return $this->redirect($this->generateUrl('index'));
    }



}
