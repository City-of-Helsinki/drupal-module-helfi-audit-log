<?php

namespace Drupal\helfi_audit_log\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event class for audit log use.
 *
 * This event allows other modules to control what will
 * will be written in the audit log or invalidate the event
 * to prevent it from ending up in the log.
 *
 * @see: \Drupal\helfi_audit_log\EventSubscriber\AuditLogEventSubscriber
 */
class AuditLogEvent extends Event {

  /**
   * The name of the audit log events.
   *
   * @var string
   */
  const LOG = 'helfi_audit_log.audit_log_event';

  /**
   * Construct a new event object.
   */
  public function __construct(array $message, string $origin = 'DRUPAL') {
    $this->message = $message;
    $this->origin = $origin;
    $this->isValid = TRUE;
  }

  /**
   * Factory method for creating AuditLog events in sinlge line.
   *
   * @param array $message
   *   Message for audit log.
   */
  public static function create(array $message) {
    $event = new AuditLogEvent($message);
    $event_dispatcher = \Drupal::service('event_dispatcher');
    $event_dispatcher->dispatch($event, AuditLogEvent::LOG);
  }

  /**
   * Get message data.
   */
  public function getMessage(): array {
    return $this->message;
  }

  /**
   * Set new message data.
   */
  public function setMessage(array $message): void {
    $this->message = $message;
  }

  /**
   * Get origin.
   */
  public function getOrigin(): string {
    return $this->origin;
  }

  /**
   * Set origin.
   *
   * @param string $origin
   *   New origin.
   */
  public function setOrigin(string $origin): void {
    $this->origin = $origin;
  }

  /**
   * Check if the event is valid.
   */
  public function isValid(): bool {
    return $this->isValid;
  }

  /**
   * Set event validity.
   *
   * @param bool $validity
   *   New value for validity.
   */
  public function setValid(bool $validity): void {
    $this->isValid = $validity;
  }

}
