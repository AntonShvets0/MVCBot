<?php

/**
 * @class BotKeyboard
 * Нужен для создания клавиатуры
 */

class BotKeyboard
{
    private $line = 0, $button = 0, $oneTime = false, $result = [];

    /**
     * @return $this
     * Добавляет новую строку
     */
    public function AddLine()
    {
        if ($this->line >= 10) {
            throw new Error('Max size keyboard 4x10');
        }
        $this->line++;
        $this->button = 0;
        return $this;
    }

    /**
     * @param $text
     * @param string $color
     * @param array $payLoad
     * Добавляет кнопку
     */
    public function AddButton($text, $color = 'secondary', $payLoad = [])
    {

        if (count($this->result[$this->line]) >= 4) {
            throw new Error('Max size keyboard 4x10');
        }

        $this->result[$this->line][$this->button] = ['color' => $color, 'action' => ['type' => 'text', 'label' => $text, 'payload' => json_encode($payLoad, JSON_UNESCAPED_UNICODE)]];
        $this->button++;
    }

    /**
     * @param array $payLoad
     * Добавляет кнопку, которая отправляет местоположение
     */
    public function AddLocationButton($payLoad = [])
    {
        $this->AddLine();
        $this->result[$this->line][] = ['action' => ['type' => 'location', 'payload' => json_encode($payLoad, JSON_UNESCAPED_UNICODE)]];
    }

    /**
     * @param string $hash
     * @param array $payLoad
     * Добавляет кнопку оплаты
     */
    public function AddPayButton($hash, $payLoad = [])
    {
        $this->AddLine();
        $this->result[$this->line][] = ['action' => ['type' => 'vkpay', 'hash' => $hash, 'payload' => json_encode($payLoad, JSON_UNESCAPED_UNICODE)]];
    }

    /**
     * @param string $text
     * @param int $app_id
     * @param string $hash
     * @param string|int $owner_id
     * Добавляет кнопку открытия приложения
     */
    public function AddAppButton($text, $app_id, $hash = '', $owner_id = 'callback')
    {
        $this->AddLine();

        if ($owner_id == 'callback') {
            $owner_id = -GROUP;
        }

        $this->result[$this->line][] = ['action' => ['type' => 'open_app', 'app_id' => $app_id, 'owner_id' => $owner_id, 'hash' => $hash, 'label' => $text]];
    }

    /**
     * @param bool $bool
     * Скрывать ли клавиатуру после нажатия на кнопку
     */
    public function Hide($bool = true)
    {
        $this->oneTime = $bool;
    }

    /**
     * @return false|string
     */
    public function Build()
    {
        $result = ['one_time' => $this->oneTime, 'buttons' => $this->result];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}