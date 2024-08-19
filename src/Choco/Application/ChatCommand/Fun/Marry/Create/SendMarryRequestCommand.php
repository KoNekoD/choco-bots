<?php

declare(strict_types=1);

namespace App\Choco\Application\ChatCommand\Fun\Marry\Create;

use App\Choco\Application\ChatCommandContracts\AbstractChocoChatCommand;
use App\Main\Domain\Exception\ChatClientAPI\ChatClientAPIException;

final class SendMarryRequestCommand
    extends AbstractChocoChatCommand
{
    /**
     * (?!ABC) - Negative lookahead.
     * Specifies a group that can not match after the main expression
     * (if it matches, the result is discarded).
     */
    public static function getChatCommandPattern(): string
    {
        return '/^(Брак)\s((?!да|нет).*)/';
    }

    public static function getChatCommandExample(): string
    {
        return 'Брак {ссылка}';
    }

    public static function getChatCommandDescription(): string
    {
        return 'Отправит предложение на создание брака';
    }

    /** @return string[] */
    public function getTargetUsernameList(): array
    {
        $args = $this->getCommandArguments();

        $usernames = explode(' ', $args[2]);

        $trimmedUsernames = [];
        foreach ($usernames as $username) {
            try {
                $trimmedUsernames[] = $this
                    ->chocoData
                    ->client
                    ->trimUsername($username);
            } catch (ChatClientAPIException) {
            }
        }

        return $trimmedUsernames;
    }

    /** @return string[] */
    public function getTargetUsernameListRaw(): array
    {
        return explode(' ', $this->getCommandArguments()[2]);
    }
}
