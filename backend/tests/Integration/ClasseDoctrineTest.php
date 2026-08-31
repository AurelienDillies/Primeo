<?php

namespace App\Tests\Integration;

use App\Entity\Classe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClasseDoctrineTest extends KernelTestCase
{
    public function testDoctrinePersistsAndLoadsClasse(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $classe = new Classe();
        $classe->setClassName('Classe de test');
        $classe->setClassDescription('Description de test pour l’intégration Doctrine.');

        $entityManager->persist($classe);
        $entityManager->flush();

        $found = $entityManager->getRepository(Classe::class)->find($classe->getId());

        $this->assertNotNull($classe->getId());
        $this->assertNotNull($found);
        $this->assertSame('Classe de test', $found->getClassName());
        $this->assertSame('Description de test pour l’intégration Doctrine.', $found->getClassDescription());
    }
}
