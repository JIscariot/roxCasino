<?php

namespace App;

class MessageService
{
    private TelegramService $telegramService;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
    }

    public function updateFromTelegramFeed()
    {
        $messages = $this->telegramService->getMessages();

        EntityManager::getInstance()
            ->getConnection()
            ->beginTransaction();

        foreach ($messages as $message) {
            $entity = EntityManager::getInstance()->getRepository(Message::class)
                ->findOneBy(['tid' => $message->id]);
            if(!$entity) {
                $entity = Message::createFromTelegramMessage(
                    $message
                );
            }
            EntityManager::getInstance()->persist($entity);
            EntityManager::getInstance()->flush();

            echo 'Message processed id: ' . $entity->getId() . PHP_EOL;
        }

        EntityManager::getInstance()
            ->getConnection()
            ->commit();

        return true;
    }
}