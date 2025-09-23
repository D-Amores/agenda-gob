<?php

namespace App\Http\Tools;

class Tools
{
    public static function formatDate($date)
    {
        return $date->format('d/m/Y');
    }

    public static function generateSecurePassword($length = 12)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '@$!%*?&';
        
        // Asegurar que tenga al menos uno de cada tipo
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Llenar el resto con caracteres aleatorios
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }
}
