<?php

namespace Nimblephp\migrations;

/**
 * Migration status
 */
enum Status: string
{

    case PENDING = 'pending';

    case FINISHED = 'finished';

    case FAILED = 'failed';

}
