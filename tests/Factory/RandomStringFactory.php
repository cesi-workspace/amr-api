<?php
namespace App\Tests\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class RandomStringFactory
{
    function generatePassword($length = 16) {
    $characterSets = [
        'abcdefghijklmnopqrstuvwxyz',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '0123456789',
        '!@#$%^&*()_-+=<>?'
    ];

    $password = '';

    foreach ($characterSets as $set) {
        $password .= $set[random_int(0, strlen($set) - 1)];
    }

    while (strlen($password) < $length) {
        $randomSet = $characterSets[random_int(0, count($characterSets) - 1)];
        $password .= $randomSet[random_int(0, strlen($randomSet) - 1)];
    }

    return str_shuffle($password); // Shuffle the characters for extra randomness
}
}
