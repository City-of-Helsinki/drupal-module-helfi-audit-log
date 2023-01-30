<?php

namespace Drupal\helfi_audit_log\EventSubscriber;

use Drupal\helfi_audit_log\AuditLogServiceInterface;
use Drupal\helfi_audit_log\Event\AuditLogEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber for audit log events.
 *
 * For valid events this subscriber calls related service which
 * handles database writing. For invalid message Drupal log entry is generated.
 */
class AuditLogEventSubscriber implements EventSubscriberInterface {

  /**
   * AuditLogService.
   *
   * @var Drupal\helfi_audit_log\AuditLogServiceInterface
   */
  protected AuditLogServiceInterface $auditLogService;

  /**
   * Construct new AuditLogEventSubscriber.
   *
   * @param \Drupal\helfi_audit_log\AuditLogServiceInterface $auditLogService
   *   Service that handles writing to the log.
   */
  public function __construct(AuditLogServiceInterface $auditLogService) {
    $this->auditLogService = $auditLogService;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[AuditLogEvent::LOG][] = ['writeToDatabase', -100];
    return $events;
  }

  /**
   * Write log message to database.
   *
   * This method is called whenever the AuditEvent::LOG event is
   * dispatched.
   *
   * @param \Drupal\helfi_audit_log\Event\AuditLogEvent $event
   *   Event to handle.
   */
  public function writeToDatabase(AuditLogEvent $event) {
    if (!$event->isValid()) {
      \Drupal::logger('helfi_audit_log')
        ->error(t('Audit log message validation failed.'));
      return;
    }
    $this->auditLogService->logOperation($event->getMessage(), $event->getOrigin());
  }

}
