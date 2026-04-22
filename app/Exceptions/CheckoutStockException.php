<?php

namespace App\Exceptions;

use RuntimeException;

class CheckoutStockException extends RuntimeException
{
    /**
     * @param  array<int, array{product:string,message:string,requested?:int,available?:int}>  $issues
     */
    public function __construct(private readonly array $issues)
    {
        parent::__construct('Checkout gagal karena stok item tidak mencukupi.');
    }

    /**
     * @return array<int, array{product:string,message:string,requested?:int,available?:int}>
     */
    public function issues(): array
    {
        return $this->issues;
    }
}