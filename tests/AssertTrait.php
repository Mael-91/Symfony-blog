<?php

namespace App\Tests;

use Symfony\Component\Validator\ConstraintViolation;

trait AssertTrait {

    private function assertHasError(object $entity, int $number = 0) {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $message = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $message[] = $error->getPropertyPath() . '-> ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $message));
    }
}