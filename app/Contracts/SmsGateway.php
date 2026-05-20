<?php

namespace App\Contracts;

interface SmsGateway
{
    /**
     * @return array{success: bool, provider: string, error?: string}
     */
    public function send(string $phone, string $message): array;
}
