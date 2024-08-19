<?php

declare(strict_types=1);

namespace App\Choco\Domain\ChatConfiguration;

use App\Choco\Domain\Enum\ChatMemberRankStatusEnum;

final class ChatConfigurationRights
{
    // More than SeniorAdministrator can use this
    final public const CAN_MANAGE_CHAT_CONFIGURATION =
        ChatMemberRankStatusEnum::SeniorAdministrator;

    // More than JuniorModerator can use this
    final public const CAN_MUTE = ChatMemberRankStatusEnum::JuniorModerator;
}
