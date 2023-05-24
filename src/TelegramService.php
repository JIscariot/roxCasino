<?php

namespace App;

use GuzzleHttp\Client;

class TelegramService
{
    private string $requestUri;

    private Client $httpClient;

    public function __construct()
    {
        $this->requestUri = trim(
            parse_url($_ENV['TELEGRAM_GROUP'], PHP_URL_PATH), '/'
        );
        $this->httpClient = new Client([
            'base_uri' => 'https://t.me/s/',
            'headers' => [
                'content-length' => 0, 'x-requested-with' => 'XMLHttpRequest'
            ],
        ]);
    }

    public function getMessages(int $count = 100): \Generator
    {
        $total = 0;
        do {
            $response = $this->httpClient->post($this->requestUri);
            $responseBody = json_decode(
                $response->getBody()->getContents()
            );

            $telegramFeed = new TelegramMessageFeed($responseBody);
            foreach ($telegramFeed->getMessages() as $message) {
                yield $this->updateMessage($message);
                $total++;
            }
            $this->requestUri = $telegramFeed->getPreviousPageUrl();
        } while ($total < $count);
    }

    public function updateMessage(TelegramMessage $message): TelegramMessage
    {
        $fileName = md5($message->previewLink);
        $fileExtension = pathinfo(
            $message->previewLink, PATHINFO_EXTENSION
        );
        $message->previewLink = __DIR__ . '/../' . $_ENV['MEDIA_FOLDER'] . '/' . $fileName . '.'
            . $fileExtension;

        if (file_exists($message->previewLink )) {
            return $message;
        }

        $responseBody = $this->httpClient->get($message->previewLink)
            ->getBody()
            ->getContents();

        file_put_contents($message->previewLink, $responseBody);

        return $message;
    }
}