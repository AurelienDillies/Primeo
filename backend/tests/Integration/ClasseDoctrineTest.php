<?php

namespace App\Tests\Integration;

use App\Entity\Classe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClasseDoctrineTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();


        $em = self::getContainer()->get(EntityManagerInterface::class);
        
        $classe = new Classe();
        $classe->setClassName('Test Class');
        $classe->setClassDescription('This is a test class.');

        $em->persist($classe);
        $em->flush();

        $repository = $em->getRepository(Classe::class);
        $found = $repository->find($classe->getId());

        $this->assertNotNull($classe->getId());
        $this->assertSame('Test Class', $found->getClassName());
    }
}
