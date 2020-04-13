<?php

namespace App\Service;

class AvatarGeneratorService {

    public function generateColor(): array {
        foreach (['r', 'g', 'b'] as $color) {
            $rgb[$color] = mt_rand(0, 255);
        }
        return $rgb;
    }
}