<?php

namespace pascualmg\cohete\ddd\Domain\Entity;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use React\Promise\PromiseInterface;

interface AuthorRepository
{
    public function findAll(): PromiseInterface; //of Author[]

    public function findById(AuthorId $id): PromiseInterface; //of Author|null

    public function findByName(AuthorName $name): PromiseInterface; //of Author|null

    public function save(Author $author): PromiseInterface; //of bool
}
