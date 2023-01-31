<?php

namespace PageAnalyzer;

use Valitron\Validator;

class UrlValidator
{
    public function validate(array $url)
    {
        $errors = [];
        $v = new Validator($url);
        // Проверка на пустое значение
        $v->rule('required', 'name');
        if (!$v->validate()) {
             return $errors = ['name' => 'URL не должен быть пустым'];
        }
        // Проверка на корректность url и макс. длину 255 символов
        $v->rule('url', 'name')->rule('lengthMax', 'name', 255);
        if (!$v->validate()) {
            return $errors = ['name' => 'Некорректный URL'];
        }

        return $errors;
    }
}
