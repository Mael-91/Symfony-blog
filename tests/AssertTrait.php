<?php

namespace App\Tests;

use Symfony\Component\Validator\ConstraintViolation;

trait AssertTrait {

    public function assertValidatorErrors(object $entity, int $number = 0) {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $message = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $message[] = $error->getPropertyPath() . '-> ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $message));
    }

    /**
     * Permet de faire une assertion sur une méthode count pour un répository
     * @param string $method
     * @param int $number
     * @param string $fixture
     * @param string $repository
     */
    public function assertEqualsMethodRepo(string $method, int $number, string $fixture, string $repository) {
        self::bootKernel();
        $this->loadFixtureFiles([
            __DIR__ . "/fixtures/$fixture.yaml"
        ]);
        $repo = self::$container->get($repository)->$method();
        $this->assertEquals($number, $repo);
    }

    /**
     * Permet de faire une assertion pour une méthode find pour un repository
     * @param string $method
     * @param int $number
     * @param string $fixture
     * @param string $repository
     */
    public function assertCountMethodRepo(string $method, int $number, string $fixture, string $repository) {
        self::bootKernel();
        $this->loadFixtureFiles([
            __DIR__ . "/fixtures/$fixture.yaml"
        ]);
        $repo = self::$container->get($repository)->$method();
        $this->assertCount($number, $repo);
    }
}