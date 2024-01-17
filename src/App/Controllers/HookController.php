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

        if (empty($_POST['message'])) {
            http_response_code(405);
            echo json_encode([
                'message' => 'Message must have a body'
            ]);
            exit();
        }

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

        foreach (explode(",", $_POST['files']) as $fileName) {
            $targetDir = __DIR__ . "/../../../uploads/";
            $fileContent = base64_encode(file_get_contents($targetDir . $fileName));
            $mime_type = mime_content_type($targetDir . $fileName);
            $attachment = [
                'filename' => $fileName,
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

        $ticketId = $_GET['ticketId'] ?? null;
        $articleId = $_GET['articleId'] ?? null;
        $attachmentId = $_GET['attachmentId'] ?? null;
        $mimeType = $_GET['mimeType'] ?? null;

        $response = $this->client->get("/api/v1/ticket_attachment/{$ticketId}/$articleId/$attachmentId");
        $content = $response->getBody();
        header("Content-type: {$mimeType}");
        echo $content;

    }

    public function upload()
    {
        $targetDir = __DIR__ . "/../../../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        function handleFileUpload($file)
        {
            $targetDir = __DIR__ . "/../../../uploads/";

            // Generate a unique file name to avoid overwriting existing files
            $fileName = uniqid() . "-" . basename($file['name']);
            $filePath = $targetDir . $fileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                return $fileName;
            } else {
                return false;
            }
        }

// FilePond sends files as HTTP POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if a file has been sent
            if (isset($_FILES['filepond'])) {
                $file = $_FILES['filepond'];

                // Handle the file upload
                $result = handleFileUpload($file);

                if ($result) {
                    // Return the file name if upload was successful
                    echo json_encode(['fileName' => $result]);
                } else {
                    http_response_code(500);
                    echo "Error uploading file";
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Handle file deletion requests from FilePond
            $fileName = file_get_contents('php://input');

            if (unlink($targetDir . $fileName)) {
                echo "File deleted successfully";
            } else {
                http_response_code(500);
                echo "Error deleting file";
            }
        }
    }
}
