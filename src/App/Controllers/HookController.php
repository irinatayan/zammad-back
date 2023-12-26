<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TicketArticle;
use App\Services\TicketService;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;


readonly class HookController
{
    public function __construct(private Client $client, private TicketService $ticketService)
    {
    }

    public function sendNote(): void
    {
//        token: 4wDPTuidhS8pmfXWGYWkQMlm31rB0_qInFC7tUPZAX8N08pEmoi3LFKWgz9g0Qn2
        $ticket_article = $this->client->resource(ResourceType::TICKET_ARTICLE);

        $attachments = [];
        foreach ($_FILES as $key => $file) {
            $fileContent = base64_encode(file_get_contents($file['tmp_name']));

            $mime_type = mime_content_type($file['tmp_name']);

            $filename = $file['name'];
            if (str_contains($key, "voice_message")) {
                $filename = $key;
            }

            $attachment = [
                'filename' => $filename,
                'data' => $fileContent,
                'mime-type' => $mime_type,
                'type' => 'phone'
            ];
            $attachments[] = $attachment;
        }

        $new_article = new TicketArticle();
        $new_article->ticket_id = $_POST['ticketId'];
        $new_article->body = $_POST['message'];
        $new_article->attachments = $attachments;

        foreach ($new_article as $var => $value) {
            $ticket_article->setValue($var, $value);
        }

        $ticket_article->save();
    }

    public function getVoiceAttachment(): void
    {
        //curl -H "Authorization: Token token=4wDPTuidhS8pmfXWGYWkQMlm31rB0_qInFC7tUPZAX8N08pEmoi3LFKWgz9g0Qn2" --output fff https://test-zammad-dev.zammad.com/api/v1/ticket_attachment/4/79/68
        $ticketId = 4;
        $ticket = $this->client->resource(ResourceType::TICKET)->get($ticketId);
        $response = $this->client->get('/api/v1/ticket_attachment/4/82/71');
//        /api/v1/ticket_attachment/{ticket id}/{article id}/{attachment id}
        $content = $response->getBody();
        header("Content-type: application/json; charset=UTF-8");
        echo $content;

    }
}
