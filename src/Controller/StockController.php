<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Productos;
use App\Entity\Almacenes;
use App\Repository\StockRepository;
use App\Repository\ProductosRepository;
use App\Repository\AlmacenesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock', name: 'AEV3_stock')]
class StockController extends AbstractController
{
    private StockRepository $stockRepository;
    private ProductosRepository $productosRepository;
    private AlmacenesRepository $almacenesRepository;

    public function __construct(StockRepository $stockRepo, ProductosRepository $productosRepo, AlmacenesRepository $almacenesRepo)
    {
        $this->stockRepository = $stockRepo;
        $this->productosRepository = $productosRepo;
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'AEV3_stock_show_all' ,methods: ['GET','POST'])]
    public function showAll(): JsonResponse
    {
        $stocks = $this->stockRepository->findAll();
        $data = [];
        
        foreach ($stocks as $stock){
            $data[] = [
                'fecha' => $stock->getFecha(),
                'producto' =>[
                    'producto_id' => $stock->getProducto()->getId(),
                    'producto_descripcion' => $stock->getProducto()->getDescripcion(),
                    'producto_preciounidad' => $stock->getProducto()->getPreciounidad()
                ], 
                'cantidad' => $stock->getCantidad(),
                'stock' => $stock->getStock(),
                'precio' => $stock->getPrecio(),
                'unidad' => $stock->getUnidad(),
                'almacen' =>[
                    'almacen_id' => $stock->getAlmacen()->getId(),
                    'almacen_nombre' => $stock->getAlmacen()->getNombre(),
                    'almacen_localizacion' => $stock->getAlmacen()->getLocalizacion()
                ] 
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'AEV3_stock_show', methods:['GET'])]
    public function show(Stock $stock):JsonResponse
    {
        $data[] = [
            'fecha' => $stock->getFecha(),
            'producto' =>[
                'producto_id' => $stock->getProducto()->getId(),
                'producto_descripcion' => $stock->getProducto()->getDescripcion(),
                'producto_preciounidad' => $stock->getProducto()->getPreciounidad()
            ], 
            'cantidad' => $stock->getCantidad(),
            'stock' => $stock->getStock(),
            'precio' => $stock->getPrecio(),
            'unidad' => $stock->getUnidad(),
            'almacen' =>[
                'almacen_id' => $stock->getAlmacen()->getId(),
                'almacen_nombre' => $stock->getAlmacen()->getNombre(),
                'almacen_localizacion' => $stock->getAlmacen()->getLocalizacion()
            ] 
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'AEV3_stock_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {   
        $data = json_decode($request->getContent());
        
        $producto = $this->productosRepository->find($data->id_producto);
        $almacen = $this->almacenesRepository->find($data->id_almacen);
        
        $stock = new Stock();
        $stock ->setFecha(date_create($data->fecha))
                ->setProducto($producto)
                ->setCantidad($data->cantidad)
                ->setStock($data->stock)
                ->setPrecio($data->precio)
                ->setUnidad($data->unidad)
                ->setAlmacen($almacen);
                
        $this->stockRepository->save($stock, true);

        return new JsonResponse(['status'=> 'Stock creado'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_stock_edit', methods:['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $stock = $this->stockRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());

        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Stock actualizado completamente de forma satisfactoria';
        else
            $mensaje = 'Stock actualizado parcialemente de forma satisfactoria';
            //Utilizamos el operador ternario
        empty($data->fecha) ? true : $stock->setFecha(date_create($data->fecha));
        empty($data->id_producto) ? true : $producto = $this->productosRepository->find($data->id_producto); $stock->setProducto($producto);
        empty($data->cantidad) ? true : $stock->setCantidad($data->cantidad);
        empty($data->stock) ? true : $stock->setStock($data->stock);
        empty($data->precio) ? true : $stock->setPrecio($data->precio);
        empty($data->unidad) ? true : $stock->setUnidad($data->unidad);
        empty($data->id_almacen) ? true : $almacen = $this->almacenesRepository->find($data->id_almacen); $stock->setAlmacen($almacen);
        $this->stockRepository->save($stock, true);
        return new JsonResponse(['status'=>$mensaje], Response::HTTP_CREATED);
    }

    
    #[Route('/delete/{id}', name:"AEV3_stock_delete", methods:['DELETE'])]
    public function remove(Stock $stock): JsonResponse
    {
        $stock_id = $stock->getId();
        $this->stockRepository->remove($stock, true);
        return new JsonResponse(['status'=>'Stock '. $stock_id.' borrado'], Response::HTTP_OK);
    }
}
