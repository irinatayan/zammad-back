<?php

declare(strict_types=1);

namespace App\Handler\Ticket;


use App\Request;
use App\Response;
use App\ZammadClient;
use DateTime;
use ZammadAPIClient\ResourceType;
use Carbon\Carbon;



class TicketState
{
    public function __invoke(Request $request, Response $response): void
    {



        $client = (new ZammadClient())->getClient();
        $params = json_decode(file_get_contents('php://input'), true);

        $ticket = $client->resource(ResourceType::TICKET)->get($params['ticketId']);
        $pendingTime = $params['pendingTime'];


        if (is_string($pendingTime)) {

            $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.v\Z', $pendingTime);

            if (!($dateTime && $dateTime->format('Y-m-d\TH:i:s.v\Z') === $pendingTime)) {
                $date = Carbon::now();

                if ($pendingTime === "0.5") {
                    $date->addHours(12);
                }
                else {
                    $date->addDays((int) $pendingTime);
                    $date->setTime(9, 0);
                }

                $pendingTime = $date->toIso8601String();
            }
        }



        $ticket->setValue('state', $params['stateName']);
        $ticket->setValue('pending_time', $pendingTime);
        $ticket->save();

        (new Response())->success()->send();
    }

}