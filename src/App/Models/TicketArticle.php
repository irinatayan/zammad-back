<?php

namespace App\Models;
class TicketArticle {
    public $ticket_id = '';
    public $subject = 'Agent note';
    public $body = 'Note';
    public $content_type = 'text/html';
    public $type = 'note';
    public $sender = 'Agent';
    public $time_unit = '15';

    public $attachments = [];
}
