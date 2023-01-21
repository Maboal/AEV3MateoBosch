<?php

namespace App\Controller;

use App\Entity\Lineaspedidos;
use App\Entity\Pedidos;
use App\Entity\Productos;
use App\Repository\LineaspedidosRepository;
use App\Repository\PedidosRepository;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/lineaspedidos', name: 'AEV3_lineaspedidos')]
class LineaspedidosController extends AbstractController
{

    private LineaspedidosRepository $lineaspedidosRepository;
    private PedidosRepository $pedidosRepository;
    private ProductosRepository $productosRepository;

    public function __construct(LineaspedidosRepository $lineaspedidosRep, PedidosRepository $pedidosRepo, ProductosRepository $productosRepo )
    {
        $this->lineaspedidosRepository = $lineaspedidosRep;
        $this->pedidosRepository = $pedidosRepo;
        $this->productosRepository = $productosRepo;
    }

    #[Route('/', name: 'AEV3_lineaspedidos_show_all' ,methods: ['GET','POST'])]
    public function showAll(): JsonResponse
    {
        $lineaspedidos = $this->lineaspedidosRepository->findAll();
        $data = [];
        
        foreach ($lineaspedidos as $lineaspedido){
            $data[] = [
                'id_pedido' => $lineaspedido->getPedido()->getId(),
                'id_producto' => $lineaspedido->getProducto()->getId(),
                'cantidad' => $lineaspedido->getCantidad(),
                'precio' => $lineaspedido->getPrecio()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'AEV3_lineaspedidos_show', methods:['GET'])]
    public function show(Lineaspedidos $lineaspedido):JsonResponse
    {
        $data[] = [
            'id_pedido' => $lineaspedido->getPedido()->getId(),
            'id_producto' => $lineaspedido->getProducto()->getId(),
            'cantidad' => $lineaspedido->getCantidad(),
            'precio' => $lineaspedido->getPrecio()
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'AEV3_lineaspedidos_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {   
        $data = json_decode($request->getContent());
        
        $pedido = $this->pedidosRepository->find($data->id_pedido);
        $producto = $this->productosRepository->find($data->id_producto);
        
        $lineaspedido = new Lineaspedidos();
        $lineaspedido ->setPedido($pedido)
                      ->setProducto($producto)
                      ->setCantidad($data->cantidad)
                      ->setPrecio($data->precio);

        $this->lineaspedidosRepository->save($lineaspedido, true);

        return new JsonResponse(['status'=> 'Lineaspedido creado'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_lineaspedidos_edit', methods:['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $lineaspedido = $this->lineaspedidosRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());

        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Lineaspedido actualizado completamente de forma satisfactoria';
        else
            $mensaje = 'Lineaspedido actualizado parcialemente de forma satisfactoria';
            //Utilizamos el operador ternario
        empty($data->id_pedido) ? true : $pedido = $this->pedidosRepository->find($data->id_pedido); $lineaspedido->setPedido($pedido);
        empty($data->id_producto) ? true : $producto = $this->productosRepository->find($data->id_producto); $lineaspedido->setProducto($producto);
        empty($data->cantidad) ? true : $lineaspedido->setCantidad($data->cantidad);
        empty($data->precio) ? true : $lineaspedido->setPrecio($data->precio);
        $this->lineaspedidosRepository->save($lineaspedido, true);
        return new JsonResponse(['status'=>$mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name:"AEV3_lineaspedidos_delete", methods:['DELETE'])]
    public function remove(Lineaspedidos $lineaspedido): JsonResponse
    {
        $lineaspedido_id = $lineaspedido->getId();
        $this->lineaspedidosRepository->remove($lineaspedido, true);
        return new JsonResponse(['status'=>'Lineaspedido '. $lineaspedido_id.' borrado'], Response::HTTP_OK);
    }
}
