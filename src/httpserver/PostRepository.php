<?php

namespace Passh\Rx\httpserver;


interface PostRepository
{

    public function findAll() : array;
}