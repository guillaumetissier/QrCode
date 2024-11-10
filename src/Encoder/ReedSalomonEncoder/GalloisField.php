<?php

namespace ThePhpGuild\Qrcode\Encoder\ReedSalomonEncoder;

class GalloisField
{
    private array $exp = [];
    private array $log = [];
    private int $primitivePolynomial = 0x11d;

    public function __construct() {
        $x = 1;
        for ($i = 0; $i < 256; $i++) {
            $this->exp[$i] = $x;
            $this->log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= $this->primitivePolynomial;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            $this->exp[$i] = $this->exp[$i - 255];
        }
    }

    public function getExp(int $i): int
    {
        return $this->exp[$i];
    }

    public function multiply($a, $b)
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }
        $logSum = ($this->log[$a] + $this->log[$b]) % 255;
        return $this->exp[$logSum];
    }

    public function divide($a, $b)
    {
        if ($b === 0) {
            throw new \InvalidArgumentException("Division par zéro dans le corps de Galois");
        }
        if ($a === 0) {
            return 0;
        }
        $logDiff = ($this->log[$a] - $this->log[$b] + 255) % 255;
        return $this->exp[$logDiff];
    }
}