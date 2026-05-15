<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class MailtrapApiTransport extends AbstractTransport
{
    protected $apiToken;
    protected $inboxId;

    public function __construct(string $apiToken, string $inboxId)
    {
        $this->apiToken = $apiToken;
        $this->inboxId = $inboxId;
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = [
            'from' => [
                'email' => $email->getFrom()[0]->getAddress(),
                'name' => $email->getFrom()[0]->getName(),
            ],
            'to' => array_map(function ($to) {
                return [
                    'email' => $to->getAddress(),
                    'name' => $to->getName(),
                ];
            }, $email->getTo()),
            'subject' => $email->getSubject(),
            'html' => $email->getHtmlBody(),
            'text' => $email->getTextBody(),
        ];

        // Add CC if exists
        if ($email->getCc()) {
            $payload['cc'] = array_map(function ($cc) {
                return [
                    'email' => $cc->getAddress(),
                    'name' => $cc->getName(),
                ];
            }, $email->getCc());
        }

        // Add BCC if exists
        if ($email->getBcc()) {
            $payload['bcc'] = array_map(function ($bcc) {
                return [
                    'email' => $bcc->getAddress(),
                    'name' => $bcc->getName(),
                ];
            }, $email->getBcc());
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
        ])->post("https://send.api.mailtrap.io/api/send", $payload);

        if (!$response->successful()) {
            throw new \Exception('Mailtrap API Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'mailtrap-api';
    }
}
