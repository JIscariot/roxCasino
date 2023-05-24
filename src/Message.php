<?php

namespace App;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'messages')]
#[ORM\Entity(
    repositoryClass: MessageRepository::class
)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $tid;

    #[ORM\Column]
    private ?string $text;

    #[ORM\Column(type: 'json')]
    private ?array $keyboards;

    #[ORM\Column]
    private ?string $previewLink;

    #[ORM\Column]
    private ?int $viewsCount;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }

    /**
     * Create from telegram DTO.
     * @param TelegramMessage $telegramMessage
     * @return static
     */
    public static function createFromTelegramMessage(TelegramMessage $telegramMessage): self
    {
        $message = new self();
        $message->setTid($telegramMessage->id);
        $message->setText($telegramMessage->text);
        $message->setKeyboards($telegramMessage->keyboards);
        $message->setPreviewLink($telegramMessage->previewLink);
        $message->setViewsCount($telegramMessage->viewsCount);
        $message->setPublishedAt($telegramMessage->publishedAt);

        return $message;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getTid(): ?int
    {
        return $this->tid;
    }

    /**
     * @param int|null $tid
     * @return Message
     */
    public function setTid(?int $tid): Message
    {
        $this->tid = $tid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return Message
     */
    public function setText(?string $text): Message
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getKeyboards(): ?array
    {
        return $this->keyboards;
    }

    /**
     * @param array|null $keyboards
     * @return Message
     */
    public function setKeyboards(?array $keyboards): Message
    {
        $this->keyboards = $keyboards;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPreviewLink(): ?string
    {
        return $this->previewLink;
    }

    /**
     * @param string|null $previewLink
     * @return Message
     */
    public function setPreviewLink(?string $previewLink): Message
    {
        $this->previewLink = $previewLink;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    /**
     * @param int|null $viewsCount
     * @return Message
     */
    public function setViewsCount(?int $viewsCount): Message
    {
        $this->viewsCount = $viewsCount;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTimeImmutable|null $publishedAt
     * @return Message
     */
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): Message
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable|null $createdAt
     * @return Message
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): Message
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $updatedAt
     * @return Message
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): Message
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}