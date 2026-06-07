<?php

namespace pascualmg\cohete\ddd\Application\Post;

/** El token Bearer no corresponde a ningun autor. El controller lo mapea a 403. */
class InvalidBearerException extends \RuntimeException
{
}
