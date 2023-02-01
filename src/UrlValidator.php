<?php

namespace PageAnalyzer;

use Valitron\Validator;

class UrlValidator
{
    public function validate(mixed $url): mixed
    {
        $errors = [];
        $v = new Validator($url);
        // Проверка на пустое значение
        if (!$v->rule('required', 'name')->validate()) {
             $errors = ['name' => 'URL не должен быть пустым'];
        } elseif (!$v->rule('url', 'name')->rule('lengthMax', 'name', 255)->validate()) {
            $errors = ['name' => 'Некорректный URL'];
        } else {
            $errors = [];
        }

        return $errors;
    }
}
