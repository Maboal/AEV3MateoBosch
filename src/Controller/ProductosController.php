<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\Almacenes;
use App\Repository\ProductosRepository;
use App\Repository\AlmacenesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/productos', name: 'AEV3_productos')]
class ProductosController extends AbstractController
{

    private ProductosRepository $productosRepository;
    private AlmacenesRepository $almacenesRepository;

    public function __construct(ProductosRepository $productosRepo, AlmacenesRepository $almacenesRepo)
    {
        $this->productosRepository = $productosRepo;
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'AEV3_productos_show_all' ,methods: ['GET','POST'])]
    public function showAll(): JsonResponse
    {
        $productos = $this->productosRepository->findAll();
        $data = [];
        
        foreach ($productos as $producto){
            $data[] = [
                'descripcion' => $producto->getDescripcion(),
                'id_almacen' => $producto->getAlmacen()->getId(),
                'unidad' => $producto->getUnidad(),
                'clasificacion' => $producto->getClasificacion(),
                'preciounidad' => $producto->getPreciounidad()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'AEV3_productos_show', methods:['GET'])]
    public function show(Productos $producto):JsonResponse
    {
        $data[] = [
            'descripcion' => $producto->getDescripcion(),
            'id_almacen' => $producto->getAlmacen()->getId(),
            'unidad' => $producto->getUnidad(),
            'clasificacion' => $producto->getClasificacion(),
            'preciounidad' => $producto->getPreciounidad()
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'AEV3_productos_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {   
        $data = json_decode($request->getContent());
        
        $almacen = $this->almacenesRepository->find($data->id_almacen);
        
        $producto = new Productos();
        $producto ->setDescripcion($data->descripcion)
                ->setAlmacen($almacen)
                ->setUnidad($data->unidad)
                ->setClasificacion($data->clasificacion)
                ->setPreciounidad($data->preciounidad);
                
        $this->productosRepository->save($producto, true);

        return new JsonResponse(['status'=> 'Producto creado'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_productos_edit', methods:['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $producto = $this->productosRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());

        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Producto actualizado completamente de forma satisfactoria';
        else
            $mensaje = 'Producto actualizado parcialemente de forma satisfactoria';
            //Utilizamos el operador ternario
        empty($data->descripcion) ? true : $producto->setDescripcion($data->descripcion);
        empty($data->id_almacen) ? true : $almacen = $this->almacenesRepository->find($data->id_almacen); $producto->setAlmacen($almacen);
        empty($data->unidad) ? true : $producto->setUnidad($data->unidad);
        empty($data->clasificacion) ? true : $producto->setClasificacion($data->clasificacion);
        empty($data->preciounidad) ? true : $producto->setPreciounidad($data->preciounidad);
        $this->productosRepository->save($producto, true);
        return new JsonResponse(['status'=>$mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name:"AEV3_productos_delete", methods:['DELETE'])]
    public function remove(Productos $producto): JsonResponse
    {
        $producto_id = $producto->getId();
        $this->productosRepository->remove($producto, true);
        return new JsonResponse(['status'=>'Producto '. $producto_id.' borrado'], Response::HTTP_OK);
    }
}