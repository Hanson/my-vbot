<?php

namespace Hanson\MyVbot;

use Hanson\MyVbot\Handlers\Contact\ColleagueGroup;
use Hanson\MyVbot\Handlers\Contact\ExperienceGroup;
use Hanson\MyVbot\Handlers\Contact\FeedbackGroup;
use Hanson\MyVbot\Handlers\Contact\Hanson;
use Hanson\MyVbot\Handlers\Type\RecallType;
use Hanson\MyVbot\Handlers\Type\TextType;
use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;

use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class MessageHandler
{
    public static function messageHandler(Collection $message)
    {
        /** @var Friends $friends */
        $friends = vbot('friends');

        /** @var Members $members */
        $members = vbot('members');

        /** @var Groups $groups */
        $groups = vbot('groups');

        Hanson::messageHandler($message, $friends, $groups);
        ColleagueGroup::messageHandler($message, $friends, $groups);
        FeedbackGroup::messageHandler($message, $friends, $groups);
        ExperienceGroup::messageHandler($message, $friends, $groups);

        TextType::messageHandler($message, $friends, $groups);
        RecallType::messageHandler($message);

        if ($message['type'] === 'new_friend') {
            Text::send($message['from']['UserName'], '客官，等你很久了！感谢跟 vbot 交朋友，如果可以帮我点个star，谢谢了！https://github.com/HanSon/vbot');
            $groups->addMember($groups->getUsernameByNickname('Vbot 体验群'), $message['from']['UserName']);
            Text::send($message['from']['UserName'], '现在拉你进去vbot的测试群，进去后为了避免轰炸记得设置免骚扰哦！如果被不小心踢出群，跟我说声“拉我”我就会拉你进群的了。');
        }

        if ($message['type'] === 'emoticon' && random_int(0, 1)) {
            Emoticon::sendRandom($message['from']['UserName']);
        }

        // @todo
        if ($message['type'] === 'official') {
            vbot('console')->log('收到公众号消息:'.$message['title'].$message['description'].
                $message['app'].$message['url']);
        }

        if ($message['type'] === 'request_friend') {
            vbot('console')->log('收到好友申请:'.$message['info']['Content'].$message['avatar']);
            if (in_array($message['info']['Content'], ['echo', 'print_r', 'var_dump', 'print'])) {
                $friends->approve($message);
            }
        }
    }
}
