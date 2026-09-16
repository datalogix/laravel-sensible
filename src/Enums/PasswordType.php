<?php

namespace Datalogix\Sensible\Enums;

/**
 * Other types could be added in the future, such as:
 * - Custom: user-provided closure/rules via configuration.
 */
enum PasswordType: string
{
    case Simple = 'simple';
    case Numeric = 'numeric';
    case Pin = 'pin';
    case Passphrase = 'passphrase';
    case Alphanumeric = 'alphanumeric';
    case Complex = 'complex';
}
