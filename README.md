# Audit logger module for helfi Drupals

## Overview
This module provides Helsinki centric functionality to produce audit log to its own database table. This module does not create database entries on its own. You need to use another module or implement your own to get actual audit log entries.

Module uses Drupal's event system. That way multiple module can react to single audit log event in a controlled way.

## Usage

### Sending events
Simplest way to add audit log entries is to call dispatchEvent method of the module's service class `\Drupal::service('helfi_audit_log.audit_log')->dispatchEvent($message);`

Recommended usage is to use dependecy injection in your own code. Example code is provided below.

```yaml
# my_module.services.yml
my_module.my_service:
    class: Drupal\my_module\MyService
    arguments: ['@helfi_audit_log.audit_log']
```

```php
<?php

namespace Drupal\my_module;

use Drupal\helfi_audit_log\AuditLogServiceInterface;

class MyService {

    __construct(AuditLogServiceInterface $auditLogService) {
        $this->auditLogService = $auditLogService;
    }

    public function myMethod() {
        /*
         * Module logic here
         */
        $message = [
            "operation" => "MY_MODULE_EVENT",
            "status" => "SUCCESS",
            "target" => [
                "id" => "MY_MODULE_ENTITY_ID",
                "type" => "MY_MODULE_ENTITY",
                "name" => "MY_MODULE_ENTITY_NAME",
            ],
        ];
        $this->auditLogService->dispatchEvent($message);
    }
}
```

### Reacting to events

You can react to audit log events by implementing an event subscriber. Weight value decided in which order subscribers reacto to the events.

```php
<?php

namespace Drupal\my_module\EventSubscriber;

use Drupal\helfi_audit_log\AuditLogServiceInterface;
use Drupal\helfi_audit_log\Event\AuditLogEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * My module Event Subscriber for audit log events.
 */
class MyModuleAuditLogEventSubscriber implements EventSubscriberInterface {

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        $events[AuditLogEvent::LOG][] = ['myModuleReact', 0];
        return $events;
    }

    public function myModuleReact(AuditLogEvent $event) {
        // React to event
    }
}
```