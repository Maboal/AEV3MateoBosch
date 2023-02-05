<?php

namespace App\Controller;

use App\Entity\Facturas;
use App\Entity\Pedidos;
use App\Repository\FacturasRepository;
use App\Repository\PedidosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/facturas', name: 'AEV3_facturas')]
class FacturasController extends AbstractController
{
    
    private FacturasRepository $facturasRepository;
    private PedidosRepository $pedidosRepository;

    public function __construct(FacturasRepository $facturasRepo, PedidosRepository $pedidosRepo)
    {
        $this->facturasRepository = $facturasRepo;
        $this->pedidosRepository = $pedidosRepo;
    }

    #[Route('/', name: 'AEV3_facturas_show_all' ,methods: ['GET','POST'])]
    public function showAll(): JsonResponse
    {
        $facturas = $this->facturasRepository->findAll();
        $data = [];
        
        foreach ($facturas as $factura){
            $data[] = [
                'fecha' => $factura->getFecha(),
                'tipo' => $factura->getTipo(),
                'valor' => $factura->getValor(),
                'pedidos' => [
                    'pedido_id' => $factura->getPedido()->getId(),
                    'pedido_observacion' => $factura->getPedido()->getObservacion()
                ]
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'AEV3_facturas_show', methods:['GET'])]
    public function show(Facturas $factura):JsonResponse
    {
        $data[] = [
            'fecha' => $factura->getFecha(),
            'tipo' => $factura->getTipo(),
            'valor' => $factura->getValor(),
            'pedidos' => [
                'pedido_id' => $factura->getPedido()->getId(),
                'pedido_observacion' => $factura->getPedido()->getObservacion()
            ]
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'AEV3_facturas_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {   
        $data = json_decode($request->getContent());
        $fecha = date_create($data->fecha);
        
        $pedido = $this->pedidosRepository->find($data->id_pedido);
        
        $factura = new Facturas();
        $factura ->setFecha($fecha)
                ->setTipo($data->tipo)
                ->setValor($data->valor)
                ->setPedido($pedido);

        $this->facturasRepository->save($factura, true);

        return new JsonResponse(['status'=> 'Factura creada'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_facturas_edit', methods:['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $factura = $this->facturasRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());

        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Factura actualizada completamente de forma satisfactoria';
        else
            $mensaje = 'Factura actualizada parcialemente de forma satisfactoria';
            //Utilizamos el operador ternario
        empty($data->fecha) ? true : $factura->setfecha(date_create($data->fecha));
        empty($data->tipo) ? true : $factura->setTipo($data->tipo);
        empty($data->valor) ? true : $factura->setValor($data->valor);
        empty($data->id_pedido) ? true : $pedido = $this->pedidosRepository->find($data->id_pedido); $factura->setPedido($pedido);
        $this->facturasRepository->save($factura, true);
        return new JsonResponse(['status'=>$mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name:"AEV3_facturas_delete", methods:['DELETE'])]
    public function remove(Facturas $factura): JsonResponse
    {
        $factura_id = $factura->getId();
        $this->facturasRepository->remove($factura, true);
        return new JsonResponse(['status'=>'Factura '. $factura_id.' borrada'], Response::HTTP_OK);
    }
}
