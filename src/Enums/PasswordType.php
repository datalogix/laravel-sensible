<?php

namespace Datalogix\Sensible\Enums;

enum PasswordType: string
{
    case Simple = 'simple';
    case Numeric = 'numeric';
    case Pin = 'pin';
    case Passphrase = 'passphrase';
    case Alphanumeric = 'alphanumeric';
    case Complex = 'complex';
}
