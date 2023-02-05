<?php

namespace App\Controller;

use App\Entity\Empresas;
use App\Repository\EmpresasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/empresas', name: 'AEV3_empresas')]
class EmpresasController extends AbstractController
{

    private EmpresasRepository $empresasRepository;

    public function __construct(EmpresasRepository $empresasRepo)
    {
        $this->empresasRepository = $empresasRepo;
    }

    #[Route('/', name: 'AEV3_empresas_show_all', methods: ['GET', 'POST'])]
    public function showAll(): JsonResponse
    {
        $empresas = $this->empresasRepository->findAll();
        $data = [];
        $dataPedidos = [];

        foreach ($empresas as $empresa) {
            foreach ($empresa->getPedidos() as $pedido) {
                $dataPedidos = ['pedidos' => [
                    "pedido_id" => $pedido->getId(),
                    "pedido_observacion" => $pedido->getObservacion()
                ]];
            }
            $data[] = [
                'nombre' => $empresa->getNombre(),
                'CIF' => $empresa->getCIF(),
                'tipo' => $empresa->getTipo(),
            ];
            array_push($data, $dataPedidos);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'AEV3_empresas_show', methods: ['GET'])]
    public function show(Empresas $empresa): JsonResponse
    {
        $data = [];
        $dataPedidos = [];


        foreach ($empresa->getPedidos() as $pedido) {
            $dataPedidos = ['pedido' => [
                "pedido_id" => $pedido->getId(),
                "pedido_observacion" => $pedido->getObservacion()
            ]];
        }
        $data[] = [
            'nombre' => $empresa->getNombre(),
            'CIF' => $empresa->getCIF(),
            'tipo' => $empresa->getTipo(),
        ];

        array_push($data, $dataPedidos);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name: 'AEV3_empresas_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());

        $empresa = new Empresas();
        $empresa->setNombre($data->nombre)
            ->setCIF($data->CIF)
            ->setTipo($data->tipo);

        $this->empresasRepository->save($empresa, true);

        return new JsonResponse(['status' => 'Empresa creada'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'AEV3_empresas_edit', methods: ['PUT', 'PATCH'])]
    public function edit($id, Request $request): JsonResponse
    {
        $empresa = $this->empresasRepository->find($id);
        //Recibimos los datos como si fueran un objeto
        $data = json_decode($request->getContent());
        if ($_SERVER['REQUEST_METHOD'] == 'PUT')
            $mensaje = 'Empresa actualizada completamente de forma satisfactoria';
        else
            $mensaje = 'Empresa actualizada parcialemente de forma satisfactoria';
        //Utilizamos el operador ternario
        empty($data->nombre) ? true : $empresa->setNombre($data->nombre);
        empty($data->CIF) ? true : $empresa->setCIF($data->CIF);
        empty($data->tipo) ? true : $empresa->setTipo($data->tipo);
        $this->empresasRepository->save($empresa, true);
        return new JsonResponse(['status' => $mensaje], Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name: "AEV3_empresas_delete", methods: ['DELETE'])]
    public function remove(Empresas $empresa): JsonResponse
    {
        $empresa_nombre = $empresa->getNombre();
        $this->empresasRepository->remove($empresa, true);
        return new JsonResponse(['status' => 'Empresa ' . $empresa_nombre . ' borrada'], Response::HTTP_OK);
    }
}
