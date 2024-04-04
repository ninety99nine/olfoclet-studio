<?php

namespace App\Enums;

enum MessageFailureType:string {
    case InternalFailure = 'Internal Failure';
    case MessageSendingFailed = 'Message Sending Failed';
    case TokenGenerationFailed = 'Token Generation Failed';
}
