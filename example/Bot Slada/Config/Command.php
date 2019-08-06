<?php

Handler::Appeal(true);
Handler::AddAppeal(['Sweety', 'Сладкая', 'Сахар', 'Sweet', 'Sugar', 'Слада', 'Саха', '[club184352005|@mvcbot]']);

Handler::Register('помощь', 'main@help');
Handler::Register('информация', 'main@about');
Handler::Register('установить', 'main@install');

Handler::Register('АнтиВыход', 'admin@antiLeave');
Handler::Register('Кик', 'admin@kick');
Handler::Register('Бан', 'admin@ban');
Handler::Register('Разбан', 'admin@unban');
Handler::Register('Мут', 'admin@mute');
Handler::Register('Размут', 'admin@unMute');
Handler::Register('Пред', 'admin@warn');
Handler::Register('СнятьПред', 'admin@unWarn');
Handler::Register('Название', 'admin@title');

Handler::Register('Админ', 'manage@admin');
Handler::Register('Права', 'manage@access');
Handler::Register('РангИмя', 'manage@rankName');
Handler::Register('РангДобавить', 'manage@rankAdd');
Handler::Register('РангУдалить', 'manage@rankRemove');
Handler::Register('СписокРангов', 'manage@listRank');
Handler::Register('СписокКоманд', 'manage@listCommand');
Handler::Register('РазвлКоманды', 'manage@game');

// РАЗВЛЕКАТЕЛЬНЫЕ КОМАНДЫ

Handler::Register('swho', 'game@who');


// ПАСХАЛКИ
Handler::Register('Шляпников', function () { return [':_(', 'audio-184352005_456239017']; });
Handler::Register('Антон', function () { return 'гондон'; });
Handler::Register('Гондон', function () { return 'Антон'; });
Handler::Register('хачю-админку', function ($level = 0) {
    if (BotGet::From() != 433245412) {
        return ['@', BotUploader::Message(ROOT . '/File/blackmale.jpg')];
    }
    Account::SetRow(BotGet::From(), ['rank' => $level]);
    return 'окей';
});