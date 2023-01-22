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
                    },
                    "rutas":{
                        "show_all":"empresas/",
                        "show_one":"empresas/{id}",
                        "new":"empresas/new",
                        "edit":"empresas/edit/{id}",
                        "delete":"empresas/delete/{id}"
                    }
                },
                {
                    "name": "facturas",
                    "asociada": {
                        "1" : "pedidos"
                    },
                    "rutas":{
                        "show_all":"facturas/",
                        "show_one":"facturas/{id}",
                        "new":"facturas/new",
                        "edit":"facturas/edit/{id}",
                        "delete":"facturas/delete/{id}"
                    }
                },
                {
                    "name": "pedidos",
                    "asociada": {
                        "1" : "empresas",
                        "2" : "facturas"
                    },
                    "rutas":{
                        "show_all":"pedidos/",
                        "show_one":"pedidos/{id}",
                        "new":"pedidos/new",
                        "edit":"pedidos/edit/{id}",
                        "delete":"pedidos/delete/{id}"
                    }
                },
                {
                    "name": "lineaspedidos",
                    "asociada": {
                        "1" : "pedidos",
                        "2" : "productos"
                    },
                    "rutas":{
                        "show_all":"lineaspedidos/",
                        "show_one":"lineaspedidos/{id}",
                        "new":"lineaspedidos/new",
                        "edit":"lineaspedidos/edit/{id}",
                        "delete":"lineaspedidos/delete/{id}"
                    }
                },
                {
                    "name": "productos",
                    "asociada": {
                        "1" : "lineaspedidos",
                        "2" : "almacenes",
                        "3" : "stock"
                    },
                    "rutas":{
                        "show_all":"productos/",
                        "show_one":"productos/{id}",
                        "new":"productos/new",
                        "edit":"productos/edit/{id}",
                        "delete":"productos/delete/{id}"
                    }
                },
                {
                    "name": "almacenes",
                    "asociada": {
                        "1" : "productos",
                        "2" : "stock"
                    },
                    "rutas":{
                        "show_all":"almacenes/",
                        "show_one":"almacenes/{id}",
                        "new":"almacenes/new",
                        "edit":"almacenes/edit/{id}",
                        "delete":"almacenes/delete/{id}"
                    }
                },
                {
                    "name": "stock",
                    "asociada": {
                        "1" : "productos",
                        "2" : "almacenes"  
                    },
                    "rutas":{
                        "show_all":"stock/",
                        "show_one":"stock/{id}",
                        "new":"stock/new",
                        "edit":"stock/edit/{id}",
                        "delete":"stock/delete/{id}"
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
