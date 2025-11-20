<?php

/**
 * Valida CNPJ
 */
function validarCnpj($cnpj) {
    $cnpj = preg_replace('/\D/', '', $cnpj); // remove tudo que não é número

    if (strlen($cnpj) != 14) return false;
    if (preg_match('/(\d)\1{13}/', $cnpj)) return false; // todos os dígitos iguais

    $t = strlen($cnpj) - 2;
    $d = substr($cnpj, -2);

    $calc = function($cnpj, $t) {
        $soma = 0;
        $pos = $t - 7;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cnpj[$i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resto = $soma % 11;
        return ($resto < 2) ? 0 : 11 - $resto;
    };

    return ($calc($cnpj, 12) == $d[0] && $calc($cnpj, 13) == $d[1]);
}

/**
 * Valida CPF
 */
function validarCpf($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf); // remove tudo que não é número

    if (strlen($cpf) != 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false; // todos os dígitos iguais

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $resto = ($soma * 10) % 11;
        if ($resto == 10) $resto = 0;
        if ($cpf[$t] != $resto) return false;
    }

    return true;
}
