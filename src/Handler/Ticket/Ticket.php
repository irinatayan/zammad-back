<?php

declare(strict_types=1);

namespace App\Handler\Ticket;

use App\Request;
use App\Response;
use App\ZammadClient;
use ZammadAPIClient\ResourceType;

class Ticket
{
    public function __invoke(Request $request, Response $response): void
    {
        $params = $request->getParams();
        $client = (new ZammadClient())->getClient();
        $ticket = $client->resource(ResourceType::TICKET)->get($params['id']);

        $articles = $ticket->getTicketArticles();

        $ticketOrNull = !empty($ticket->getValues()) ? $ticket->getValues() : null;

        $articlesArr = [];
        foreach ($articles as $article) {
            $articlesArr[] = $article->getValues();
        }

        $data = [
            "ticket" => $ticketOrNull,
            "articles" => $articlesArr

        ];
        (new Response())->success($data)->send();

    }
}