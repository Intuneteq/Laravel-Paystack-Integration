<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Exceptions\DtoCastException;

class TransactionInitializationDto implements IDtoFactory
{
    private function __construct(
        private string $authorization_url,
        private string $access_code,
        private string $reference,

    ) {}

    /**
     * Get the authorization URL.
     */
    public function getAuthorizationUrl(): string
    {
        return $this->authorization_url;
    }

    /**
     * Get the access code.
     */
    public function getAccessCode(): string
    {
        return $this->access_code;
    }

    /**
     * Get the reference.
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Retrieve formatted Result
     */
    public function toArray(): array
    {
        return [
            'authorization_url' => $this->getAuthorizationUrl(),
            'access_code' => $this->getAccessCode(),
            'reference' => $this->getReference(),
        ];
    }

    /**
     * Create a new instance of TransactionInitializationDto from array data.
     *
     * @param  array  $data  The array containing initialization data.
     *
     * @throws DtoCastException If required fields are missing in $data.
     */
    public static function create(array $data): self
    {
        if (! isset($data['authorization_url'], $data['access_code'], $data['reference'])) {
            throw new DtoCastException(self::class);
        }

        return new self(
            $data['authorization_url'],
            $data['access_code'],
            $data['reference']
        );
    }
}
