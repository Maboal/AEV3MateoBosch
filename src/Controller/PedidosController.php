<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Entity\Empresas;
use App\Repository\PedidosRepository;
use App\Repository\EmpresasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/pedidos', name: 'AEV3_pedidos')]
class PedidosController extends AbstractController
{

    private PedidosRepository $pedidosRepository;
    private EmpresasRepository $empresasRepository;

    public function __construct(PedidosRepository $pedidosRepo, EmpresasRepository $empresasRepo)
    {
        $this->pedidosRepository = $pedidosRepo;
        $this->empresasRepository = $empresasRepo;
    }

    #[Route('/', name: 'AEV3_pedidos_show_all', methods: ['GET', 'POST'])]
    public function showAll(): JsonResponse
    {
        $pedidos = $this->pedidosRepository->findAll();
        $data = [];
        $dataFacturas = [];
        $dataLineaspedidos = [];

        foreach ($pedidos as $pedido) {
            foreach ($pedido->getFacturas() as $factura) {
                $dataFacturas = ['factura' => [
                    'factura_id' => $factura->getId(),
                    'factura_valor' => $factura->getValor()]];
            }
            foreach ($pedido->getLineaspedidos() as $lineaspedidos) {
                $dataLineaspedidos = ['lineaspedidos' => [
                    'lineaspedidos_id' => $lineaspedidos->getId(),
                    'lineaspedidos_cantidad' => $lineaspedidos->getCantidad(),
                    'lineaspedidos_precio' => $lineaspedidos->getPrecio()]];
            }
                $data[] = [
                    'tipo' => $pedido->getTipo(),
                    'fecha' => $pedido->getFecha(),
                    'observacion' => $pedido->getObservacion(),
                    'empresa' => [
                        'empresa_id' => $pedido->getEmpresa()->getId(),
                        'empresa_nombre' => $pedido->getEmpresa()->getNombre()
                    ]];   
            array_push($data, $dataFacturas);
            array_push($data, $dataLineaspedidos);
                    
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'AEV3_pedidos_show', methods: ['GET'])]
    public function show(Pedidos $pedido): JsonResponse
    {
        foreach ($pedido->getFacturas() as $factura) {
            $dataFacturas = ['factura' => [
                'factura_id' => $factura->getId(),
                'factura_valor' => $factura->getValor()]];
        }
        foreach ($pedido->getLineaspedidos() as $lineaspedidos) {
            $dataLineaspedidos = ['lineaspedidos' => [
                'lineaspedidos_id' => $lineaspedidos->getId(),
                'lineaspedidos_cantidad' => $lineaspedidos->getCantidad(),
                'lineaspedidos_precio' => $lineaspedidos->getPrecio()]];
        }
            $data[] = [
                'tipo' => $pedido->getTipo(),
                'fecha' => $pedido->getFecha(),
                'observacion' => $pedido->getObservacion(),
                'empresa' => [
                    'empresa_id' => $pedido->getEmpresa()->getId(),
                    'empresa_nombre' => $pedido->getEmpresa()->getNombre()
                ]];   
        array_push($data, $dataFacturas);
        array_push($data, $dataLineaspedidos);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name: 'AEV3_pedidos_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $fecha = date_create($data->fecha);

        $empresa = $this->empresasRepository->find($data->id_empresa);

        $pedido = new Pedidos();
        $pedido->setTipo($data->tipo)
            ->setFecha($fecha)
            ->setObservacion($data->observacion)
            ->setEmpresa($empresa);

        $this->pedidosRepository->save($pedido, true);

        return new JsonResponse(['status' => 'Pedido creado'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_pedidos_edit', methods: ['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $pedido = $this->pedidosRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());

        if ($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Pedido actualizado completamente de forma satisfactoria';
        else
            $mensaje = 'Pedido actualizado parcialemente de forma satisfactoria';
        //Utilizamos el operador ternario
        empty($data->tipo) ? true : $pedido->setTipo($data->tipo);
        empty($data->fecha) ? true : $pedido->setfecha(date_create($data->fecha));
        empty($data->observacion) ? true : $pedido->setObservacion($data->observacion);
        empty($data->id_empresa) ? true : $empresa = $this->empresasRepository->find($data->id_empresa);
        $pedido->setEmpresa($empresa);
        $this->pedidosRepository->save($pedido, true);
        return new JsonResponse(['status' => $mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name: "AEV3_pedidos_delete", methods: ['DELETE'])]
    public function remove(Pedidos $pedido): JsonResponse
    {
        $pedido_id = $pedido->getId();
        $this->pedidosRepository->remove($pedido, true);
        return new JsonResponse(['status' => 'Pedido ' . $pedido_id . ' borrado'], Response::HTTP_OK);
    }
}
