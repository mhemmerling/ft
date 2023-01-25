<?php

declare(strict_types=1);

namespace App\Message\Application;

use App\Message\Command\ChangeMessageStatus;
use App\Message\Command\CreateMessage;
use App\Message\Command\DeleteMessage;
use App\Message\Command\EditMessage;

interface MessageServiceInterface
{
    public function send(CreateMessage $command): void;

    public function edit(EditMessage $command): void;

    public function deleteRecipient(DeleteMessage $command): void;

    public function delete(DeleteMessage $command): void;

    public function setReadStatus(ChangeMessageStatus $command): void;

    public function listUserMessages(int $userId): array;
}
