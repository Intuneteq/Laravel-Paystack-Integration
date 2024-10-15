<?php

namespace Intune\LaravelPaystack\Dtos;

use Intune\LaravelPaystack\Exceptions\DtoCastException;

/**
 * Data Transfer Object for Paystack Transfer Requests.
 *
 * This DTO encapsulates the data associated with a transfer request made to the Paystack API.
 *
 * @author @Intuneteq
 * @version 1.0
 * @since 15-10-2024
 * @package Intune\LaravelPaystack\Dtos
 */
class TransferDto implements IDtoFactory
{
    /**
     * TransferDto constructor.
     *
     * @param string $amount      The amount of the transfer in the smallest currency unit (e.g., kobo for NGN).
     * @param string $code        The unique transfer code associated with this transfer.
     * @param string $created_at   The timestamp indicating when the transfer was created.
     */
    private function __construct(
        private string $amount,
        private string $code,
        private string $created_at
    ) {
        // Convert amount from smallest currency unit to standard currency unit (e.g., kobo to Naira)
        $this->amount = (string) ((int) $this->amount / 100);
    }

    /**
     * Get the amount of the transfer.
     *
     * @return string The amount of the transfer in standard currency unit.
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Get the transfer code.
     *
     * @return string The unique code associated with this transfer.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the creation timestamp of the transfer.
     *
     * @return string The timestamp when the transfer was created.
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * Convert the DTO properties to an associative array.
     *
     * @return array Associative array representation of the DTO's properties.
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'transfer_code' => $this->code,
            'createdAt' => $this->created_at,
        ];
    }

    /**
     * Create an instance of TransferDto from an array of data.
     *
     * @param array $data Input data for creating the TransferDto.
     *
     * @return self
     * @throws DtoCastException If required fields (amount, transfer_code, createdAt) are missing.
     */
    public static function create(array $data): self
    {
        if (! isset($data['amount'], $data['transfer_code'], $data['createdAt'])) {
            throw new DtoCastException(self::class);
        }

        return new self(
            $data['amount'],
            $data['transfer_code'],
            $data['createdAt']
        );
    }
}
