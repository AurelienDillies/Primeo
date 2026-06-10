<?php

namespace App\Tests\Unit;

use App\Entity\Classe;
use PHPUnit\Framework\TestCase;

class ClasseTest extends TestCase
{
    public function testCreateClasse(): void
    {
        $classe = new Classe();
        $classe->setClassName('Test Class');
        $classe->setClassDescription('This is a test class.');
        $this->assertEquals('Test Class', $classe->getClassName());
        $this->assertEquals('This is a test class.', $classe->getClassDescription());
    }
}
