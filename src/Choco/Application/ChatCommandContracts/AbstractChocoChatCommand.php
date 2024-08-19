<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommandContracts;

use App\Choco\Application\ChatCommandDTO\ChocoCommandDataStructure;
use App\Choco\Domain\DTO\RequiredChatConfigurationDTO;
use App\Choco\Domain\Exception\RequiredConfigurationException;
use App\Main\Application\ChatCommandContracts\AbstractChatCommand;
use App\Main\Domain\Enum\UpdateChatTypeEnum;

abstract class AbstractChocoChatCommand
    extends AbstractChatCommand
{
    public AllowedChatTypeEnum $allowedChatType = AllowedChatTypeEnum::ALL;

    // @phpstan-ignore-next-line
    public readonly ChocoCommandDataStructure $chocoData;
    public bool $requiredReplyToMessage = false;

    public function loadInitialChocoConfiguration(
        ChocoCommandDataStructure $dataStructure
    ): void {
        $this->chocoData = $dataStructure; // @phpstan-ignore-line
    }

    public function checkIfNeededReplyToMessage(): bool
    {
        // Not required = just return ok
        // Required = Check if exist ReplyToMessage
        // Required, but ReplyToMessage missed
        return !$this->requiredReplyToMessage
            || $this->chocoData->getUpdateMessageReplyToMessageOrNull();
    }

    /** @throws RequiredConfigurationException */
    public function checkRequiredConfiguration(): void
    {
        $config = $this->getRequiredChatConfiguration();
        if (null === $config) {
            return;
        }

        if (
            $config->muteEnabled
            && !$this->chocoData->chat->getConfiguration()->isMuteEnabled()
        ) {
            throw new RequiredConfigurationException('RequiredEnabledMute');
        }
    }

    protected function getRequiredChatConfiguration(
    ): ?RequiredChatConfigurationDTO
    {
        return null;
    }

    public function checkCommandChatTypeRule(): bool
    {
        if (AllowedChatTypeEnum::ALL === $this->allowedChatType) {
            return true;
        }

        if (
            AllowedChatTypeEnum::CHAT === $this->allowedChatType
            && UpdateChatTypeEnum::GROUP === $this
                ->chocoData->getUpdateChat()->getType()
        ) {
            return true;
        }

        return AllowedChatTypeEnum::PM === $this->allowedChatType
            && UpdateChatTypeEnum::PRIVATE === $this
                ->chocoData->getUpdateChat()->getType();
    }
}
