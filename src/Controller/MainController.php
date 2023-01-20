<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'AEV3_main')]
    public function index(): JsonResponse
    {
        
        // Json en formato string.
        $json = '{
            "tablas": [
                {
                    "name": "empresas",
                    "asociada": {
                        "1" : "pedidos"
                    }
                },
                {
                    "name": "facturas",
                    "asociada": {
                        "1" : "pedidos"
                    }
                },
                {
                    "name": "pedidos",
                    "asociada": {
                        "1" : "empresas",
                        "2" : "facturas"
                    }
                },
                {
                    "name": "lineaspedidos",
                    "asociada": {
                        "1" : "pedidos",
                        "2" : "productos"
                    }
                },
                {
                    "name": "productos",
                    "asociada": {
                        "1" : "lineaspedidos",
                        "2" : "almacenes",
                        "3" : "stock"
                    }
                },
                {
                    "name": "almacenes",
                    "asociada": {
                        "1" : "productos",
                        "2" : "stock"
                    }
                },
                {
                    "name": "stock",
                    "asociada": {
                        "1" : "productos",
                        "2" : "almacenes"  
                    }
                }
            ]
        }';
        // Transformar string json a array mediante json_decode
        // json_decode -> Takes a JSON encoded string and converts it into a PHP value.
        $data = json_decode($json, true);
        // Transformar array $data que contiene json codificado a objeto json.
        return $this->json($data);
    }
}
