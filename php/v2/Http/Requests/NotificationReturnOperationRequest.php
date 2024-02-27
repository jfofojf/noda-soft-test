<?php

namespace App\Http\Requests;

use App\Exceptions\NotificationRequestException;

class NotificationReturnOperationRequest
{
    private array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * @throws NotificationRequestException
     */
    public function validated(string $key): array
    {
        if (!isset($this->request[$key])) {
            throw new NotificationRequestException('Request Data is empty!', 500);
        }

        $data = [
            'complaintId' => (int)$this->request['complaintId'] ?? null,
            'complaintNumber' => $this->request['complaint'] ?? null,
            'creatorId' => (int)$this->request['creatorId'] ?? null,
            'expertId' => (int)$this->request['expertId'] ?? null,
            'clientId' => (int)$this->request['clientId'] ?? null,
            'consumptionId' => (int)$this->request['consumptionId'] ?? null,
            'consumptionNumber' => $this->request['consumptionNumber'] ?? null,
            'agreementNumber' => $this->request['agreementNumber'] ?? null,
            'date' => $this->request['date'] ?? null,
            'differences' => $this->request['differences'] ?? null,
            'resellerId' => (int)$this->request['resellerId'] ?? null,
            'notificationType' => $this->request['notificationType'] ?? null,
        ];

        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new NotificationRequestException(sprintf('Request Data (%s) is empty!', $key), 500);
            }
        }

        return $data;
    }
}