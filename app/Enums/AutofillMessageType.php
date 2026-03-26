<?php

namespace App\Enums;

enum AutofillMessageType: string
{
    case Text = 'text';
    case Selection = 'selection';
    case Progress = 'progress';
    case Images = 'images';
    case Recap = 'recap';
    case Error = 'error';
}
