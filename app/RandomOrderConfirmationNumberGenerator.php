<?php

namespace App;

class RandomOrderConfirmationNumberGenerator
{
    public function generate()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        return substr(str_shuffle(str_repeat($characters, 24)), 0, 24);
    }
}
