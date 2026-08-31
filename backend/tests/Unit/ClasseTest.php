<?php

namespace App\Tests\Unit;

use App\Entity\Classe;
use PHPUnit\Framework\TestCase;

class ClasseTest extends TestCase
{
    public function testCreateClasse(): void
    {
        $classe = new Classe();
        $classe->setClassName('Classe de test');
        $classe->setClassDescription('Description de test.');

        $this->assertSame('Classe de test', $classe->getClassName());
        $this->assertSame('Description de test.', $classe->getClassDescription());
        $this->assertNull($classe->getTeacher());
        $this->assertCount(0, $classe->getStudents());
        $this->assertCount(0, $classe->getCourses());
    }
}
