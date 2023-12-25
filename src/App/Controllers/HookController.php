<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TicketArticle;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;


readonly class HookController
{
    public function __construct(private Client $client,)
    {
    }

    public function sendNote(): void
    {
//        token: 2X2r4Rp51W2HYSJfDn6RFjZY45h_2F_j372fRgyMav_uUjfN7m5vlJBi3g8G_Miz
        $ticket_article = $this->client->resource(ResourceType::TICKET_ARTICLE);

        $attachments = [];
        foreach ($_FILES as $file) {
            $fileContent = base64_encode(file_get_contents($file['tmp_name']));
            $attachment = [
                'filename' => $file['name'],
                'data' => $fileContent,
                'mime-type' => 'text/plain'
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
}
