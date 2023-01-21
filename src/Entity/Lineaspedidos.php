<?php

namespace App\Entity;

use App\Repository\LineaspedidosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineaspedidosRepository::class)]
class Lineaspedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lineaspedidos')]
    #[ORM\JoinColumn(name:'id_pedido', nullable: false, referencedColumnName:'id')]
    private ?Pedidos $id_pedido = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $precio = null;

    #[ORM\ManyToOne(inversedBy: 'lineaspedidos')]
    #[ORM\JoinColumn(name:'id_producto', nullable: false, referencedColumnName:'id')]
    private ?Productos $id_producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): ?Pedidos
    {
        return $this->id_pedido;
    }

    public function setPedido(?Pedidos $id_pedido): self
    {
        $this->id_pedido = $id_pedido;

        return $this;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getProducto(): ?Productos
    {
        return $this->id_producto;
    }

    public function setProducto(?Productos $id_producto): self
    {
        $this->id_producto = $id_producto;

        return $this;
    }
}
