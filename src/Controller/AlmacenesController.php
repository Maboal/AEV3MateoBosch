<?php

namespace App\Controller;

use App\Entity\Almacenes;
use App\Repository\AlmacenesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/almacenes', name: 'AEV3_almacenes')]
class AlmacenesController extends AbstractController
{

    private AlmacenesRepository $almacenesRepository;

    public function __construct(AlmacenesRepository $almacenesRepo)
    {
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'AEV3_almacenes_show_all', methods: ['GET', 'POST'])]
    public function showAll(): JsonResponse
    {
        $almacenes = $this->almacenesRepository->findAll();
        $data = [];
        $dataProductos = [];
        $dataStocks = [];

        foreach ($almacenes as $almacen) {

            foreach ($almacen->getStocks() as $stock) {
                $dataStock = ['stock' => [
                    'stock_id' => $stock->getId(),
                    'stock_cantidad' => $stock->getCantidad(),
                    'stock_precio' => $stock->getPrecio(),
                    'stock_unidad' => $stock->getUnidad()
                ]];
                array_push($dataStocks, $dataStock);
            }

            foreach ($almacen->getProductos() as $productos) {
                $dataProducto = ['producto' => [
                    'producto_id' => $productos->getId(),
                    'producto_descripcion' => $productos->getDescripcion(),
                    'producto_unidad' => $productos->getUnidad(),
                    'producto_preciounidad' => $productos->getPreciounidad()
                ]];
                array_push($dataProductos, $dataProducto);
            }



            $data[] = [
                'nombre' => $almacen->getNombre(),
                'localizacion' => $almacen->getLocalizacion(),
                'descripcion' => $almacen->getDescripcion()
            ];
            array_push($data, $dataStocks);
            array_push($data, $dataProductos);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'AEV3_almacenes_show', methods: ['GET'])]
    public function show(Almacenes $almacen): JsonResponse
    {
        $data = [];
        $dataProductos = [];
        $dataStocks = [];

        foreach ($almacen->getStocks() as $stock) {
            $dataStock = ['stock' => [
                'stock_id' => $stock->getId(),
                'stock_cantidad' => $stock->getCantidad(),
                'stock_precio' => $stock->getPrecio(),
                'stock_unidad' => $stock->getUnidad()
            ]];
            array_push($dataStocks, $dataStock);
        }

        foreach ($almacen->getProductos() as $productos) {
            $dataProducto = ['producto' => [
                'producto_id' => $productos->getId(),
                'producto_descripcion' => $productos->getDescripcion(),
                'producto_unidad' => $productos->getUnidad(),
                'producto_preciounidad' => $productos->getPreciounidad()
            ]];
            array_push($dataProductos, $dataProducto);
        }



        $data[] = [
            'nombre' => $almacen->getNombre(),
            'localizacion' => $almacen->getLocalizacion(),
            'descripcion' => $almacen->getDescripcion()
        ];
        array_push($data, $dataStocks);
        array_push($data, $dataProductos);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name: 'AEV_almacenes_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());

        $almacen = new Almacenes();
        $almacen->setId($data->id)
            ->setNombre($data->nombre)
            ->setLocalizacion($data->localizacion)
            ->setDescripcion($data->descripcion);

        $this->almacenesRepository->save($almacen, true);

        return new JsonResponse(['status' => 'Almacen creado'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_almacenes_edit', methods: ['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $almacen = $this->almacenesRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());
        if ($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Almacén actualizado completamente de forma satisfactoria';
        else
            $mensaje = 'Almacén actualizado parcialemente de forma satisfactoria';
        //Utilizamos el operador ternario
        empty($data->id) ? true : $almacen->setId($data->id);
        empty($data->nombre) ? true : $almacen->setNombre($data->nombre);
        empty($data->localizacion) ? true : $almacen->setLocalizacion($data->localizacion);
        empty($data->descripcion) ? true : $almacen->setDescripcion($data->descripcion);
        $this->almacenesRepository->save($almacen, true);
        return new JsonResponse(['status' => $mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name: "AEV3_almacenes_delete", methods: ['DELETE'])]
    public function remove(Almacenes $almacen): JsonResponse
    {
        $almacen_nombre = $almacen->getNombre();
        $this->almacenesRepository->remove($almacen, true);
        return new JsonResponse(['status' => 'Almacén ' . $almacen_nombre . ' borrado'], Response::HTTP_OK);
    }
}
