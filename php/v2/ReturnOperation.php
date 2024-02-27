<?php

namespace App\V2;

use App\Enum\NotificationEnum;
use App\Enum\StatusEnum;
use App\Events\MailEvents\ChangeReturnStatusEvent;
use App\Events\MailEvents\NewReturnStatusEvent;
use App\Http\Requests\NotificationReturnOperationRequest;
use App\Models\Contractor;
use App\Models\Employee;
use App\Models\Seller;
use App\V2\Events\NotificationEvent;
use Exception;

/**
 * В данном задании есть совсем примитивные ошибки
 * и так же есть архитектурные ошибки в структуре кода. Добавил валидацию реквеста, эвенты а ля ларавель,
 * предположительные модели, классы с константами и исключения
 */
class TsReturnOperation extends ReferencesOperation
{
    /**
     * @throws Exception
     */
    public function doOperation(): array
    {
        $request = new NotificationReturnOperationRequest($_REQUEST);
        $data = $request->validated('data');

        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ];

        $resellerId = $data['resellerId'];
        $notificationType = $data['notificationType'];

        $reseller = Seller::getById($resellerId);
        if (is_null($reseller)) {
            throw new Exception('Seller not found!', 400);
        }

        $client = Contractor::getById($data['clientId']);
        if (
            is_null($client)
            || $client->getType() !== Contractor::TYPE_CUSTOMER
            || $client->Seller->getId() !== $resellerId
        ) {
            throw new Exception('Client not found!', 400);
        }
        $clientFullName = $client->getFullName();

        $creator = Employee::getById((int)$data['creatorId']);
        if (is_null($creator)) {
            throw new Exception('Creator not found!', 400);
        }

        $expert = Employee::getById((int)$data['expertId']);
        if (is_null($expert)) {
            throw new Exception('Expert not found!', 400);
        }

        $differences = '';
        if ($notificationType === NotificationEnum::TYPE_NEW) {
            $differences = __('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === NotificationEnum::TYPE_CHANGE && !empty($data['differences'])) {
            $differences = __(
                'PositionStatusHasChanged',
                [
                    'FROM' => StatusEnum::getNameByCode((int)$data['differences']['from']),
                    'TO' => StatusEnum::getNameByCode((int)$data['differences']['to']),
                ],
                $resellerId
            );
        }

        $templateData = [
            'COMPLAINT_ID' => $data['complaintId'],
            'COMPLAINT_NUMBER' => $data['complaintNumber'],
            'CREATOR_ID' => $data['creatorId'],
            'CREATOR_NAME' => $creator->getFullName(),
            'EXPERT_ID' => $data['expertId'],
            'EXPERT_NAME' => $expert->getFullName(),
            'CLIENT_ID' => $data['clientId'],
            'CLIENT_NAME' => $clientFullName,
            'CONSUMPTION_ID' => $data['consumptionId'],
            'CONSUMPTION_NUMBER' => $data['consumptionNumber'],
            'AGREEMENT_NUMBER' => $data['agreementNumber'],
            'DATE' => $data['date'],
            'DIFFERENCES' => $differences,
        ];

        $emailFrom = NotificationEnum::getResellerEmailFrom();
        // Получаем email сотрудников из настроек
        $emails = NotificationEnum::getEmailsByPermit($resellerId, 'tsGoodsReturn');
        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) {
                NewReturnStatusEvent::dispatch(
                    [
                        [
                            'emailFrom' => $emailFrom,
                            'emailTo' => $email,
                            'subject' => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                            'message' => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                        ],
                    ],
                    $resellerId
                );

                $result['notificationEmployeeByEmail'] = true;
            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === NotificationEnum::TYPE_CHANGE && !empty($data['differences']['to'])) {
            if (!empty($emailFrom) && !empty($client->getEmail())) {
                ChangeReturnStatusEvent::dispatch(
                    [
                        [
                            'emailFrom' => $emailFrom,
                            'emailTo' => $client->getEmail(),
                            'subject' => __('complaintClientEmailSubject', $templateData, $resellerId),
                            'message' => __('complaintClientEmailBody', $templateData, $resellerId),
                        ],
                    ],
                    $resellerId,
                    $client->getId(),
                    $data['differences']['to']
                );

                $result['notificationClientByEmail'] = true;
            }

            if (!empty($client->getMobile())) {
                NotificationEvent::dispatch(
                    $resellerId,
                    $client->getId(),
                    ChangeReturnStatusEvent::EVENT_NAME,
                    (int)$data['differences']['to'],
                    $templateData
                );

                $result['notificationClientBySms']['isSent'] = true;
            } else {
                $result['notificationClientBySms']['message'] = 'Empty mobile number!';
            }
        }

        return $result;
    }
}
